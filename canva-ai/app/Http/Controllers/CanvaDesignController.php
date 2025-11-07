<?php
// app/Http/Controllers/CanvaDesignController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\Design;

class CanvaDesignController extends Controller
{
    // Get access token for frontend
    public function getToken(Request $request)
    {
        // Verify request is from your frontend
        // $origin = $request->header('Origin');
        // if ($origin !== env('FRONTEND_URL')) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

        $tokens = Session::get('canva_token');
        if (!$tokens) {
            return response()->json(['error' => 'No token found'], 401);
        }

        return response()->json([
            'access_token' => $tokens['access_token']
        ]);
    }

    // Create a new design in Canva
    public function createDesign(Request $request)
    {
        $request->validate([
            'asset_type' => 'required|string',
            'title' => 'nullable|string'
        ]);

        $tokens = Session::get('canva_token');
        
        $response = Http::withToken($tokens['access_token'])
            ->post('https://api.canva.com/rest/v1/designs', [
                'asset_type' => $request->asset_type,
                'title' => $request->title ?? 'New Design'
            ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to create design'], 400);
        }

        $designData = $response->json();
        
        // Store design in database
        $design = Design::create([
            'canva_design_id' => $designData['design']['id'],
            'title' => $request->title ?? 'New Design',
            'asset_type' => $request->asset_type,
            'edit_url' => $designData['design']['urls']['edit_url'],
        ]);

        return response()->json($design);
    }

    // Export design
    public function exportDesign(Request $request)
    {
        $request->validate([
            'design_id' => 'required|string',
            'format' => 'required|string|in:png,jpg,pdf'
        ]);

        $tokens = Session::get('canva_token');
        
        $response = Http::withToken($tokens['access_token'])
            ->post("https://api.canva.com/rest/v1/designs/{$request->design_id}/export", [
                'format' => $request->format
            ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to export design'], 400);
        }

        return response()->json($response->json());
    }

    // Download exported design to server
    public function downloadExport(Request $request)
    {
        $request->validate([
            'export_url' => 'required|url',
            'design_id' => 'required|integer'
        ]);

        $design = Design::findOrFail($request->design_id);

        try {
            // Download the file from Canva's export URL
            $fileContents = file_get_contents($request->export_url);
            
            if ($fileContents === false) {
                return response()->json(['error' => 'Failed to download file'], 500);
            }

            // Generate filename
            $fileName = 'design_' . $design->id . '_' . time() . '.png';
            $path = 'designs/' . $fileName;

            // Store in Laravel storage
            Storage::disk('public')->put($path, $fileContents);

            // Update design record
            $design->update([
                'export_url' => Storage::url($path),
                'local_path' => $path
            ]);

            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'design' => $design
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Get design details
    public function getDesign($id)
    {
        $design = Design::findOrFail($id);
        return response()->json($design);
    }
}
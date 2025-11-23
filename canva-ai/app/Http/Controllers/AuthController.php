<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'User created'], 201);
    }

    public function login(Request $request)
{
    try {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        // Generate token
        $token = $user->createToken('app')->plainTextToken;

        // Save user ID in session (optional)

        return response()->json([
            'success' => true,
            'token' => $token
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'error' => 'Validation failed',
            'details' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
            'message' => $e->getMessage()
        ], 500);
    }
}


    // placeholder design APIs
    public function getDesigns()
    {
        return [
            ['id' => 1, 'name' => 'Sample Design 1'],
            ['id' => 2, 'name' => 'Sample Design 2']
        ];
    }

    public function createDesign(Request $request)
    {
        return [
            'message' => 'Design created successfully',
            'data' => $request->all()
        ];
    }
}

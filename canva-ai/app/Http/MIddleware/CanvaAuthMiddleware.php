<?php
// app/Http/Middleware/CanvaAuthMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CanvaAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('canva_token')) {
            return response()->json(['error' => 'Not authenticated with Canva'], 401);
        }
        
        return $next($request);
    }
}
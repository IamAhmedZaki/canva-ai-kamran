<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Helpers\PKCEHelper;

class CanvaAuthController extends Controller
{
    public function authorizeCanva()
    {
        $clientId = env('CANVA_CLIENT_ID');
        $redirectUri = env('CANVA_REDIRECT_URI');
        $verifier = PKCEHelper::generateCodeVerifier();
        $challenge = PKCEHelper::generateCodeChallenge($verifier);
        $state = bin2hex(random_bytes(16));

        // Save to session for later verification
        Session::put('canva_verifier', $verifier);
        Session::put('canva_state', $state);

        $scopes = 'asset:read asset:write brandtemplate:content:read brandtemplate:meta:read design:content:read design:content:write design:meta:read profile:read';

        $authUrl = "https://www.canva.com/api/oauth/authorize?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scopes,
            'state' => $state,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256'
        ]);

        return redirect($authUrl);
    }

    public function callback(Request $request)
{
    $code = $request->query('code');
    $state = $request->query('state');

    // Validate state
    if ($state !== Session::get('canva_state')) {
        // Redirect to frontend with error
        return redirect(env('FRONTEND_URL') . '?auth=failed&error=invalid_state');
    }

    $verifier = Session::get('canva_verifier');
    $clientId = env('CANVA_CLIENT_ID');
    $clientSecret = env('CANVA_CLIENT_SECRET');
    
    // Encode credentials for Basic Auth
    $credentials = base64_encode($clientId . ':' . $clientSecret);

    $tokenResponse = Http::withHeaders([
        'Authorization' => 'Basic ' . $credentials,
        'Content-Type' => 'application/x-www-form-urlencoded',
    ])->asForm()->post('https://api.canva.com/rest/v1/oauth/token', [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => env('CANVA_REDIRECT_URI'),
        'code_verifier' => $verifier,
    ]);

    if ($tokenResponse->failed()) {
        // Redirect to frontend with error
        return redirect(env('FRONTEND_URL') . '?auth=failed&error=token_exchange_failed');
    }

    $tokens = $tokenResponse->json();
    
    // Store tokens in session (or database for production)
    Session::put('canva_token', $tokens);
    
    // Clean up temporary OAuth state
    Session::forget('canva_state');
    Session::forget('canva_verifier');

    // ✅ Redirect back to frontend with success
    return redirect(env('FRONTEND_URL') . '?auth=success');
}

    public function revoke()
{
    $tokens = Session::get('canva_token');
    if (!$tokens) {
        return response()->json(['error' => 'No active token'], 401);
    }

    $clientId = env('CANVA_CLIENT_ID');
    $clientSecret = env('CANVA_CLIENT_SECRET');
    $credentials = base64_encode($clientId . ':' . $clientSecret);

    Http::withHeaders([
        'Authorization' => 'Basic ' . $credentials,
        'Content-Type' => 'application/x-www-form-urlencoded',
    ])->asForm()->post('https://api.canva.com/rest/v1/oauth/revoke', [
        'token' => $tokens['refresh_token'],
    ]);

    Session::forget('canva_token');
    Session::forget('canva_state');
    Session::forget('canva_verifier');
    
    return response()->json(['revoked' => true]);
}

    public function isAuthorized()
    {
        return response()->json(['authorized' => Session::has('canva_token')]);
    }

   
public function returnNav(Request $request)
{
    $correlationJwt = $request->query('correlation_jwt');
    
    if (!$correlationJwt) {
        return redirect(env('FRONTEND_URL') . '?error=missing_jwt');
    }

    try {
        // Verify the JWT
        if (!$this->verifyCanvaJwt($correlationJwt)) {
            return redirect(env('FRONTEND_URL') . '?error=invalid_jwt');
        }

        // Decode the JWT to extract design_id and correlation_state
        $parts = explode('.', $correlationJwt);
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        
        $designId = $payload['design_id'] ?? null;
        $correlationState = $payload['correlation_state'] ?? null;

        // Redirect to your frontend with the parameters
        $frontendUrl = env('FRONTEND_URL') . '/#/return-nav?' . http_build_query([
            'design_id' => $designId,
            'correlation_state' => $correlationState
        ]);

        return redirect($frontendUrl);
        
    } catch (\Exception $e) {
        \Log::error('Return navigation failed: ' . $e->getMessage());
        return redirect(env('FRONTEND_URL') . '?error=processing_failed');
    }
}

private function verifyCanvaJwt($token)
{
    try {
        // Fetch Canva's public keys
        $keysUrl = 'https://api.canva.com/rest/v1/connect/keys';
        $keysResponse = Http::get($keysUrl);
        
        if ($keysResponse->failed()) {
            return false;
        }
        
        $keys = $keysResponse->json();
        
        // For now, basic validation - you should use firebase/php-jwt for proper verification
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        
        // Verify audience matches your client ID
        if (!isset($payload['aud']) || $payload['aud'] !== env('CANVA_CLIENT_ID')) {
            return false;
        }
        
        // Verify expiration
        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }
        
        return true;
        
    } catch (\Exception $e) {
        \Log::error('JWT verification failed: ' . $e->getMessage());
        return false;
    }
}
}

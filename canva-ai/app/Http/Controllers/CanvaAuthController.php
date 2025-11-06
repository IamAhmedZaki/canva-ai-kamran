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
        return response()->json(['error' => 'Invalid state'], 400);
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
        return response()->json([
            'error' => 'Token exchange failed', 
            'details' => $tokenResponse->json(),
            'status' => $tokenResponse->status()
        ], 400);
    }

    $tokens = $tokenResponse->json();
    Session::put('canva_token', $tokens);

    return response()->json(['success' => true, 'tokens' => $tokens]);
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
}

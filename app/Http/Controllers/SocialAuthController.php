<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect($provider)
    {
        if ($provider !== 'google') {
            return response("Only Google OAuth is implemented");
        }
        
        // The exact redirect URI you added in Google Cloud Console
        $redirectUri = url('/social-auth/google/callback');
        
        // Generate a random state to prevent CSRF
        $state = Str::random(40);
        session(['google_auth_state' => $state]);
        
        // Build the authorization URL
        $queryParams = http_build_query([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
            'state' => $state,
        ]);
        
        // Redirect to Google's authorization URL
        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $queryParams);
    }
    
    /**
     * Handle the provider callback.
     *
     * @param string $provider
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback($provider, Request $request)
    {
        if ($provider !== 'google') {
            return response("Only Google OAuth is implemented");
        }
        
        // Verify state to prevent CSRF
        if ($request->state !== session('google_auth_state')) {
            return redirect()->route('login')->with('error', 'Invalid state parameter');
        }
        
        // Check for error
        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'Google authentication failed: ' . $request->error_description);
        }
        
        // The exact redirect URI you added in Google Cloud Console
        $redirectUri = url('/social-auth/google/callback');
        
        try {
            // Exchange code for access token
            $client = new \GuzzleHttp\Client();
            $tokenResponse = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code' => $request->code,
                    'client_id' => env('GOOGLE_CLIENT_ID'),
                    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                    'redirect_uri' => $redirectUri,
                    'grant_type' => 'authorization_code',
                ],
            ]);
            
            $tokenData = json_decode($tokenResponse->getBody(), true);
            
            if (!isset($tokenData['access_token'])) {
                return redirect()->route('login')->with('error', 'Failed to obtain access token');
            }
            
            // Get user info from Google
            $userResponse = $client->get('https://www.googleapis.com/oauth2/v2/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokenData['access_token'],
                ],
            ]);
            
            $userData = json_decode($userResponse->getBody(), true);
            
            if (!isset($userData['email'])) {
                return redirect()->route('login')->with('error', 'Failed to get user email');
            }
            
            // Find or create user
            $user = User::where('email', $userData['email'])->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                ]);
            }
            
            // Log the user in
            Auth::login($user);
            
            return redirect()->intended('dashboard');
            
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }
}

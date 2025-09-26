<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            $redirect = Socialite::driver('google')->redirect();
            return $redirect->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                           ->header('Pragma', 'no-cache')
                           ->header('Expires', '0');
        } catch (\Exception $e) {
            \Log::error('Google OAuth Redirect Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Errore Google OAuth: ' . $e->getMessage());
        }
    }

    /**
     * Handle Google OAuth callback
     */
	public function handleGoogleCallback()
	{
	    try {
	        $googleUser = Socialite::driver('google')->user();
	        
	        // Log Google user data
	        \Log::info('Google user data: ' . json_encode([
	            'id' => $googleUser->getId(),
	            'name' => $googleUser->getName(),
	            'email' => $googleUser->getEmail(),
	        ]));
	        
	        // Check if user exists in our database by email
	        $user = User::where('email', $googleUser->getEmail())->first();
	        
	        // Log database query result
	        \Log::info('Database query result: ' . ($user ? json_encode($user->toArray()) : 'User not found'));
	        
	        if ($user) {
	            // User exists - update provider info to Google
	            $user->update([
	                'provider' => 'google',
	                'provider_id' => $googleUser->getId(),
                	'email_verified_at' => now(),
		        'last_login_at' => now(),
		    ]);
            
	            // Generate Sanctum token
        	    $token = $user->createToken('oauth-token')->plainTextToken;
	            
	            // Log the token for debugging
	            \Log::info('Generated token for user: ' . $user->email . ' (ID: ' . $user->id . ') - Token: ' . $token);
	            
	            // Redirect to dashboard with token parameter
	            $redirectUrl = $user->is_admin ? '/admin' : '/dashboard';
	            $redirectUrl .= '?token=' . $token;
	            
	            return redirect($redirectUrl);
	        } else {
	            return redirect('/login')->with('error', 'Account non trovato. Contatta il tuo amministratore per essere aggiunto alla piattaforma.');
	        }
	        
	    } catch (\Exception $e) {
	        \Log::error('Google OAuth Error: ' . $e->getMessage());
	        return redirect('/login')->with('error', 'Errore durante l\'autenticazione con Google. Riprova.');
	    }
	}
    /**
     * Redirect to Microsoft OAuth
     */
    public function redirectToMicrosoft()
    {
        try {
            return Socialite::driver('microsoft')->redirect();
        } catch (\Exception $e) {
            \Log::error('Microsoft OAuth Redirect Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Errore Microsoft OAuth: ' . $e->getMessage());
        }
    }

    /**
     * Handle Microsoft OAuth callback
     */
    public function handleMicrosoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();
            
            // Check if user exists in our database by email
            $user = User::where('email', $microsoftUser->getEmail())->first();
            
            if ($user) {
                // User exists - update provider info to Microsoft
                $user->update([
                    'provider' => 'microsoft',
                    'provider_id' => $microsoftUser->getId(),
                    'email_verified_at' => now(),
                    'last_login_at' => now(),
                ]);
                
                // Generate Sanctum token
                $token = $user->createToken('oauth-token')->plainTextToken;
                
                // Redirect to dashboard with token parameter
                $redirectUrl = $user->is_admin ? '/admin' : '/dashboard';
                $redirectUrl .= '?token=' . $token;
                
                return redirect($redirectUrl);
            } else {
                return redirect('/login')->with('error', 'Account non trovato. Contatta il tuo amministratore per essere aggiunto alla piattaforma.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Microsoft OAuth Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Errore durante l\'autenticazione con Microsoft. Riprova.');
        }
    }
}

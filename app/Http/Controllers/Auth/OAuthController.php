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
            \Log::info('Google OAuth Callback started');
            
            $googleUser = Socialite::driver('google')->user();
            \Log::info('Google user retrieved: ' . $googleUser->getEmail());
            
            // Check if user exists in our database by email
            $user = User::where('email', $googleUser->getEmail())->first();
            \Log::info('Database user lookup result: ' . ($user ? 'User found (ID: ' . $user->id . ')' : 'User not found'));
            
            if ($user) {
                // User exists - update provider info to Google
                $user->update([
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'last_login_at' => now(),
                ]);
                \Log::info('User updated with Google provider info');
                
                // Generate Sanctum token
                $token = $user->createToken('oauth-token')->plainTextToken;
                \Log::info('Sanctum token generated for user: ' . $user->email . ' (ID: ' . $user->id . ')');
                
                // Redirect to dashboard with token parameter based on user role
                if ($user->is_admin) {
                    $redirectUrl = '/admin';
                } elseif ($user->is_reseller) {
                    $redirectUrl = '/reseller/dashboard';
                } elseif ($user->is_company_manager) {
                    $redirectUrl = '/company/dashboard';
                } else {
                    $redirectUrl = '/dashboard';
                }
                $redirectUrl .= '?token=' . $token;
                \Log::info('Redirecting to: ' . $redirectUrl);
                
                // Force page reload to ensure Vue.js reinitializes
                return redirect($redirectUrl)->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                                           ->header('Pragma', 'no-cache')
                                           ->header('Expires', '0');
            } else {
                // User doesn't exist - show error
                \Log::warning('User not found in database: ' . $googleUser->getEmail());
                return redirect('/login')->with('error', 'Account non trovato. Contatta il tuo amministratore per essere aggiunto alla piattaforma.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            \Log::error('Google OAuth Error Stack: ' . $e->getTraceAsString());
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
                // User doesn't exist - show error
                return redirect('/login')->with('error', 'Account non trovato. Contatta il tuo amministratore per essere aggiunto alla piattaforma.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Microsoft OAuth Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Errore durante l\'autenticazione con Microsoft. Riprova.');
        }
    }

    /**
     * Debug endpoint for testing Google OAuth with Postman
     */
    public function debugGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists in our database by email
            $user = User::where('email', $googleUser->getEmail())->first();
            
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
                
                // Return JSON response for Postman testing
                return response()->json([
                    'success' => true,
                    'message' => 'Google OAuth successful',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_admin' => $user->is_admin,
                        'is_reseller' => $user->is_reseller,
                        'is_company_manager' => $user->is_company_manager,
                        'provider' => $user->provider,
                        'provider_id' => $user->provider_id,
                    ],
                    'token' => $token,
                    'google_user' => [
                        'id' => $googleUser->getId(),
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'avatar' => $googleUser->getAvatar(),
                    ]
                ]);
            } else {
                // User doesn't exist
                return response()->json([
                    'success' => false,
                    'message' => 'Account non trovato. Contatta il tuo amministratore per essere aggiunto alla piattaforma.',
                    'google_user' => [
                        'id' => $googleUser->getId(),
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'avatar' => $googleUser->getAvatar(),
                    ]
                ], 404);
            }
            
        } catch (\Exception $e) {
            \Log::error('Debug Google OAuth Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'autenticazione con Google: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
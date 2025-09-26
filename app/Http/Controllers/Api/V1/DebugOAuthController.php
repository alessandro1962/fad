<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class DebugOAuthController extends Controller
{
    /**
     * Test Google OAuth callback - restituisce tutti i dati
     */
    public function debugGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            return response()->json([
                'success' => true,
                'google_user' => [
                    'id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                ],
                'user_in_db' => User::where('email', $googleUser->getEmail())->first(),
                'timestamp' => now(),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}

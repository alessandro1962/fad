<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reseller;
use App\Models\ResellerToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resellers = Reseller::with(['user', 'tokens', 'clients'])
            ->withCount(['clients', 'createdUsers', 'createdOrganizations'])
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $resellers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'vat_number' => 'nullable|string|max:50',
            'tokens_assigned' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_reseller' => true,
                'email_verified_at' => now(),
            ]);

            // Create reseller profile
            $reseller = Reseller::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'address' => $request->address,
                'vat_number' => $request->vat_number,
            ]);

            // Create initial tokens if provided
            if ($request->tokens_assigned > 0) {
                ResellerToken::create([
                    'reseller_id' => $reseller->id,
                    'admin_id' => auth()->id(),
                    'tokens_assigned' => $request->tokens_assigned,
                    'assigned_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rivenditore creato con successo',
                'data' => $reseller->load(['user', 'tokens'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione del rivenditore',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reseller $reseller)
    {
        $reseller->load([
            'user',
            'tokens',
            'clients.user',
            'clients.organization',
            'createdUsers',
            'createdOrganizations'
        ]);

        return response()->json([
            'success' => true,
            'data' => $reseller
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reseller $reseller)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $reseller->user_id,
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'vat_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Update user
            $reseller->user->update([
                'name' => $request->name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
            ]);

            // Update reseller
            $reseller->update([
                'company_name' => $request->company_name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'address' => $request->address,
                'vat_number' => $request->vat_number,
                'is_active' => $request->is_active ?? $reseller->is_active,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rivenditore aggiornato con successo',
                'data' => $reseller->load(['user', 'tokens'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento del rivenditore',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reseller $reseller)
    {
        DB::beginTransaction();
        try {
            // Delete reseller (this will cascade to tokens and clients)
            $reseller->delete();
            
            // Delete user
            $reseller->user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rivenditore eliminato con successo'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione del rivenditore',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign tokens to reseller.
     */
    public function assignTokens(Request $request, Reseller $reseller)
    {
        $request->validate([
            'tokens' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $tokens = $reseller->tokens;
            
            if (!$tokens) {
                $tokens = ResellerToken::create([
                    'reseller_id' => $reseller->id,
                    'admin_id' => auth()->id(),
                    'tokens_assigned' => $request->tokens,
                    'assigned_at' => now(),
                ]);
            } else {
                $tokens->tokens_assigned += $request->tokens;
                $tokens->assigned_at = now();
                $tokens->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Assegnati {$request->tokens} gettoni al rivenditore",
                'data' => $tokens
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'assegnazione dei gettoni',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reseller statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_resellers' => 0,
            'active_resellers' => 0,
            'total_tokens_assigned' => 0,
            'total_tokens_used' => 0,
            'total_clients' => 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations
     */
    public function index(Request $request): JsonResponse
    {
        $all = $request->get('all', false);
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = Organization::withCount(['users' => function($query) {
            $query->where('is_admin', false)->where('is_company_manager', false);
        }]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('vat_number', 'like', "%{$search}%");
            });
        }

        if ($all) {
            $organizations = $query->orderBy($sortBy, $sortOrder)->get();
            return response()->json([
                'data' => $organizations,
            ]);
        }

        $organizations = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        return response()->json([
            'data' => $organizations->items(),
            'pagination' => [
                'current_page' => $organizations->currentPage(),
                'last_page' => $organizations->lastPage(),
                'per_page' => $organizations->perPage(),
                'total' => $organizations->total(),
            ],
        ]);
    }

    /**
     * Store a newly created organization
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vat_number' => ['nullable', 'string', 'max:255', 'unique:organizations'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'sso_type' => ['nullable', 'in:none,google,azuread'],
            'is_active' => ['boolean'],
        ]);

        $organization = Organization::create($validated);

        return response()->json([
            'message' => 'Azienda creata con successo',
            'data' => $organization,
        ], 201);
    }

    /**
     * Display the specified organization
     */
    public function show(Organization $organization): JsonResponse
    {
        $organization->load(['users' => function($query) {
            $query->where('is_admin', false);
        }]);

        return response()->json([
            'data' => $organization,
        ]);
    }

    /**
     * Update the specified organization
     */
    public function update(Request $request, Organization $organization): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vat_number' => ['nullable', 'string', 'max:255', 'unique:organizations,vat_number,' . $organization->id],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'sso_type' => ['nullable', 'in:none,google,azuread'],
            'is_active' => ['boolean'],
        ]);

        $organization->update($validated);

        return response()->json([
            'message' => 'Azienda aggiornata con successo',
            'data' => $organization,
        ]);
    }

    /**
     * Remove the specified organization
     */
    public function destroy(Organization $organization): JsonResponse
    {
        // Check if organization has users
        $userCount = $organization->users()->count();
        if ($userCount > 0) {
            return response()->json([
                'error' => 'Impossibile eliminare l\'azienda. Ha ' . $userCount . ' utenti associati.',
            ], 422);
        }

        $organization->delete();

        return response()->json([
            'message' => 'Azienda eliminata con successo',
        ]);
    }

    /**
     * Get users not assigned to any organization
     */
    public function unassignedUsers(): JsonResponse
    {
        $users = User::whereNull('organization_id')
            ->where('is_admin', false)
            ->select('id', 'name', 'email', 'company', 'profession')
            ->get();

        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * Assign users to organization
     */
    public function assignUsers(Request $request, Organization $organization): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $users = User::whereIn('id', $validated['user_ids'])
            ->whereNull('organization_id')
            ->where('is_admin', false)
            ->get();

        if ($users->count() !== count($validated['user_ids'])) {
            return response()->json([
                'error' => 'Alcuni utenti non sono validi o sono già assegnati a un\'azienda',
            ], 422);
        }

        $users->each(function($user) use ($organization) {
            $user->update(['organization_id' => $organization->id]);
        });

        return response()->json([
            'message' => $users->count() . ' utenti assegnati all\'azienda con successo',
            'data' => $users,
        ]);
    }

    /**
     * Remove users from organization
     */
    public function removeUsers(Request $request, Organization $organization): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $users = User::whereIn('id', $validated['user_ids'])
            ->where('organization_id', $organization->id)
            ->where('is_admin', false)
            ->get();

        $users->each(function($user) {
            $user->update(['organization_id' => null]);
        });

        return response()->json([
            'message' => $users->count() . ' utenti rimossi dall\'azienda con successo',
        ]);
    }

    /**
     * Create company manager for organization
     */
    public function createCompanyManager(Request $request, Organization $organization): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
        ]);

        // Check if organization already has a company manager
        $existingManager = User::where('organization_id', $organization->id)
            ->where('is_company_manager', true)
            ->first();

        if ($existingManager) {
            return response()->json([
                'error' => 'Questa azienda ha già un company manager: ' . $existingManager->name,
            ], 422);
        }

        // Create company manager
        $plainPassword = $validated['password'];
        $validated['password'] = bcrypt($validated['password']);
        $validated['organization_id'] = $organization->id;
        $validated['is_company_manager'] = true;
        $validated['is_admin'] = false;
        $validated['email_verified_at'] = now();
        $validated['marketing_consent'] = true;
        $validated['privacy_consent'] = true;

        unset($validated['password_confirmation']);

        $companyManager = User::create($validated);

        // Send welcome email with password
        event(new \App\Events\UserRegistered($companyManager, $plainPassword));

        return response()->json([
            'message' => 'Company manager creato con successo',
            'data' => $companyManager,
        ], 201);
    }

    /**
     * Get organization statistics
     */
    public function statistics(Organization $organization): JsonResponse
    {
        $stats = [
            'total_users' => $organization->users()->where('is_admin', false)->count(),
            'company_managers' => $organization->users()->where('is_company_manager', true)->count(),
            'employees' => $organization->users()
                ->where('is_admin', false)
                ->where('is_company_manager', false)
                ->count(),
            'active_users' => $organization->users()
                ->where('is_admin', false)
                ->whereNotNull('last_login_at')
                ->count(),
        ];

        return response()->json([
            'data' => $stats,
        ]);
    }
}
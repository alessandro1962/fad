<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Reseller;
use App\Models\ResellerClient;
use App\Models\User;
use App\Models\Organization;
use App\Models\Enrollment;
use App\Models\Course;
use App\Notifications\UserRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResellerDashboardController extends Controller
{
    /**
     * Get reseller dashboard data.
     */
    public function dashboard()
    {
        $reseller = auth()->user()->reseller;
        
        if (!$reseller) {
            return response()->json([
                'success' => false,
                'message' => 'Profilo rivenditore non trovato'
            ], 404);
        }

        $reseller->load(['tokens', 'clients.user', 'createdUsers', 'createdOrganizations']);

        $stats = [
            'total_tokens' => $reseller->tokens->tokens_assigned + $reseller->tokens->tokens_purchased,
            'used_tokens' => $reseller->tokens->tokens_used,
            'available_tokens' => $reseller->tokens->available_tokens,
            'total_clients' => $reseller->clients_count,
            'standalone_users' => $reseller->createdUsers()->whereNull('organization_id')->count(),
            'organizations' => $reseller->createdOrganizations()->count(),
            'company_managers' => $reseller->createdUsers()->where('is_company_manager', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'reseller' => $reseller,
                'tokens' => $reseller->tokens,
                'statistics' => $stats,
                'created_users' => $reseller->createdUsers,
                'created_organizations' => $reseller->createdOrganizations
            ]
        ]);
    }

    /**
     * Get reseller clients.
     */
    public function clients()
    {
        $reseller = auth()->user()->reseller;
        
        $clients = ResellerClient::where('reseller_id', $reseller->id)
            ->with(['user', 'organization'])
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $clients
        ]);
    }

    /**
     * Get standalone users created by reseller.
     */
    public function getStandaloneUsers()
    {
        $reseller = auth()->user()->reseller;
        
        $users = User::where('created_by_reseller_id', $reseller->id)
            ->whereNull('organization_id')
            ->where('is_company_manager', false)
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get organizations created by reseller.
     */
    public function getOrganizations()
    {
        $reseller = auth()->user()->reseller;
        
        $organizations = Organization::where('created_by_reseller_id', $reseller->id)
            ->with(['manager'])
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * Get company managers created by reseller.
     */
    public function getCompanyManagers()
    {
        $reseller = auth()->user()->reseller;
        
        $managers = User::where('created_by_reseller_id', $reseller->id)
            ->where('is_company_manager', true)
            ->with(['organization'])
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $managers
        ]);
    }

    /**
     * Update a standalone user.
     */
    public function updateStandaloneUser(Request $request, $userId)
    {
        $reseller = auth()->user()->reseller;
        
        $user = User::where('id', $userId)
            ->where('created_by_reseller_id', $reseller->id)
            ->whereNull('organization_id')
            ->where('is_company_manager', false)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'name' => $request->name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Utente aggiornato con successo',
            'data' => $user
        ]);
    }

    /**
     * Update an organization.
     */
    public function updateOrganization(Request $request, $organizationId)
    {
        $reseller = auth()->user()->reseller;
        
        $organization = Organization::where('id', $organizationId)
            ->where('created_by_reseller_id', $reseller->id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $organization->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Organizzazione aggiornata con successo',
            'data' => $organization
        ]);
    }

    /**
     * Update a company manager.
     */
    public function updateCompanyManager(Request $request, $managerId)
    {
        $reseller = auth()->user()->reseller;
        
        $manager = User::where('id', $managerId)
            ->where('created_by_reseller_id', $reseller->id)
            ->where('is_company_manager', true)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $manager->id,
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'name' => $request->name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $manager->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Company manager aggiornato con successo',
            'data' => $manager
        ]);
    }

    /**
     * Assign user to organization.
     */
    public function assignUserToOrganization(Request $request)
    {
        $reseller = auth()->user()->reseller;
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $user = User::where('id', $request->user_id)
            ->where('created_by_reseller_id', $reseller->id)
            ->whereNull('organization_id')
            ->firstOrFail();

        $organization = Organization::where('id', $request->organization_id)
            ->where('created_by_reseller_id', $reseller->id)
            ->firstOrFail();

        $user->update([
            'organization_id' => $organization->id,
        ]);

        // Update reseller client record
        $resellerClient = ResellerClient::where('reseller_id', $reseller->id)
            ->where('user_id', $user->id)
            ->first();
        
        if ($resellerClient) {
            $resellerClient->update([
                'organization_id' => $organization->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Utente assegnato all\'organizzazione con successo',
            'data' => $user->load('organization')
        ]);
    }

    /**
     * Create a standalone user.
     */
    public function createStandaloneUser(Request $request)
    {
        \Log::info('createStandaloneUser called with data:', $request->all());
        $reseller = auth()->user()->reseller;
        
        if (!$reseller || !$reseller->tokens || !$reseller->tokens->hasEnoughTokens(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Gettoni insufficienti per creare un utente'
            ], 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
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
                'created_by_reseller_id' => $reseller->id,
                'email_verified_at' => now(),
            ]);

            // Create reseller client record
            ResellerClient::create([
                'reseller_id' => $reseller->id,
                'user_id' => $user->id,
                'tokens_used' => 1,
            ]);

            // Use token
            $reseller->tokens->useTokens(1);

            // Enroll user in Full Vision course (ID: 11)
            $fullVisionCourse = Course::find(11);
            if ($fullVisionCourse) {
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => 11,
                    'source' => 'assign',
                    'status' => 'active',
                    'started_at' => now(),
                ]);
                
                // Auto-enroll in all other courses
                $this->autoEnrollInAllCourses($user);
            }

            // Send welcome email
            $user->notify(new UserRegisteredNotification($user, $request->password));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Utente standalone creato con successo',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in createStandaloneUser: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione dell\'utente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create an organization with company manager.
     */
    public function createOrganization(Request $request)
    {
        \Log::info('createOrganization called with data:', $request->all());
        $reseller = auth()->user()->reseller;
        
        // Organization + Manager creation doesn't require tokens (0 tokens)
        // if (!$reseller || !$reseller->tokens || !$reseller->tokens->hasEnoughTokens(0)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Gettoni insufficienti per creare un\'organizzazione'
        //     ], 400);
        // }

        try {
            $request->validate([
                'organization_name' => 'required|string|max:255',
                'manager_name' => 'required|string|max:255',
                'manager_first_name' => 'required|string|max:255',
                'manager_last_name' => 'required|string|max:255',
                'manager_email' => 'required|email|unique:users,email',
                'manager_password' => 'required|string|min:8',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in createOrganization:', $e->errors());
            throw $e;
        }

        DB::beginTransaction();
        try {
            // Create organization
            $organization = Organization::create([
                'name' => $request->organization_name,
                'created_by_reseller_id' => $reseller->id,
            ]);

            // Create company manager
            $manager = User::create([
                'name' => $request->manager_name,
                'first_name' => $request->manager_first_name,
                'last_name' => $request->manager_last_name,
                'email' => $request->manager_email,
                'password' => Hash::make($request->manager_password),
                'organization_id' => $organization->id,
                'is_company_manager' => true,
                'created_by_reseller_id' => $reseller->id,
                'email_verified_at' => now(),
            ]);

            // Create reseller client record for manager
            ResellerClient::create([
                'reseller_id' => $reseller->id,
                'user_id' => $manager->id,
                'organization_id' => $organization->id,
                'tokens_used' => 0, // Organization + Manager creation doesn't use tokens
            ]);

            // Organization + Manager creation doesn't use tokens (0 tokens)
            // $reseller->tokens->useTokens(0);

            // Organization + Manager creation doesn't include Full Vision (0 tokens)
            // $fullVisionCourse = Course::find(11);
            // if ($fullVisionCourse) {
            //     Enrollment::create([
            //         'user_id' => $manager->id,
            //         'course_id' => 11,
            //         'source' => 'reseller',
            //         'status' => 'active',
            //         'started_at' => now(),
            //     ]);
            //     
            //     // Auto-enroll in all other courses
            //     $this->autoEnrollInAllCourses($manager);
            // }

            // Send welcome email to manager
            $manager->notify(new UserRegisteredNotification($manager, $request->manager_password));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Organizzazione e company manager creati con successo',
                'data' => [
                    'organization' => $organization,
                    'manager' => $manager
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            \Log::error('Validation error in createOrganization: ', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in createOrganization: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione dell\'organizzazione',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add user to organization.
     */
    public function addUserToOrganization(Request $request)
    {
        $reseller = auth()->user()->reseller;
        
        if (!$reseller || !$reseller->tokens || !$reseller->tokens->hasEnoughTokens(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Gettoni insufficienti per aggiungere un utente'
            ], 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        // Check if organization was created by this reseller
        $organization = Organization::where('id', $request->organization_id)
            ->where('created_by_reseller_id', $reseller->id)
            ->first();

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organizzazione non trovata o non autorizzata'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'organization_id' => $organization->id,
                'created_by_reseller_id' => $reseller->id,
                'email_verified_at' => now(),
            ]);

            // Create reseller client record
            ResellerClient::create([
                'reseller_id' => $reseller->id,
                'user_id' => $user->id,
                'organization_id' => $organization->id,
                'tokens_used' => 1,
            ]);

            // Use token
            $reseller->tokens->useTokens(1);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Utente aggiunto all\'organizzazione con successo',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiunta dell\'utente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reseller statistics.
     */
    public function statistics()
    {
        $reseller = auth()->user()->reseller;
        
        $stats = [
            'total_clients' => $reseller->clients()->count(),
            'standalone_users' => $reseller->createdUsers()->whereNull('organization_id')->count(),
            'organizations' => $reseller->createdOrganizations()->count(),
            'company_managers' => $reseller->createdUsers()->where('is_company_manager', true)->count(),
            'total_tokens_used' => $reseller->tokens ? $reseller->tokens->tokens_used : 0,
            'available_tokens' => $reseller->total_tokens,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Auto-enroll user in all courses when they get Full Vision access
     */
    private function autoEnrollInAllCourses(User $user): void
    {
        // Get all active courses except the Full Vision course itself
        $allCourses = Course::where('is_active', true)
            ->where('id', '!=', 11) // Exclude Full Vision course
            ->get();

        foreach ($allCourses as $course) {
            // Check if user is already enrolled in this course
            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$existingEnrollment) {
                // Create enrollment for this course
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'source' => 'full_vision',
                    'status' => 'active',
                    'started_at' => now(),
                ]);
            }
        }
    }
}

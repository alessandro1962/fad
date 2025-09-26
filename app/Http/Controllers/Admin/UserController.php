<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Events\UserRegistered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Imports\UsersImport;
use App\Exports\UsersImportTemplate;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['organization', 'enrollments'])
            ->withCount(['enrollments']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter by admin status
        if ($request->filled('is_admin')) {
            $query->where('is_admin', $request->boolean('is_admin'));
        }

        // Filter by email verification
        if ($request->filled('email_verified')) {
            if ($request->boolean('email_verified')) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Check if all users are requested (for dropdowns)
        if ($request->boolean('all')) {
            $users = $query->get();
            return response()->json([
                'data' => $users
            ]);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
            'links' => [
                'first' => $users->url(1),
                'last' => $users->url($users->lastPage()),
                'prev' => $users->previousPageUrl(),
                'next' => $users->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'company' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'is_admin' => ['boolean'],
            'is_company_manager' => ['boolean'],
            'marketing_consent' => ['boolean'],
            'privacy_consent' => ['boolean'],
        ]);

        // Salva la password in chiaro per l'email (prima di criptarla)
        $plainPassword = $validated['password'];
        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now();

        $user = User::create($validated);

        // Scatena l'evento di registrazione per inviare l'email di benvenuto
        // Passa la password in chiaro che l'admin ha inserito
        \Log::info('Scateno evento UserRegistered per utente: ' . $user->email . ' con password: ' . $plainPassword);
        event(new UserRegistered($user, $plainPassword));
        \Log::info('Evento UserRegistered scatenato con successo');

        return response()->json([
            'message' => 'Utente creato con successo',
            'data' => $user->load(['organization', 'enrollments']),
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['organization', 'enrollments.course']);

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'company' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'is_admin' => ['boolean'],
            'marketing_consent' => ['boolean'],
            'privacy_consent' => ['boolean'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Utente aggiornato con successo',
            'data' => $user->load(['organization', 'enrollments']),
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Non puoi eliminare il tuo stesso account',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'Utente eliminato con successo',
        ]);
    }

    /**
     * Toggle admin status.
     */
    public function toggleAdmin(User $user): JsonResponse
    {
        // Prevent admin from removing their own admin status
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Non puoi modificare il tuo stesso status admin',
            ], 422);
        }

        $user->update(['is_admin' => !$user->is_admin]);

        return response()->json([
            'message' => 'Status admin aggiornato con successo',
            'data' => $user,
        ]);
    }

    /**
     * Get user statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'regular_users' => User::where('is_admin', false)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'recent_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return response()->json([
            'data' => $stats,
        ]);
    }

    /**
     * Import users from CSV file.
     */
    public function importCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // 10MB max
        ]);

        try {
            $import = new UsersImport();
            Excel::import($import, $request->file('file'));

            return response()->json([
                'message' => 'Import completato con successo',
                'data' => [
                    'imported_count' => $import->getImportedCount(),
                    'skipped_count' => $import->getSkippedCount(),
                    'errors' => $import->getErrors(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore durante l\'import',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Download CSV template for user import.
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $template = new UsersImportTemplate();
        return Excel::download($template, 'template_import_utenti.csv');
    }
}

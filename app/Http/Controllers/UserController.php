<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $search = $request->validate([
            'search' => ['nullable', 'string'],
        ])['search'] ?? null;

        $users = User::paginateForManagement($search);
        $roles = Role::forSelection();

        return Inertia::render('users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::default()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        // Privilege Escalation Protection: Hanya Super Admin yang boleh menugaskan role Super Admin
        if (isset($validated['roles']) && in_array('Super Admin', $validated['roles']) && ! $request->user()->hasRole('Super Admin')) {
            return back()->withErrors(['roles' => 'Hanya Super Admin yang berhak menugaskan role Super Admin']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        if (! empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->withProperties(['roles' => $validated['roles'] ?? []])
            ->log('Menambahkan pengguna baru: '.$user->name);

        return back()->with('success', 'User baru berhasil ditambahkan');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::default()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        // Privilege Escalation Protection
        $isTargetSuperAdmin = $user->hasRole('Super Admin');
        $isActorSuperAdmin = $request->user()->hasRole('Super Admin');

        // Non-SuperAdmin dilarang mengedit Super Admin
        if ($isTargetSuperAdmin && ! $isActorSuperAdmin) {
            return back()->withErrors(['message' => 'Hanya Super Admin yang diizinkan mengedit akun Super Admin']);
        }

        // Non-SuperAdmin dilarang memberikan atau mencabut role Super Admin
        if (isset($validated['roles']) && ! $isActorSuperAdmin) {
            if (in_array('Super Admin', $validated['roles']) || $isTargetSuperAdmin) {
                return back()->withErrors(['roles' => 'Hanya Super Admin yang berhak mengatur status role Super Admin']);
            }
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        if (array_key_exists('roles', $validated)) {
            $user->syncRoles($validated['roles'] ?? []);
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->withProperties(['roles' => $validated['roles'] ?? []])
            ->log('Memperbarui data pengguna: '.$user->name);

        return back()->with('success', 'Data user berhasil diperbarui');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['message' => 'Anda tidak dapat menghapus akun Anda sendiri']);
        }

        if ($user->isLastSuperAdmin()) {
            return back()->withErrors(['message' => 'Tidak dapat menghapus Super Admin terakhir']);
        }

        $userName = $user->name;
        $user->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Menghapus pengguna: '.$userName);

        return back()->with('success', 'User berhasil dihapus');
    }
}

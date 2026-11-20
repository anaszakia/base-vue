<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionScannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(PermissionScannerService $scanner): Response|RedirectResponse
    {
        // Otomatis sinkronkan modul baru agar langsung tersedia di checklist
        $scanner->scanAndSync();

        $roles = Role::paginateForManagement();

        if ($roles->currentPage() > $roles->lastPage()) {
            return redirect()->route('roles.index', ['page' => $roles->lastPage()]);
        }

        $permissions = Permission::orderedForSelection();

        return Inertia::render('roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return back()->with('success', 'Role berhasil ditambahkan');
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return back()->with('success', 'Role berhasil diperbarui');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'Super Admin') {
            return back()->withErrors(['message' => 'Role Super Admin tidak dapat dihapus']);
        }

        $role->delete();

        return back()->with('success', 'Role berhasil dihapus');
    }
}

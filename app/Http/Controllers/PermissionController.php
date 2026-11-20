<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Services\PermissionScannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(PermissionScannerService $scanner): Response|RedirectResponse
    {
        // Otomatis sinkronkan dan temukan permission baru dari controller & routes
        $scanner->scanAndSync();

        $permissions = Permission::paginateForManagement();

        if ($permissions->currentPage() > $permissions->lastPage()) {
            return redirect()->route('permissions.index', ['page' => $permissions->lastPage()]);
        }

        return Inertia::render('permissions/Index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create(['name' => $validated['name']]);

        return back()->with('success', 'Permission berhasil ditambahkan');
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,'.$permission->id],
        ]);

        $permission->update(['name' => $validated['name']]);

        return back()->with('success', 'Permission berhasil diperbarui');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return back()->with('success', 'Permission berhasil dihapus');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    /**
     * Display a listing of menus.
     */
    public function index(): Response|RedirectResponse
    {
        $menus = Menu::paginateForManagement();

        if ($menus->currentPage() > $menus->lastPage()) {
            return redirect()->route('menus.index', ['page' => $menus->lastPage()]);
        }

        $parentMenus = Menu::parentMenusForSelection();
        $permissions = Permission::orderedForSelection();

        return Inertia::render('menus/Index', [
            'menus' => $menus,
            'parentMenus' => $parentMenus,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:menus,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'route_or_url' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'permission_name' => ['nullable', 'string', 'exists:permissions,name'],
            'order' => ['required', 'integer'],
            'is_active' => ['boolean'],
        ]);

        Menu::create($validated);

        return back()->with('success', 'Menu berhasil ditambahkan');
    }

    /**
     * Update the specified menu.
     */
    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:menus,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'route_or_url' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'permission_name' => ['nullable', 'string', 'exists:permissions,name'],
            'order' => ['required', 'integer'],
            'is_active' => ['boolean'],
        ]);

        // Prevent setting itself as parent
        if (isset($validated['parent_id']) && $validated['parent_id'] == $menu->id) {
            return back()->withErrors(['parent_id' => 'Menu tidak dapat menjadi parent dari dirinya sendiri']);
        }

        $menu->update($validated);

        return back()->with('success', 'Menu berhasil diperbarui');
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus');
    }
}

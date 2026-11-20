<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class MenuService
{
    /**
     * Get accessible menu hierarchy grouped by category for the given user.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getMenuForUser(?User $user): Collection
    {
        if (! $user) {
            return collect();
        }

        $allMenus = Menu::activeMenusForSidebar();

        // Ambil root menus (parent_id is null)
        $rootMenus = $this->buildMenuTree($allMenus, null, $user);

        // Kelompokkan berdasarkan category
        $grouped = $rootMenus->groupBy(function (array $menu): string {
            return ! empty($menu['category']) ? $menu['category'] : 'Menu';
        });

        // Format hasil kelompok: [{ category: 'Dashboard', items: [...] }, ...]
        return $grouped->map(function (Collection $items, string $category): array {
            return [
                'category' => $category,
                'items' => $items->values()->all(),
            ];
        })->values();
    }

    /**
     * Build nested menu tree filtered by user permission.
     *
     * @param  Collection<int, Menu>  $menus
     * @return Collection<int, array<string, mixed>>
     */
    protected function buildMenuTree(Collection $menus, ?int $parentId, User $user): Collection
    {
        return $menus
            ->where('parent_id', $parentId)
            ->filter(function (Menu $menu) use ($user, $menus): bool {
                $hasChildren = $menus->where('parent_id', $menu->id)->isNotEmpty();

                // If menu has permission specified, user must have it
                if ($menu->permission_name && ! $user->can($menu->permission_name)) {
                    return false;
                }

                // If it's a parent menu without permission, it should be visible only if it has visible children
                if ($hasChildren) {
                    $children = $this->buildMenuTree($menus, $menu->id, $user);

                    return $children->isNotEmpty();
                }

                return true;
            })
            ->map(function (Menu $menu) use ($menus, $user): array {
                $children = $this->buildMenuTree($menus, $menu->id, $user);

                $url = $menu->route_or_url;
                if ($url && Route::has($url)) {
                    $url = route($url);
                }

                return [
                    'id' => $menu->id,
                    'category' => $menu->category ?? 'Menu',
                    'title' => $menu->title,
                    'href' => $url ?? '#',
                    'route_or_url' => $menu->route_or_url,
                    'icon' => $menu->icon,
                    'permission_name' => $menu->permission_name,
                    'children' => $children->values()->all(),
                ];
            })
            ->values();
    }
}

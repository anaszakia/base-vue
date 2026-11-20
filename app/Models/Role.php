<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public static function paginateForManagement(): LengthAwarePaginator
    {
        return static::query()
            ->with('permissions')
            ->orderBy('id')
            ->paginate(10);
    }

    /**
     * @return Collection<int, self>
     */
    public static function forSelection(): Collection
    {
        return static::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return Collection<int, self>
     */
    public static function dashboardSummary(): Collection
    {
        return static::query()
            ->withCount(['permissions', 'users'])
            ->orderByDesc('users_count')
            ->take(5)
            ->get(['id', 'name']);
    }

    public static function countForDashboard(): int
    {
        return static::query()->count();
    }

    public static function syncSuperAdminPermissions(): void
    {
        $superAdmin = static::query()
            ->where('name', 'Super Admin')
            ->first();

        if ($superAdmin !== null) {
            $superAdmin->syncPermissions(Permission::all());
        }
    }
}

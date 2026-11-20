<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public static function paginateForManagement(): LengthAwarePaginator
    {
        return static::query()
            ->orderBy('name')
            ->paginate(10);
    }

    /**
     * @return Collection<int, self>
     */
    public static function orderedForSelection(): Collection
    {
        return static::query()
            ->orderBy('name')
            ->get();
    }

    public static function countForDashboard(): int
    {
        return static::query()->count();
    }

    /**
     * @param  list<string>  $permissionNames
     * @return list<string>
     */
    public static function createMissing(array $permissionNames): array
    {
        $newlyCreated = [];

        foreach ($permissionNames as $permissionName) {
            if (static::query()->where('name', $permissionName)->exists()) {
                continue;
            }

            static::create(['name' => $permissionName]);
            $newlyCreated[] = $permissionName;
        }

        return $newlyCreated;
    }
}

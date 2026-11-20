<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    /**
     * @return Collection<int, self>
     */
    public static function recentForDashboard(): Collection
    {
        return static::query()
            ->with('causer')
            ->latest()
            ->take(5)
            ->get();
    }
}

<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of system activity logs.
     */
    public function index(): Response
    {
        $activities = Activity::with('causer')
            ->latest()
            ->paginate(15);

        return Inertia::render('activities/Index', [
            'activities' => $activities,
        ]);
    }
}

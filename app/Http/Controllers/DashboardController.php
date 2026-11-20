<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function __invoke(): Response
    {
        $stats = [
            'total_users' => User::countForDashboard(),
            'total_roles' => Role::countForDashboard(),
            'total_permissions' => Permission::countForDashboard(),
            'total_menus' => Menu::countForDashboard(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentUsers' => User::recentForDashboard(),
            'rolesSummary' => Role::dashboardSummary(),
            'recentActivities' => Activity::recentForDashboard(),
        ]);
    }
}

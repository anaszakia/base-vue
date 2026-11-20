<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class PermissionScannerService
{
    /**
     * Scan application routes, controllers, and pages to automatically discover and register permissions.
     *
     * @return array{discovered: array<string>, newly_created: array<string>}
     */
    public function scanAndSync(): array
    {
        $discovered = collect();

        // 1. Scan routes middleware for `permission:xyz`
        $routeCollection = Route::getRoutes();
        foreach ($routeCollection as $route) {
            $middlewares = $route->gatherMiddleware();
            foreach ($middlewares as $middleware) {
                if (is_string($middleware) && Str::startsWith($middleware, 'permission:')) {
                    $perms = explode('|', Str::after($middleware, 'permission:'));
                    foreach ($perms as $p) {
                        $p = trim($p);
                        if ($p !== '') {
                            $discovered->push($p);
                        }
                    }
                }
            }
        }

        // 2. Scan Controllers in app/Http/Controllers (kecuali bawaan/vendor)
        $controllerFiles = File::isDirectory(app_path('Http/Controllers'))
            ? File::allFiles(app_path('Http/Controllers'))
            : [];

        foreach ($controllerFiles as $file) {
            $className = $file->getFilenameWithoutExtension();
            if (in_array($className, ['Controller', 'SecurityController', 'ProfileController'])) {
                continue;
            }

            // Misal: MahasiswaController -> mahasiswa
            $module = Str::of($className)
                ->replaceLast('Controller', '')
                ->snake('-')
                ->plural()
                ->toString();

            if ($module !== '') {
                $discovered->push("{$module}.view");
                $discovered->push("{$module}.create");
                $discovered->push("{$module}.edit");
                $discovered->push("{$module}.delete");
            }
        }

        // 3. Scan resources/js/pages directories untuk mendeteksi modul halaman baru
        $pagesPath = resource_path('js/pages');
        if (File::isDirectory($pagesPath)) {
            $directories = File::directories($pagesPath);
            foreach ($directories as $dir) {
                $dirName = basename($dir);
                if (in_array($dirName, ['auth', 'settings'])) {
                    continue;
                }

                $module = Str::snake($dirName, '-');
                $discovered->push("{$module}.view");
                $discovered->push("{$module}.create");
                $discovered->push("{$module}.edit");
                $discovered->push("{$module}.delete");
            }
        }

        // Standard default permissions
        $discovered->push('dashboard.view');

        $uniqueList = $discovered->unique()->sort()->values();
        $newlyCreated = Permission::createMissing($uniqueList->all());
        Role::syncSuperAdminPermissions();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return [
            'discovered' => $uniqueList->all(),
            'newly_created' => $newlyCreated,
        ];
    }
}

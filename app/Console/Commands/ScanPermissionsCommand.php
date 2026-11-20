<?php

namespace App\Console\Commands;

use App\Services\PermissionScannerService;
use Illuminate\Console\Command;

class ScanPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan routes and controllers to automatically register new permissions';

    /**
     * Execute the console command.
     */
    public function handle(PermissionScannerService $scanner): int
    {
        $this->info('Scanning routes, controllers, and modules...');

        $result = $scanner->scanAndSync();

        $this->info('Total permissions discovered: '.count($result['discovered']));

        if (! empty($result['newly_created'])) {
            $this->info('Newly registered permissions:');
            foreach ($result['newly_created'] as $newPerm) {
                $this->line(" + {$newPerm}");
            }
        } else {
            $this->comment('All permissions are already up to date.');
        }

        return self::SUCCESS;
    }
}

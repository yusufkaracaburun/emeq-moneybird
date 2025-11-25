<?php

namespace Emeq\Moneybird\Commands;

use Emeq\Moneybird\Services\MoneybirdService;
use Illuminate\Console\Command;

class TestConnectionCommand extends Command
{
    protected $signature = 'moneybird:test-connection
                            {--connection-id= : Specific connection ID to test}
                            {--user-id= : User ID to test connection for}
                            {--tenant-id= : Tenant ID to test connection for}';

    protected $description = 'Test Moneybird connection';

    public function handle(MoneybirdService $moneybirdService): int
    {
        try {
            $connectionId = $this->option('connection-id');
            $userId       = $this->option('user-id') ? (int) $this->option('user-id') : null;
            $tenantId     = $this->option('tenant-id');

            if ($connectionId) {
                $moneybirdService->connection(null, null, (int) $connectionId);
            } else {
                $moneybirdService->connection($userId, $tenantId);
            }

            $administrations = $moneybirdService->administrations()->list();

            $this->info('Connection successful!');
            $this->info('Found '.count($administrations).' administration(s):');
            $this->newLine();

            foreach ($administrations as $administration) {
                /** @var \Picqer\Financials\Moneybird\Entities\Administration $administration */
                $name = isset($administration->name) ? $administration->name : 'Unknown';
                $id   = isset($administration->id) ? (string) $administration->id : 'Unknown';
                $this->line("  - {$name} (ID: {$id})");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Connection test failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}

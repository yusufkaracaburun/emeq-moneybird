<?php

namespace Emeq\Moneybird\Commands;

use Emeq\Moneybird\Models\MoneybirdConnection;
use Emeq\Moneybird\Services\OAuthService;
use Illuminate\Console\Command;

class RefreshTokensCommand extends Command
{
    protected $signature = 'moneybird:refresh-tokens
                            {--connection-id= : Specific connection ID to refresh}
                            {--all : Refresh all connections}';

    protected $description = 'Refresh Moneybird OAuth tokens';

    public function handle(OAuthService $oauthService): int
    {
        if ($this->option('all')) {
            $connections = MoneybirdConnection::where('is_active', true)
                ->whereNotNull('refresh_token')
                ->get();

            if ($connections->isEmpty()) {
                $this->warn('No active connections with refresh tokens found');

                return self::SUCCESS;
            }

            $this->info("Refreshing tokens for {$connections->count()} connection(s)...");
            $this->newLine();

            $successCount = 0;
            $failureCount = 0;

            foreach ($connections as $connection) {
                try {
                    $oauthService->refreshTokens($connection);
                    $this->info("✓ Connection {$connection->id} refreshed successfully");
                    $successCount++;
                } catch (\Exception $e) {
                    $this->error("✗ Connection {$connection->id} failed: ".$e->getMessage());
                    $failureCount++;
                }
            }

            $this->newLine();
            $this->info("Completed: {$successCount} successful, {$failureCount} failed");

            return $failureCount > 0 ? self::FAILURE : self::SUCCESS;
        }

        $connectionId = $this->option('connection-id');

        if (! $connectionId) {
            $this->error('Either --connection-id or --all option is required');

            return self::FAILURE;
        }

        $connection = MoneybirdConnection::findOrFail($connectionId);

        try {
            $oauthService->refreshTokens($connection);
            $this->info("Successfully refreshed tokens for connection {$connectionId}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to refresh tokens: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}

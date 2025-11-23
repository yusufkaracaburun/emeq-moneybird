<?php

namespace Emeq\Moneybird\Commands;

use Emeq\Moneybird\Services\OAuthService;
use Illuminate\Console\Command;

class ConnectCommand extends Command
{
    protected $signature = 'moneybird:connect
                            {--user-id= : User ID to associate the connection with}
                            {--tenant-id= : Tenant ID to associate the connection with}';

    protected $description = 'Connect to Moneybird via OAuth';

    public function handle(OAuthService $oauthService): int
    {
        $this->info('Starting Moneybird OAuth connection...');

        $state = \Illuminate\Support\Str::random(40);
        $authorizationUrl = $oauthService->getAuthorizationUrl($state);

        $this->info('Please visit the following URL to authorize:');
        $this->line($authorizationUrl);
        $this->newLine();

        $authorizationCode = $this->ask('Enter the authorization code from the callback URL');

        if (! $authorizationCode) {
            $this->error('Authorization code is required');

            return self::FAILURE;
        }

        try {
            $connection = $oauthService->exchangeCodeForTokens(
                $authorizationCode,
                $this->option('user-id') ? (int) $this->option('user-id') : null,
                $this->option('tenant-id')
            );

            $this->info('Successfully connected to Moneybird!');
            $this->info("Connection ID: {$connection->id}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to connect: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}

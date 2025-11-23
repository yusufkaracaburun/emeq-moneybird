<?php

namespace Emeq\Moneybird\Services;

use Emeq\Moneybird\Exceptions\ConnectionErrorException;
use Emeq\Moneybird\Exceptions\MoneybirdException;
use Emeq\Moneybird\Models\MoneybirdConnection;
use Illuminate\Support\Str;
use Picqer\Financials\Moneybird\Connection;

class OAuthService
{
    /**
     * Get OAuth authorization URL.
     */
    public function getAuthorizationUrl(?string $state = null): string
    {
        $connection = $this->createConnection();
        $connection->setState($state ?? Str::random(40));

        return $connection->getAuthUrl();
    }

    /**
     * Exchange authorization code for access tokens.
     */
    public function exchangeCodeForTokens(string $authorizationCode, int $userId, ?string $tenantId = null, ?string $administrationId = null): MoneybirdConnection
    {
        if (! $userId) {
            throw new MoneybirdException('User ID is required');
        }

        $timeout = config('moneybird.api.timeout', 30);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout($timeout)
                ->asForm()
                ->post('https://moneybird.com/oauth/token', [
                    'grant_type' => 'authorization_code',
                    'code' => $authorizationCode,
                    'redirect_uri' => config('moneybird.oauth.redirect_uri'),
                    'client_id' => config('moneybird.oauth.client_id'),
                    'client_secret' => config('moneybird.oauth.client_secret'),
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $exception = new ConnectionErrorException('Failed to connect to Moneybird OAuth endpoint: '.$e->getMessage());
            throw $exception;
        }

        $body = $response->json();

        if (! $response->successful() || ! isset($body['access_token'])) {
            $errorMessage = $body['error_description'] ?? $body['error'] ?? 'Failed to exchange authorization code for tokens';
            $statusCode = $response->status();

            throw new MoneybirdException(
                "Failed to exchange authorization code for tokens (HTTP {$statusCode}): {$errorMessage}",
                $statusCode
            );
        }

        $connection = $this->createConnection();
        $connection->setAccessToken($body['access_token']);
        $moneybird = $this->createMoneybirdClient($connection);

        $administrations = $moneybird->administration()->get();

        if (empty($administrations)) {
            throw new MoneybirdException('No administrations found for this Moneybird account');
        }

        $selectedAdministration = null;
        if ($administrationId) {
            $selectedAdministration = collect($administrations)->firstWhere('id', $administrationId);
            if (! $selectedAdministration) {
                throw new MoneybirdException("Administration with ID {$administrationId} not found");
            }
        } else {
            $selectedAdministration = $administrations[0];
        }

        $moneybirdConnection = MoneybirdConnection::create([
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'name' => $selectedAdministration->name ?? 'Moneybird Connection',
            'administration_id' => (string) $selectedAdministration->id,
            'access_token' => $body['access_token'],
            'refresh_token' => $body['refresh_token'] ?? null,
            'expires_at' => isset($body['expires_in']) ? now()->addSeconds($body['expires_in']) : now()->addHours(1),
            'is_active' => true,
        ]);

        return $moneybirdConnection;
    }

    /**
     * Refresh access tokens using refresh token.
     */
    public function refreshTokens(MoneybirdConnection $connection): MoneybirdConnection
    {
        if (! $connection->refresh_token) {
            throw new MoneybirdException('No refresh token available');
        }

        $timeout = config('moneybird.api.timeout', 30);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout($timeout)
                ->asForm()
                ->post('https://moneybird.com/oauth/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $connection->refresh_token,
                    'client_id' => config('moneybird.oauth.client_id'),
                    'client_secret' => config('moneybird.oauth.client_secret'),
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $exception = new ConnectionErrorException('Failed to connect to Moneybird OAuth endpoint: '.$e->getMessage());
            throw $exception;
        }

        $body = $response->json();

        if (! $response->successful() || ! isset($body['access_token'])) {
            $errorMessage = $body['error_description'] ?? $body['error'] ?? 'Failed to refresh tokens';
            $statusCode = $response->status();

            throw new MoneybirdException(
                "Failed to refresh tokens (HTTP {$statusCode}): {$errorMessage}",
                $statusCode
            );
        }

        $connection->update([
            'access_token' => $body['access_token'],
            'refresh_token' => $body['refresh_token'] ?? $connection->refresh_token,
            'expires_at' => now()->addSeconds($body['expires_in'] ?? 3600),
        ]);

        return $connection->fresh();
    }

    /**
     * Create Picqer Moneybird connection instance.
     */
    protected function createConnection(): Connection
    {
        $connection = new Connection;
        $connection->setClientId(config('moneybird.oauth.client_id'));
        $connection->setClientSecret(config('moneybird.oauth.client_secret'));
        $connection->setRedirectUrl(config('moneybird.oauth.redirect_uri'));
        $connection->setScopes(config('moneybird.oauth.scopes', []));

        return $connection;
    }

    /**
     * Create Moneybird client instance.
     */
    protected function createMoneybirdClient(Connection $connection): \Picqer\Financials\Moneybird\Moneybird
    {
        return new \Picqer\Financials\Moneybird\Moneybird($connection);
    }
}

<?php

namespace Emeq\Moneybird\Services;

use Emeq\Moneybird\Models\MoneybirdConnection;
use Illuminate\Support\Str;
use Picqer\Financials\Moneybird\Connection;

class OAuthService
{
    public function getAuthorizationUrl(?string $state = null): string
    {
        $connection = $this->createConnection();
        $connection->setState($state ?? Str::random(40));

        return $connection->getAuthUrl();
    }

    public function exchangeCodeForTokens(string $authorizationCode, int $userId, ?string $tenantId = null, ?string $administrationId = null): MoneybirdConnection
    {
        if (! $userId) {
            throw new \RuntimeException('User ID is required');
        }

        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://moneybird.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $authorizationCode,
            'redirect_uri' => config('moneybird.oauth.redirect_uri'),
            'client_id' => config('moneybird.oauth.client_id'),
            'client_secret' => config('moneybird.oauth.client_secret'),
        ]);

        $body = $response->json();

        if (! $response->successful() || ! isset($body['access_token'])) {
            throw new \RuntimeException('Failed to exchange authorization code for tokens');
        }

        // Fetch administrations to get administration_id and name
        $connection = $this->createConnection();
        $connection->setAccessToken($body['access_token']);
        $moneybird = $this->createMoneybirdClient($connection);

        $administrations = $moneybird->administration()->get();

        if (empty($administrations)) {
            throw new \RuntimeException('No administrations found for this Moneybird account');
        }

        // Use provided administration_id or select the first one
        $selectedAdministration = null;
        if ($administrationId) {
            $selectedAdministration = collect($administrations)->firstWhere('id', $administrationId);
            if (! $selectedAdministration) {
                throw new \RuntimeException("Administration with ID {$administrationId} not found");
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

    public function refreshTokens(MoneybirdConnection $connection): MoneybirdConnection
    {
        if (! $connection->refresh_token) {
            throw new \RuntimeException('No refresh token available');
        }

        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://moneybird.com/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $connection->refresh_token,
            'client_id' => config('moneybird.oauth.client_id'),
            'client_secret' => config('moneybird.oauth.client_secret'),
        ]);

        $body = $response->json();

        if (! $response->successful() || ! isset($body['access_token'])) {
            throw new \RuntimeException('Failed to refresh tokens');
        }

        $connection->update([
            'access_token' => $body['access_token'],
            'refresh_token' => $body['refresh_token'] ?? $connection->refresh_token,
            'expires_at' => now()->addSeconds($body['expires_in'] ?? 3600),
        ]);

        return $connection->fresh();
    }

    protected function createConnection(): Connection
    {
        $connection = new Connection;
        $connection->setClientId(config('moneybird.oauth.client_id'));
        $connection->setClientSecret(config('moneybird.oauth.client_secret'));
        $connection->setRedirectUrl(config('moneybird.oauth.redirect_uri'));
        $connection->setScopes(config('moneybird.oauth.scopes', []));

        return $connection;
    }

    protected function createMoneybirdClient(Connection $connection): \Picqer\Financials\Moneybird\Moneybird
    {
        return new \Picqer\Financials\Moneybird\Moneybird($connection);
    }
}

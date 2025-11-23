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

    public function exchangeCodeForTokens(string $authorizationCode, ?int $userId = null, ?string $tenantId = null): MoneybirdConnection
    {
        $client = new \GuzzleHttp\Client;
        $response = $client->post('https://moneybird.com/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $authorizationCode,
                'redirect_uri' => config('moneybird.oauth.redirect_uri'),
                'client_id' => config('moneybird.oauth.client_id'),
                'client_secret' => config('moneybird.oauth.client_secret'),
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE || ! isset($body['access_token'])) {
            throw new \RuntimeException('Failed to exchange authorization code for tokens');
        }

        $moneybirdConnection = MoneybirdConnection::create([
            'user_id' => $userId,
            'tenant_id' => $tenantId,
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

        $client = new \GuzzleHttp\Client;
        $response = $client->post('https://moneybird.com/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $connection->refresh_token,
                'client_id' => config('moneybird.oauth.client_id'),
                'client_secret' => config('moneybird.oauth.client_secret'),
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE || ! isset($body['access_token'])) {
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
}

<?php

namespace Emeq\Moneybird\Services;

use Emeq\Moneybird\Models\MoneybirdConnection;
use Emeq\Moneybird\Resources\AdministrationResource;
use Emeq\Moneybird\Resources\ContactResource;
use Emeq\Moneybird\Resources\SalesInvoiceResource;
use Picqer\Financials\Moneybird\Connection;
use Picqer\Financials\Moneybird\Moneybird;

class MoneybirdService
{
    protected ?MoneybirdConnection $connection = null;

    protected ?Moneybird $client = null;

    public function __construct(
        protected OAuthService $oauthService
    ) {}

    /**
     * Set the Moneybird connection to use.
     */
    public function connection(?int $userId = null, ?string $tenantId = null, ?int $connectionId = null): self
    {
        $query = MoneybirdConnection::query()->where('is_active', true);

        if ($connectionId) {
            $this->connection = $query->findOrFail($connectionId);
        } elseif ($userId || $tenantId) {
            $query->where(function (\Illuminate\Database\Eloquent\Builder $q) use ($userId, $tenantId): void {
                if ($userId) {
                    $q->where('user_id', $userId);
                }

                if ($tenantId) {
                    $q->where('tenant_id', $tenantId);
                }
            });
            $this->connection = $query->firstOrFail();
        } else {
            $this->connection = $query->firstOrFail();
        }

        $this->ensureValidTokens();
        $this->client = null;

        return $this;
    }

    /**
     * Set connection directly.
     */
    public function setConnection(MoneybirdConnection $connection): self
    {
        $this->connection = $connection;
        $this->ensureValidTokens();

        return $this;
    }

    /**
     * Get the Moneybird client instance.
     */
    public function getClient(): Moneybird
    {
        if ($this->client) {
            return $this->client;
        }

        if (! $this->connection) {
            throw new \Emeq\Moneybird\Exceptions\MoneybirdException('No Moneybird connection set');
        }

        $connection   = $this->createPicqerConnection();
        $this->client = new Moneybird($connection);

        return $this->client;
    }

    /**
     * Get administrations resource.
     */
    public function administrations(): AdministrationResource
    {
        return new AdministrationResource($this->getClient());
    }

    /**
     * Get contacts resource.
     */
    public function contacts(): ContactResource
    {
        return new ContactResource($this->getClient());
    }

    /**
     * Get sales invoices resource.
     */
    public function salesInvoices(): SalesInvoiceResource
    {
        return new SalesInvoiceResource($this->getClient());
    }

    /**
     * Ensure access tokens are valid and refresh if needed.
     */
    protected function ensureValidTokens(): void
    {
        if (! $this->connection) {
            return;
        }

        if ($this->connection->needsRefresh()) {
            $this->oauthService->refreshTokens($this->connection);
            $this->connection->refresh();
        }
    }

    /**
     * Create Picqer Moneybird connection instance.
     */
    protected function createPicqerConnection(): Connection
    {
        $connection = new Connection;
        $connection->setClientId(config('moneybird.oauth.client_id'));
        $connection->setClientSecret(config('moneybird.oauth.client_secret'));
        $connection->setRedirectUrl(config('moneybird.oauth.redirect_uri'));
        $connection->setScopes(config('moneybird.oauth.scopes', []));
        $connection->setAccessToken($this->connection->access_token);

        if ($this->connection->administration_id) {
            $connection->setAdministrationId($this->connection->administration_id);
        }

        return $connection;
    }
}

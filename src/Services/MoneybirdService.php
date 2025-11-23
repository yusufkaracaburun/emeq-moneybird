<?php

namespace Emeq\Moneybird\Services;

use Emeq\Moneybird\Models\MoneybirdConnection;
use Emeq\Moneybird\Resources\AdministrationResource;
use Emeq\Moneybird\Resources\ContactResource;
use Emeq\Moneybird\Resources\CustomFieldResource;
use Emeq\Moneybird\Resources\DocumentResource;
use Emeq\Moneybird\Resources\EstimateResource;
use Emeq\Moneybird\Resources\LedgerResource;
use Emeq\Moneybird\Resources\SalesInvoiceResource;
use Emeq\Moneybird\Resources\TaxRateResource;
use Emeq\Moneybird\Resources\WebhookResource;
use Emeq\Moneybird\Resources\WorkflowResource;
use Picqer\Financials\Moneybird\Connection;
use Picqer\Financials\Moneybird\Moneybird;

class MoneybirdService
{
    protected ?MoneybirdConnection $connection = null;

    protected ?Moneybird $client = null;

    public function __construct(
        protected OAuthService $oauthService
    ) {}

    public function connection(?int $userId = null, ?string $tenantId = null, ?int $connectionId = null): self
    {
        $query = MoneybirdConnection::query()->where('is_active', true);

        if ($connectionId) {
            $this->connection = $query->findOrFail($connectionId);
        } elseif ($userId || $tenantId) {
            $query->where(function ($q) use ($userId, $tenantId) {
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

    public function setConnection(MoneybirdConnection $connection): self
    {
        $this->connection = $connection;
        $this->ensureValidTokens();

        return $this;
    }

    public function getClient(): Moneybird
    {
        if ($this->client) {
            return $this->client;
        }

        if (! $this->connection) {
            throw new \Emeq\Moneybird\Exceptions\MoneybirdException('No Moneybird connection set');
        }

        $connection = $this->createPicqerConnection();
        $this->client = new Moneybird($connection);

        return $this->client;
    }

    public function administrations(): AdministrationResource
    {
        return new AdministrationResource($this->getClient());
    }

    public function contacts(): ContactResource
    {
        return new ContactResource($this->getClient());
    }

    public function salesInvoices(): SalesInvoiceResource
    {
        return new SalesInvoiceResource($this->getClient());
    }

    public function estimates(): EstimateResource
    {
        return new EstimateResource($this->getClient());
    }

    public function documents(): DocumentResource
    {
        return new DocumentResource($this->getClient());
    }

    public function webhooks(): WebhookResource
    {
        return new WebhookResource($this->getClient());
    }

    public function customFields(): CustomFieldResource
    {
        return new CustomFieldResource($this->getClient());
    }

    public function ledgers(): LedgerResource
    {
        return new LedgerResource($this->getClient());
    }

    public function taxRates(): TaxRateResource
    {
        return new TaxRateResource($this->getClient());
    }

    public function workflows(): WorkflowResource
    {
        return new WorkflowResource($this->getClient());
    }

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

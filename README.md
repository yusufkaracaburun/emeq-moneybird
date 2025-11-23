# EMEQ Moneybird

Laravel package for Moneybird integration with OAuth token management and extended API features.

## Installation

You can install the package via composer:

```bash
composer require emeq/moneybird
```

The package will automatically publish the config file and migration to your application during installation. If you need to republish them manually:

```bash
# Publish config file
php artisan vendor:publish --tag="moneybird-config"

# Publish migrations
php artisan vendor:publish --tag="moneybird-migrations"
```

After installation, run the migrations:

```bash
php artisan migrate
```

## Configuration

Add the following environment variables to your `.env` file:

```env
MONEYBIRD_CLIENT_ID=your_client_id
MONEYBIRD_CLIENT_SECRET=your_client_secret
MONEYBIRD_REDIRECT_URI=https://your-app.com/moneybird/callback
MONEYBIRD_WEBHOOK_SECRET=your_webhook_secret
MONEYBIRD_API_TIMEOUT=30
```

### OAuth Setup

1. Create a Moneybird application at [https://moneybird.com/oauth/applications](https://moneybird.com/oauth/applications)
2. Set your redirect URI to match `MONEYBIRD_REDIRECT_URI`
3. Copy the Client ID and Client Secret to your `.env` file

## Usage

### Connecting to Moneybird

Use the artisan command to connect:

```bash
php artisan moneybird:connect --user-id=1
```

Or programmatically:

```php
use Emeq\Moneybird\Services\OAuthService;

$oauthService = app(OAuthService::class);
$authorizationUrl = $oauthService->getAuthorizationUrl();

// Redirect user to $authorizationUrl
// After authorization, exchange the code:
$connection = $oauthService->exchangeCodeForTokens($authorizationCode, $userId);
```

### Using the Moneybird Service

```php
use Emeq\Moneybird\Facades\Moneybird;

// Get connection for a user
$service = Moneybird::connection($userId);

// Or for a specific connection
$service = Moneybird::connection(connectionId: $connectionId);

// List administrations
$administrations = $service->administrations()->list();

// Work with contacts
$contacts = $service->contacts()->list();
$contact = $service->contacts()->create([
    'company_name' => 'Example Company',
    'email' => 'contact@example.com',
]);

// Work with sales invoices
$invoices = $service->salesInvoices()->list();
$invoice = $service->salesInvoices()->create([
    'contact_id' => $contact->id,
    'invoice_date' => now()->format('Y-m-d'),
    'details' => [
        [
            'description' => 'Product 1',
            'price' => 100.00,
            'amount' => 1,
        ],
    ],
]);

// Find invoice by invoice ID (invoice number)
$invoice = $service->salesInvoices()->findByInvoiceId('2023-001');

// Send invoice
$service->salesInvoices()->send($invoice->id);

// Work with estimates
$estimates = $service->estimates()->list();
$estimate = $service->estimates()->create([...]);

// Work with documents
$documents = $service->documents()->listGeneralDocuments();
$document = $service->documents()->createGeneralDocument([...]);

// Work with webhooks
$webhooks = $service->webhooks()->list();
$webhook = $service->webhooks()->create([
    'url' => 'https://your-app.com/moneybird/webhook',
    'enabled_events' => ['sales_invoice.created', 'contact.updated'],
]);

// Work with custom fields
$customFields = $service->customFields()->list();

// Work with ledgers
$ledgers = $service->ledgers()->list();
$ledger = $service->ledgers()->create([
    'name' => 'New Ledger',
    'account_type' => 'expenses',
], 'rgs-code');

// Work with tax rates
$taxRates = $service->taxRates()->list();

// Work with workflows
$workflows = $service->workflows()->list();
```

### Webhooks

The package automatically registers a webhook route at `/moneybird/webhook` (configurable in `config/moneybird.php`).

Listen to webhook events:

```php
use Emeq\Moneybird\Events\SalesInvoiceCreated;
use Emeq\Moneybird\Events\ContactUpdated;

Event::listen(SalesInvoiceCreated::class, function ($event) {
    // Handle sales invoice created
    $invoiceData = $event->payload;
});

Event::listen(ContactUpdated::class, function ($event) {
    // Handle contact updated
    $contactData = $event->payload;
});
```

### Commands

- `php artisan moneybird:connect` - Connect to Moneybird via OAuth
- `php artisan moneybird:test-connection` - Test an existing connection
- `php artisan moneybird:refresh-tokens` - Refresh expired tokens

## Features

- OAuth 2.0 authentication with automatic token refresh
- Database-backed token storage
- Support for multiple administrations
- Extended API features:
    - Contacts (CRUD operations)
    - Sales Invoices (create, update, send, download, findByInvoiceId)
    - Estimates (create, update, download)
    - Documents (General and Typeless documents)
    - Webhooks (create, list, delete)
    - Administrations (list, get)
    - Custom Fields (list, get)
    - Ledgers (CRUD operations)
    - Tax Rates (list, get)
    - Workflows (list, get)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
[EMEQ](https://emeq.nl)

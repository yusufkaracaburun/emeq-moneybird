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
2. Set your redirect URI to match `MONEYBIRD_REDIRECT_URI` (e.g., `https://your-app.com/moneybird/auth/callback`)
3. Copy the Client ID and Client Secret to your `.env` file

## Usage

### Connecting to Moneybird

#### Via Routes (Recommended for Web Applications)

The package provides web routes for easy OAuth connection:

1. **Start the connection**: Visit `/moneybird/connect` while authenticated
    - This route requires authentication (`auth` middleware)
    - It will redirect you to Moneybird's authorization page

2. **Handle the callback**: After authorization, Moneybird redirects to `/moneybird/auth/callback`
    - The callback route automatically exchanges the authorization code for tokens
    - On success, you'll be redirected to your dashboard with a success message
    - On error, you'll be redirected with an error message

**Example in your Blade template:**

```blade
<a href="{{ route('moneybird.connect') }}" class="btn btn-primary">
    Connect to Moneybird
</a>
```

**Example redirect in your controller:**

```php
return redirect()->route('moneybird.connect');
```

#### Via Artisan Command

Use the artisan command for CLI-based connections:

```bash
php artisan moneybird:connect --user-id=1
```

#### Programmatically

```php
use Emeq\Moneybird\Services\OAuthService;

$oauthService = app(OAuthService::class);
$authorizationUrl = $oauthService->getAuthorizationUrl();

// Redirect user to $authorizationUrl
// After authorization, exchange the code:
$connection = $oauthService->exchangeCodeForTokens($authorizationCode, $userId);
```

### Testing the Connection

#### Via Routes

After connecting, you can test the connection by accessing any route that uses the Moneybird service. The package will automatically handle token refresh if needed.

**Example test route in your application:**

```php
Route::middleware('auth')->get('/moneybird/test', function () {
    $userId = auth()->id();
    $service = \Emeq\Moneybird\Facades\Moneybird::connection($userId);

    try {
        $administrations = $service->administrations()->list();
        return response()->json([
            'success' => true,
            'message' => 'Connection successful!',
            'administrations' => $administrations,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Connection failed: ' . $e->getMessage(),
        ], 500);
    }
});
```

#### Via Artisan Command

```bash
php artisan moneybird:test-connection --user-id=1
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

// Work with webhooks
$webhooks = $service->webhooks()->list();
$webhook = $service->webhooks()->create([
    'url' => 'https://your-app.com/moneybird/webhook',
    'enabled_events' => ['sales_invoice.created', 'contact.updated'],
]);
```

### API Routes

The package provides RESTful API routes for accessing Moneybird data. All routes are prefixed with `/api/moneybird` and require Laravel Sanctum authentication.

#### Authentication

All API routes require authentication via Laravel Sanctum. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your-sanctum-token}
```

#### Connection Parameters

You can specify which Moneybird connection to use by including one of these query parameters:

- `connection_id` - Use a specific connection by ID
- `user_id` - Use the first active connection for a user
- `tenant_id` - Use the first active connection for a tenant
- If none provided, uses the first active connection

**Example:**

```
GET /api/moneybird/administrations?connection_id=1
GET /api/moneybird/contacts?user_id=5&tenant_id=tenant1
```

#### Response Format

All endpoints return JSON with the following structure:

**Success:**

```json
{
  "success": true,
  "data": { ... }
}
```

**Error:**

```json
{
    "success": false,
    "message": "Error message"
}
```

#### Available Endpoints

##### Administrations

- `GET /api/moneybird/administrations` - List all administrations
- `GET /api/moneybird/administrations/{id}` - Get a specific administration

**Example:**

```bash
curl -X GET "https://your-app.com/api/moneybird/administrations" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

##### Contacts

- `GET /api/moneybird/contacts` - List all contacts (supports filters: `firstname`, `lastname`, `company_name`, `email`)
- `GET /api/moneybird/contacts/search?q={query}` - Search contacts
- `GET /api/moneybird/contacts/{id}` - Get a specific contact
- `POST /api/moneybird/contacts` - Create a new contact
- `PUT /api/moneybird/contacts/{id}` - Update a contact
- `DELETE /api/moneybird/contacts/{id}` - Delete a contact

**Examples:**

```bash
# List contacts with filters
curl -X GET "https://your-app.com/api/moneybird/contacts?firstname=John&lastname=Doe" \
  -H "Authorization: Bearer {token}"

# Search contacts
curl -X GET "https://your-app.com/api/moneybird/contacts/search?q=john" \
  -H "Authorization: Bearer {token}"

# Create a contact
curl -X POST "https://your-app.com/api/moneybird/contacts" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "John",
    "lastname": "Doe",
    "email": "john@example.com"
  }'

# Update a contact
curl -X PUT "https://your-app.com/api/moneybird/contacts/123456" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "Jane",
    "lastname": "Doe"
  }'
```

##### Sales Invoices

- `GET /api/moneybird/sales-invoices` - List all sales invoices (supports filters: `contact_id`, `state`, `invoice_id`)
- `GET /api/moneybird/sales-invoices/by-invoice-id/{invoiceId}` - Find invoice by invoice number
- `GET /api/moneybird/sales-invoices/{id}` - Get a specific sales invoice
- `POST /api/moneybird/sales-invoices` - Create a new sales invoice
- `PUT /api/moneybird/sales-invoices/{id}` - Update a sales invoice
- `DELETE /api/moneybird/sales-invoices/{id}` - Delete a sales invoice
- `POST /api/moneybird/sales-invoices/{id}/send` - Send a sales invoice
- `GET /api/moneybird/sales-invoices/{id}/download/pdf` - Download invoice as PDF
- `GET /api/moneybird/sales-invoices/{id}/download/ubl` - Download invoice as UBL

**Examples:**

```bash
# List invoices with filters
curl -X GET "https://your-app.com/api/moneybird/sales-invoices?contact_id=123456&state=open" \
  -H "Authorization: Bearer {token}"

# Find by invoice ID
curl -X GET "https://your-app.com/api/moneybird/sales-invoices/by-invoice-id/INV-2024-001" \
  -H "Authorization: Bearer {token}"

# Create an invoice
curl -X POST "https://your-app.com/api/moneybird/sales-invoices" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "contact_id": "123456",
    "invoice_id": "INV-2024-001",
    "details": [
      {
        "description": "Product 1",
        "price": 100.00,
        "amount": 1
      }
    ]
  }'

# Send an invoice
curl -X POST "https://your-app.com/api/moneybird/sales-invoices/123456/send" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "delivery_method": "Email",
    "email_address": "customer@example.com"
  }'

# Download PDF
curl -X GET "https://your-app.com/api/moneybird/sales-invoices/123456/download/pdf" \
  -H "Authorization: Bearer {token}" \
  --output invoice.pdf
```

##### Webhooks

- `GET /api/moneybird/webhooks` - List all webhooks
- `POST /api/moneybird/webhooks` - Create a new webhook
- `DELETE /api/moneybird/webhooks/{id}` - Delete a webhook

**Examples:**

```bash
# List webhooks
curl -X GET "https://your-app.com/api/moneybird/webhooks" \
  -H "Authorization: Bearer {token}"

# Create a webhook
curl -X POST "https://your-app.com/api/moneybird/webhooks" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://your-app.com/moneybird/webhook",
    "events": ["sales_invoice.created", "contact.updated"]
  }'

# Delete a webhook
curl -X DELETE "https://your-app.com/api/moneybird/webhooks/123456" \
  -H "Authorization: Bearer {token}"
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
    - Webhooks (create, list, delete)
    - Administrations (list, get)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
[EMEQ](https://emeq.nl)

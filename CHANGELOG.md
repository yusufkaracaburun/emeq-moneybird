# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-01-23

### Added

- OAuth 2.0 authentication with automatic token refresh
- Database-backed token storage with `moneybird_connections` table
- Support for multiple administrations per user/tenant
- Resource classes for Moneybird API endpoints:
  - Administrations (list, get)
  - Contacts (CRUD operations, search)
  - Sales Invoices (create, update, send, download)
  - Webhooks (create, list, delete)
- Artisan commands:
  - `moneybird:connect` - Connect to Moneybird via OAuth
  - `moneybird:test-connection` - Test an existing connection
  - `moneybird:refresh-tokens` - Refresh expired tokens
- Webhook handling with signature validation
- Laravel events for webhook types (SalesInvoiceCreated, ContactUpdated, etc.)
- Auto-publishing of config and migration files on package installation
- Moneybird facade for easy access to the service
- Support for tenant-based multi-tenancy
- API routes for Moneybird features (administrations, contacts, sales invoices, webhooks)
- Feature-specific controllers (AdministrationController, ContactController, SalesInvoiceController, WebhookApiController)
- Shared trait for common controller logic (GetsMoneybirdService)

### Fixed

- Fixed file paths in MoneybirdServiceProvider for correct Packagist installation
- Fixed GitHub workflow failures by correcting package file paths
- Added file existence checks before loading routes to prevent errors during package discovery
- Added memory limit to PHPStan workflow to prevent memory issues

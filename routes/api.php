<?php

use Emeq\Moneybird\Http\Controllers\AdministrationController;
use Emeq\Moneybird\Http\Controllers\ContactController;
use Emeq\Moneybird\Http\Controllers\SalesInvoiceController;
use Emeq\Moneybird\Http\Controllers\WebhookApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('moneybird')->name('api.moneybird.')->middleware('auth:sanctum')->group(function (): void {
    Route::prefix('administrations')->name('administrations.')->group(function (): void {
        Route::get('/', [AdministrationController::class, 'index'])->name('index');
        Route::get('/{id}', [AdministrationController::class, 'show'])->name('show');
    });

    Route::prefix('contacts')->name('contacts.')->group(function (): void {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/search', [ContactController::class, 'search'])->name('search');
        Route::get('/{id}', [ContactController::class, 'show'])->name('show');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::put('/{id}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContactController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('sales-invoices')->name('sales-invoices.')->group(function (): void {
        Route::get('/', [SalesInvoiceController::class, 'index'])->name('index');
        Route::get('/by-invoice-id/{invoiceId}', [SalesInvoiceController::class, 'findByInvoiceId'])->name('by-invoice-id');
        Route::get('/{id}', [SalesInvoiceController::class, 'show'])->name('show');
        Route::post('/', [SalesInvoiceController::class, 'store'])->name('store');
        Route::put('/{id}', [SalesInvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [SalesInvoiceController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/send', [SalesInvoiceController::class, 'send'])->name('send');
        Route::get('/{id}/download/pdf', [SalesInvoiceController::class, 'downloadPdf'])->name('download.pdf');
        Route::get('/{id}/download/ubl', [SalesInvoiceController::class, 'downloadUbl'])->name('download.ubl');
    });

    Route::prefix('webhooks')->name('webhooks.')->group(function (): void {
        Route::get('/', [WebhookApiController::class, 'index'])->name('index');
        Route::post('/', [WebhookApiController::class, 'store'])->name('store');
        Route::delete('/{id}', [WebhookApiController::class, 'destroy'])->name('destroy');
    });
});

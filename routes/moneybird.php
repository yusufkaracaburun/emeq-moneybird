<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('moneybird')->name('moneybird.')->group(function (): void {
    Route::middleware('auth')->group(function (): void {
        Route::get('/auth/callback', function (Request $request) {
            $code = $request->query('code');
            $error = $request->query('error');

            if ($error) {
                return redirect()->route('dashboard')->with('error', 'Moneybird authorization failed: '.$error);
            }

            if (! $code) {
                return redirect()->route('dashboard')->with('error', 'Missing authorization code');
            }

            try {
                $userId = auth()->id();

                if (! $userId) {
                    return redirect()->route('dashboard')->with('error', 'You must be logged in to connect Moneybird');
                }

                $oauthService = app(\Emeq\Moneybird\Services\OAuthService::class);
                $administrationId = $request->query('administration_id');

                $connection = $oauthService->exchangeCodeForTokens(
                    $code,
                    $userId,
                    null,
                    $administrationId
                );

                return redirect()->route('dashboard')->with('success', 'Moneybird connection established successfully!');
            } catch (\Exception $e) {
                return redirect()->route('dashboard')->with('error', 'Failed to connect to Moneybird: '.$e->getMessage());
            }
        })->name('auth.callback');

        Route::get('/connect', function () {
            $oauthService = app(\Emeq\Moneybird\Services\OAuthService::class);
            $authorizationUrl = $oauthService->getAuthorizationUrl();

            return redirect($authorizationUrl);
        })->name('connect');
    });
});

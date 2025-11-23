<?php

use Emeq\Moneybird\Services\OAuthService;

it('can generate authorization url', function () {
    config()->set('moneybird.oauth.client_id', 'test_client_id');
    config()->set('moneybird.oauth.client_secret', 'test_secret');
    config()->set('moneybird.oauth.redirect_uri', 'https://example.com/callback');
    config()->set('moneybird.oauth.scopes', ['sales_invoices']);

    $oauthService = app(OAuthService::class);
    $url = $oauthService->getAuthorizationUrl('test_state');

    expect($url)->toBeString()
        ->and($url)->toContain('moneybird.com/oauth/authorize')
        ->and($url)->toContain('client_id=test_client_id')
        ->and($url)->toContain('state=test_state');
});

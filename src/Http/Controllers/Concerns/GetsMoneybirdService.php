<?php

namespace Emeq\Moneybird\Http\Controllers\Concerns;

use Emeq\Moneybird\Facades\Moneybird;
use Emeq\Moneybird\Services\MoneybirdService;
use Illuminate\Http\Request;

trait GetsMoneybirdService
{
    /**
     * Get Moneybird service instance based on request parameters.
     */
    protected function getService(Request $request): MoneybirdService
    {
        $user = $request->user();
        $userId = $user ? $user->id : $request->input('user_id');
        $tenantId = $request->input('tenant_id');
        $connectionId = $request->input('connection_id');

        if ($connectionId) {
            return Moneybird::connection(connectionId: $connectionId);
        }

        if ($userId) {
            return Moneybird::connection(userId: $userId);
        }

        if ($tenantId) {
            return Moneybird::connection(tenantId: $tenantId);
        }

        return Moneybird::connection();
    }
}

<?php

namespace Emeq\Moneybird\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Emeq\Moneybird\Services\MoneybirdService
 */
class Moneybird extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Emeq\Moneybird\Services\MoneybirdService::class;
    }
}

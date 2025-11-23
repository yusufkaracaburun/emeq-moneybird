<?php

namespace Emeq\Moneybird\Exceptions;

class EntityUpdateException extends \RuntimeException
{
    public function __construct(string $message = 'Failed to update entity in Moneybird.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

<?php

namespace Emeq\Moneybird\Exceptions;

class EntityCreationException extends \RuntimeException
{
    public function __construct(string $message = 'Failed to create entity in Moneybird.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

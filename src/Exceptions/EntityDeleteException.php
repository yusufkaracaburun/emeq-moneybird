<?php

namespace Emeq\Moneybird\Exceptions;

class EntityDeleteException extends \RuntimeException
{
    public function __construct(string $message = 'Failed to delete entity in Moneybird.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

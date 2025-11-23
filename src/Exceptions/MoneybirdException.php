<?php

namespace Emeq\Moneybird\Exceptions;

class MoneybirdException extends \RuntimeException
{
    public function __construct(string $message = 'An error occurred while communicating with Moneybird API.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

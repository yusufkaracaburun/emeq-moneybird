<?php

namespace Emeq\Moneybird\Exceptions;

class TooManyRequestsException extends \RuntimeException
{
    public function __construct(string $message = 'Too many requests to Moneybird API. Please try again later.', int $code = 429, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

<?php

namespace Emeq\Moneybird\Exceptions;

class ConnectionErrorException extends MoneybirdException
{
    protected ?string $curlErrorNumber = null;

    protected ?string $curlErrorString = null;

    public function __construct(string $message = 'Failed to connect to Moneybird API.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getCurlErrorNumber(): ?string
    {
        return $this->curlErrorNumber;
    }

    public function setCurlErrorNumber(?string $curlErrorNumber): self
    {
        $this->curlErrorNumber = $curlErrorNumber;

        return $this;
    }

    public function getCurlErrorString(): ?string
    {
        return $this->curlErrorString;
    }

    public function setCurlErrorString(?string $curlErrorString): self
    {
        $this->curlErrorString = $curlErrorString;

        return $this;
    }
}

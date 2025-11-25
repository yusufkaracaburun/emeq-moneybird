<?php

namespace Emeq\Moneybird\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MoneybirdWebhookReceived
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public string $eventType,
        public array $payload
    ) {}
}

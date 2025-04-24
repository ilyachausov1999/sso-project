<?php

declare(strict_types = 1);

namespace App\Dto\Nats;

readonly class SendMessageDto
{
    public function __construct(
        private string $event,
        private string $subject,
        private array  $payload,
    ) {}

    public function getMessage(): string
    {
        return serialize(
            [
                'headers' => ['event' => $this->event],
                'body' => json_encode($this->payload),
            ]
        );
    }

    public function getSubject(): string
    {
        return $this->subject;
    }
}

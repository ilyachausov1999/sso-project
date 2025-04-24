<?php

declare(strict_types = 1);

namespace App\Services\Nats;

use App\Dto\Nats\SendMessageDto;
use Basis\Nats\Client;

readonly class JetStreamService
{
    const STREAM_BACKEND = 'backend';
    const SUBJECT_BACKEND = 'backend.topic1';
    public function __construct(
        private Client $client
    ) {}

    public function send(SendMessageDto $messageDto): void
    {
        $stream = $this->client->getApi()->getStream(self::STREAM_BACKEND);
        $stream->publish($messageDto->getSubject(), $messageDto->getMessage());
    }
}

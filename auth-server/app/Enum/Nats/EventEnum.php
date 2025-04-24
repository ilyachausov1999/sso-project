<?php

declare(strict_types = 1);

namespace App\Enum\Nats;

enum EventEnum: string
{
    /** @var string */
    case LOGIN = 'login';
    case REGISTERED = 'registerer';
}

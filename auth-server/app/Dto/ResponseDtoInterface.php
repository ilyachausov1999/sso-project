<?php

declare(strict_types = 1);

namespace App\Dto;

interface ResponseDtoInterface
{
    public function toArray(): array;
}

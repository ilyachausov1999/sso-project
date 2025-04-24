<?php

declare(strict_types = 1);

namespace App\Dto\User\Response;

use App\Dto\ResponseDtoInterface;

class UserResponseDto implements ResponseDtoInterface
{
    public function __construct(
        private readonly string|null $accessToken = null,
        private readonly string|null $refreshToken = null,
        private bool                 $status = true,
        private array|null           $errors = null,
    ) {}

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'errors' => $this->errors
        ];
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
}

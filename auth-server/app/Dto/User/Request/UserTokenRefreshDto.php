<?php

declare(strict_types = 1);

namespace App\Dto\User\Request;

use App\DTO\RequestDtoInterface;
use Illuminate\Http\Request;

readonly class UserTokenRefreshDto implements RequestDtoInterface
{
    private function __construct(
        private string $refreshToken
    ) {}

    /**
     * @inheritDoc
     */
    public static function fromRequest(Request $request): static
    {
        $userData = $request->validate([
            'refresh_token' => ['required', 'string']
        ]);

        return new static($userData['refresh_token']);
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}

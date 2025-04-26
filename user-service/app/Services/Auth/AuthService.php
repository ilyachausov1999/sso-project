<?php

declare(strict_types = 1);

namespace App\Services\Auth;

use App\Http\Integrations\AuthServer\AuthServerClient;
use Exception;

readonly class AuthService
{
    public function __construct(
        private AuthServerClient $authClient
    ) {}

    /**
     * @throws Exception
     */
    public function refreshToken(string $refreshToken): false|array
    {
        $responseData = $this->authClient->refreshToken(['refresh_token' => $refreshToken]);

        if ($responseData['status']) {
            return [
                'access_token' => $responseData['access_token'],
                'refresh_token' => $responseData['refresh_token'],
            ];
        }

        return false;
    }
}

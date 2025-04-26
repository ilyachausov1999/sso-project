<?php

declare(strict_types = 1);

namespace App\Dto\Token;

use Lcobucci\JWT\Token;
use stdClass;

class AuthTokenDto
{
    public function __construct(
        public Token $parsedAccessToken,
        public string|null $accessToken = null,
        public string|null $refreshToken = null,
    ) {}

    public function getUserFromToken(): stdClass
    {
        $user = new stdClass();
        $user->id = $this->parsedAccessToken->claims()->get('u_id');
        $user->name = $this->parsedAccessToken->claims()->get('u_name');
        $user->login = $this->parsedAccessToken->claims()->get('u_login');
        $user->email = $this->parsedAccessToken->claims()->get('u_email');
        $user->is_admin = $this->parsedAccessToken->claims()->get('is_admin');
        $user->access_token = $this->accessToken;
        $user->refresh_token = $this->refreshToken;

        return $user;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }
}

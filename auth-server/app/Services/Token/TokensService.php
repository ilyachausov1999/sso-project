<?php

declare(strict_types = 1);

namespace App\Services\Token;

use App\Models\User;
use DateTimeImmutable;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use RedisException;

readonly class TokensService
{
    public function __construct(
        private Configuration $configuration,
        private mixed         $redis
    ) {}

    public function getUserIdByRefreshToken(string $refreshToken): int
    {
        return (int) $this->redis->get($refreshToken);
    }

    /**
     * @throws RedisException
     */
    public function createRefreshToken(User $user, string $oldRefreshToken = null): string
    {
        if ($oldRefreshToken) {
            $this->redis->del($oldRefreshToken);
        }

        $refreshToken = (string) Str::uuid();
        $this->redis->setex($refreshToken, $this->getCacheTill(), $user->id);

        return $refreshToken;
    }

    public function createAccessToken(User $user): string
    {
        $now = new DateTimeImmutable();

        $token = $this->configuration->builder()
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('u_id', $user->id)
            ->withClaim('u_name', $user->name)
            ->withClaim('u_login', $user->login)
            ->withClaim('u_email', $user->email)
            ->withClaim('is_admin', $user->is_admin)
            ->getToken($this->configuration->signer(), $this->configuration->signingKey());

        return $token->toString();
    }

    private function getCacheTill(): float|int
    {
        return 60 * 60 * 24;
    }
}

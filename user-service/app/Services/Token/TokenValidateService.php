<?php

declare(strict_types = 1);

namespace App\Services\Token;

use App\Dto\Token\AuthTokenDto;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;

readonly class TokenValidateService
{
    public function __construct(
        private Configuration $configuration,
        private Validator $validator
    ) {}

    public function validateToken(string $accessToken): AuthTokenDto|bool
    {
        try {
            $parsedToken = $this->configuration->parser()->parse($accessToken);
        } catch (CannotDecodeContent $e) {
            return false;
        }

        $now = new DateTimeImmutable();
        if ($parsedToken->isExpired($now)) {
            return false;
        }

        $constraints = [new SignedWith($this->configuration->signer(), $this->configuration->verificationKey())];

        if ($this->validator->validate($parsedToken, ...$constraints)) {
            return new AuthTokenDto($parsedToken);
        } else {
            return false;
        }
    }
}

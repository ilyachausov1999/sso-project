<?php

declare(strict_types = 1);

namespace App\Services\Token;

use App\Models\User;
use DateTimeImmutable;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;
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





        $token2 = $configuration->parser()->parse($token1);

        $constraints = [
//            new StrictValidAt(SystemClock::fromSystemTimezone()),
            new SignedWith($configuration->signer(), $configuration->verificationKey())
        ];

        $validator = new Validator();

        if ($validator->validate($token2, ...$constraints)) {
            echo "Токен действительный!";
            $uid = $token2->claims()->get('uid');
            echo "User ID: " . $uid;
        } else {
            echo "Токен недействителен!";
        }


        die();



        $parser = new Parser(new JoseEncoder());

        $token = $parser->parse($token);

        var_dump($token1->signature());
//        var_dump($token1->toString());


//        var_dump($token->payload());
        var_dump($token->signature());

//        var_dump($token->claims()->all());
        die();

        $validator = new Validator();

        try {
            $validator->assert($token, new RelatedTo('1234567891')); // doesn't throw an exception
            $validator->assert($token, new RelatedTo('1234567890'));
        } catch (RequiredConstraintsViolated $e) {
            // list of constraints violation exceptions:
            var_dump($e->violations());
        }
    }

    private function getCacheTill(): float|int
    {
        return 60 * 60 * 24;
    }
}

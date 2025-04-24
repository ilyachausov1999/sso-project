<?php

namespace App\Providers;

use App\Services\Nats\JetStreamService;
use App\Services\Token\TokensService;
use Basis\Nats\Client;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Basis\Nats\Configuration as NatsConfiguration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $algorithm = new Sha256();
        $signingKey = InMemory::base64Encoded(config('auth.signing_key'));
        $configuration = Configuration::forSymmetricSigner($algorithm, $signingKey);

        $this->app->singleton(TokensService::class,
            fn() => new TokensService(
                $configuration,
                Redis::connection()->client()
            )
        );

        $configuration = new NatsConfiguration(
            host: config('nats.host'),
            port: config('nats.port'),
            nkey: config('nats.nkey')
        );

        $this->app->singleton(JetStreamService::class,
            fn() => new JetStreamService(
                new Client($configuration)
            )
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use App\Http\Integrations\AuthServer\AuthServerClient;
use App\Services\Token\TokenValidateService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Validator;

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

        $this->app->singleton(TokenValidateService::class,
            fn() => new TokenValidateService(
                $configuration,
                new Validator()
            )
        );

        $this->app->singleton(
            AuthServerClient::class,
            fn() => new AuthServerClient(
                Http::baseUrl(config('services.auth_service.url'))
                    ->timeout(15)
                    ->asJson()
                    ->acceptJson()
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

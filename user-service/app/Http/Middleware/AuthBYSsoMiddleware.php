<?php

namespace App\Http\Middleware;

use App\Dto\Token\AuthTokenDto;
use App\Services\Auth\AuthService;
use App\Services\Token\TokenValidateService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class AuthBYSsoMiddleware
{

    public function __construct(
        private TokenValidateService $tokenValidateService,
        private AuthService          $authService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     * @throws HttpException
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->bearerToken();
        $refreshToken = $request->headers->get('RefreshToken');
        $tokenDto = $this->tokenValidateService->validateToken($accessToken);

        if ($tokenDto) {
            $tokenDto->setAccessToken($accessToken);
            $tokenDto->setRefreshToken($refreshToken);
            $request = $this->setUser($request, $tokenDto);
            return $next($request);
        }

        $token = $this->authService->refreshToken($request->headers->get('RefreshToken'));

        if ($token) {
            $tokenDto = $this->tokenValidateService->validateToken($token['access_token']);

            $tokenDto->setAccessToken($token['access_token']);
            $tokenDto->setRefreshToken($token['refresh_token']);
            $request = $this->setUser($request, $tokenDto);
            return $next($request);
        }

        throw new HttpException(
            Response::HTTP_FORBIDDEN,
            'Авторизация истекла, войдите заново в ручную!',
        );
    }

    private function setUser(Request $request, AuthTokenDto $tokenDto): Request
    {
        $user['user'] = $tokenDto->getUserFromToken();
        $request->attributes->add($user);
        return $request;
    }
}

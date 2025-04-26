<?php

declare(strict_types = 1);

namespace App\Http\Integrations\AuthServer;

use App\Http\Integrations\AbstractConnector;
use Exception;

readonly class AuthServerClient extends AbstractConnector
{
    /**
     * @param array $requestData
     * @return array
     * @throws Exception
     */
    public function refreshToken(array $requestData): array
    {
        return [
            'status' => true,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDU2ODc4NjguMTExOTE4LCJleHAiOjE3NDU2OTE0NjguMTExOTE4LCJ1X2lkIjo0LCJ1X25hbWUiOiJJbHlhIiwidV9sb2dpbiI6InpvcGluMTk5OCIsInVfZW1haWwiOiJjaGF1c292XzE5OThAbWFpbC5ydSIsImlzX2FkbWluIjowfQ.eYq196DzDPG7KiPLOEvzwK6pg-Kfi-J0pc8XAgItUvM',
            'refresh_token' => 'c6b3fb39-6770-443b-a276-2f5bdd31e4f1',
            'errors' => []
        ];
//        return $this->send(
//            method: 'POST',
//            uri: '/refresh',
//            requestBody: $requestData
//        )->json();
    }
}

<?php

declare(strict_types = 1);

namespace App\Http\Integrations;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

abstract readonly class AbstractConnector
{
    public function __construct(
        private PendingRequest $request,
    ) {}

    /**
     * Метод отправки запроса
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     * @param array $requestBody
     * @param array $headers
     * @param int $unicode
     * @return Response
     *
     * @throws Exception
     */
    public function send(
        string $method,
        string $uri,
        array $options = [],
        array $requestBody = [],
        array $headers = [],
        int $unicode = 0
    ): Response
    {
        if (!empty($requestBody)) {
            $this->request->withBody(json_encode($requestBody, $unicode));
        }

        if (!empty($headers)) {
            $this->request->withHeaders($headers);
        }

        return $this->request->send(
            $method,
            $uri,
            $options,
        );
    }
}

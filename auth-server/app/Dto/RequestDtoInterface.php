<?php

declare(strict_types = 1);

namespace App\Dto;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

interface RequestDtoInterface
{
    /**
     * Валидация и парсинг параметров, пришедших из запроса
     *
     * @param Request $request
     * @return static
     * @throws ValidationException
     */
    public static function fromRequest(Request $request): static;
}

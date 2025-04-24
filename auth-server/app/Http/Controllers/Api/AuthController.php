<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api;

use App\Dto\User\Request\UserLoginDto;
use App\Dto\User\Request\UserRegisterDto;
use App\Dto\User\Request\UserTokenRefreshDto;
use App\Dto\User\Response\UserResponseDto;
use App\Http\Controllers\Controller;
use App\Services\Auth\AuthorizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use RedisException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthorizationService $authorizationService
    ) {}


    /**
     * Эндпоинт для регистрации пользователей
     *
     * @OA\Post(
     *     path="/api/v1/register",
     *     tags={"Аутентификация"},
     *     description="Регистрация пользователей",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     description="Имя пользователя",
     *                     type="string",
     *                     example="John Doe",
     *                 ),
     *                 @OA\Property(
     *                     property="login",
     *                     description="Логин пользователя",
     *                     type="string",
     *                     example="johnDoe123",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="Email пользователя",
     *                     type="string",
     *                     example="example@example.ru",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Пароль пользователя",
     *                     type="string",
     *                     example="qwerty123",
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="Подтверждение пароля",
     *                     type="string",
     *                     example="qwerty123",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Успешная регистрация",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="boolean",
     *                         example="true",
     *                     ),
     *                     @OA\Property(
     *                         property="access_token",
     *                         type="string",
     *                         example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9",
     *                     ),
     *                     @OA\Property(
     *                         property="refresh_token",
     *                         type="string",
     *                         example="23f24589-adf3-42d4-b632-30e7d94f9db5",
     *                     ),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                     @OA\Property(
     *                         property="email",
     *                         type="object",
     *                         @OA\Property(property="message", type="string", example="Указанный email не уникальный и уже кем-то используется."),
     *                         )
     *                     ),
     *                 ),
     *             ),
     *         }
     *     ),
     *     @OA\Response(response="400", description="Ошибки валидации входных параметров")
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws RedisException
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $userDto = UserRegisterDto::fromRequest($request);
        } catch (ValidationException $e) {
            $response = new UserResponseDto();
            $response->setStatus(false);
            $response->setErrors($e->validator->errors()->toArray());
            return response()->json(
                $response->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        }


        return response()->json(
            $this->authorizationService->register($userDto)->toArray(),
            Response::HTTP_CREATED
        );
    }

    /**
     * Эндпоинт для аутентификации пользователей
     *
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Аутентификация"},
     *     description="Аутентификация пользователей",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     description="Email пользователя",
     *                     type="string",
     *                     example="example@example.ru",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Пароль пользователя",
     *                     type="string",
     *                     example="qwerty123",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Успешная регистрация",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="boolean",
     *                         example="true",
     *                     ),
     *                     @OA\Property(
     *                         property="access_token",
     *                         type="string",
     *                         example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9",
     *                     ),
     *                     @OA\Property(
     *                         property="refresh_token",
     *                         type="string",
     *                         example="23f24589-adf3-42d4-b632-30e7d94f9db5",
     *                     ),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                     @OA\Property(
     *                         property="email",
     *                         type="object",
     *                         @OA\Property(property="message", type="string", example="Указанный email не уникальный и уже кем-то используется."),
     *                         )
     *                     ),
     *                 ),
     *             ),
     *         }
     *     ),
     *     @OA\Response(response="400", description="Ошибки валидации входных параметров"),
     *     @OA\Response(response="401", description="Некорректный логин или пароль"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws RedisException
     */
    public function login(Request $request): JsonResponse
    {
        $response = new UserResponseDto();
        try {
            $response = $this->authorizationService->login(UserLoginDto::fromRequest($request));
        } catch (ValidationException $e) {
            $response->setStatus(false);
            $response->setErrors($e->validator->errors()->toArray());
            return response()->json(
                $response->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        } catch (HttpException $e) {
            $response->setStatus(false);
            $response->setErrors([$e->getMessage()]);
            return response()->json(
                $response->toArray(),
                $e->getStatusCode()
            );
        }

        return response()->json($response->toArray());
    }

    /**
     * Эндпоинт для обновления токена
     *
     * @OA\Post(
     *     path="/api/v1/refresh",
     *     tags={"Аутентификация"},
     *     description="Обновление токена",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="refresh_token",
     *                     description="Refresh token",
     *                     type="string",
     *                     example="7615dee3-1391-4ce2-ae7c-38f42cff4f77",
     *                 )
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Успешная регистрация",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="boolean",
     *                         example="true",
     *                     ),
     *                     @OA\Property(
     *                         property="access_token",
     *                         type="string",
     *                         example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9",
     *                     ),
     *                     @OA\Property(
     *                         property="refresh_token",
     *                         type="string",
     *                         example="23f24589-adf3-42d4-b632-30e7d94f9db5",
     *                     ),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                     @OA\Property(
     *                         property="email",
     *                         type="object",
     *                         @OA\Property(property="message", type="string", example="Указанный email не уникальный и уже кем-то используется."),
     *                         )
     *                     ),
     *                 ),
     *             ),
     *         }
     *     ),
     *     @OA\Response(response="401", description="refresh_token устарел или невалиден")
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws RedisException
     */
    public function refresh(Request $request): JsonResponse
    {
        $response = new UserResponseDto();
        try {
            $response = $this->authorizationService->refresh(UserTokenRefreshDto::fromRequest($request));
        } catch (ValidationException $e) {
            $response->setStatus(false);
            $response->setErrors($e->validator->errors()->toArray());
            return response()->json(
                $response->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        } catch (HttpException $e) {
            $response->setStatus(false);
            $response->setErrors([$e->getMessage()]);
            return response()->json(
                $response->toArray(),
                $e->getStatusCode()
            );
        }

        return response()->json($response->toArray());
    }

    /**
     * Эндпоинт авторизации через Google
     *
     * @OA\Get(
     *     path="/api/v1/auth/google-redirect",
     *     tags={"Аутентификация"},
     *     description="Авторизации через Google",
     *     @OA\Response(
     *         response="200",
     *         description="Успешная регистрация",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="boolean",
     *                         example="true",
     *                     ),
     *                     @OA\Property(
     *                         property="access_token",
     *                         type="string",
     *                         example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9",
     *                     ),
     *                     @OA\Property(
     *                         property="refresh_token",
     *                         type="string",
     *                         example="23f24589-adf3-42d4-b632-30e7d94f9db5",
     *                     ),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                     @OA\Property(
     *                         property="email",
     *                         type="object",
     *                         @OA\Property(property="message", type="string", example="Указанный email не уникальный и уже кем-то используется."),
     *                         )
     *                     ),
     *                 ),
     *             ),
     *         }
     *     ),
     *  )
     *
     * @return RedirectResponse
     */
    public function googleRedirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * @throws RedisException
     */
    public function googleCallback(): JsonResponse
    {
        $user = Socialite::driver('google')->user();

        $response = $this->authorizationService->loginByGoogle($user);

        return response()->json($response->toArray());
    }
}

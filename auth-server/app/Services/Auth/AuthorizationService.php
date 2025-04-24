<?php

declare(strict_types = 1);

namespace App\Services\Auth;

use App\Dto\Nats\SendMessageDto;
use App\Dto\User\Request\UserLoginDto;
use App\Dto\User\Request\UserRegisterDto;
use App\Dto\User\Request\UserTokenRefreshDto;
use App\Dto\User\Response\UserResponseDto;
use App\Models\User;
use App\Repository\User\UserRepository;
use App\Services\Nats\JetStreamService;
use App\Services\Token\TokensService;
use App\Enum\Nats\EventEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RedisException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Laravel\Socialite\Contracts\User as SocialiteUser;

readonly class AuthorizationService
{
    public function __construct(
        private UserRepository $userRepository,
        private TokensService  $tokensService,
        private JetStreamService $jetStreamService
    ) {}

    /**
     * @param UserRegisterDto $userDto
     * @return UserResponseDto
     * @throws RedisException
     */
    public function register(UserRegisterDto $userDto): UserResponseDto
    {
        $user = $this->userRepository->create($userDto);
        $accessToken = $this->tokensService->createAccessToken($user);
        $refreshToken = $this->tokensService->createRefreshToken($user);

        $yourDataPayload = [
            $user->id => 'Вы успешно зарегестрировались!'
        ];

        $messageDto = new SendMessageDto(
            EventEnum::LOGIN->value,
            JetStreamService::SUBJECT_BACKEND,
            $yourDataPayload,
        );

        $this->jetStreamService->send($messageDto);

        return new UserResponseDto(
            $accessToken,
            $refreshToken
        );
    }

    /**
     * @param UserLoginDto $userDto
     * @return UserResponseDto
     * @throws HttpException|RedisException
     */
    public function login(UserLoginDto $userDto): UserResponseDto
    {
        if (!Auth::attempt($userDto->toArray())) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, "Неправильный логин или пароль");
        }

        /** @var User $user */
        $user = Auth::user();
        $accessToken = $this->tokensService->createAccessToken($user);
        $refreshToken = $this->tokensService->createRefreshToken($user);

        $yourDataPayload = [
            $user->id => 'Вы успешно авторизовались!'
        ];

        $messageDto = new SendMessageDto(
            EventEnum::LOGIN->value,
            JetStreamService::SUBJECT_BACKEND,
            $yourDataPayload,
        );

        $this->jetStreamService->send($messageDto);

        return new UserResponseDto(
            $accessToken,
            $refreshToken
        );
    }

    /**
     * @throws RedisException
     */
    public function refresh(UserTokenRefreshDto $refreshDto): UserResponseDto
    {
        $userId = $this->tokensService->getUserIdByRefreshToken($refreshDto->getRefreshToken());

        if (!$userId) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, "refresh_token устарел или невалиден");
        }

        $user = $this->userRepository->getById($userId);
        $accessToken = $this->tokensService->createAccessToken($user);
        $refreshToken = $this->tokensService->createRefreshToken($user, $refreshDto->getRefreshToken());

        return new UserResponseDto(
            $accessToken,
            $refreshToken
        );
    }

    /**
     * @throws RedisException
     */
    public function loginByGoogle(SocialiteUser $userGoogle): UserResponseDto
    {
        $user = $this->userRepository->getByGoogleId($userGoogle->getId());

        if (!$user) {
            $user = $this->userRepository->getByEmail($userGoogle->getEmail());

            if ($user) {
                $user->google_id = $userGoogle->getId();
                $user->save();
            } else {
                $userDto = new UserRegisterDto(
                    $userGoogle->getName(),
                    $userGoogle->getNickname() ? $userGoogle->getNickname()  : $userGoogle->getName(),
                    $userGoogle->getEmail(),
                    Hash::make('123456789'),
                    $userGoogle->getId()
                );

                $user = $this->userRepository->create($userDto);
            }
        }

        $accessToken = $this->tokensService->createAccessToken($user);
        $refreshToken = $this->tokensService->createRefreshToken($user);

        return new UserResponseDto(
            $accessToken,
            $refreshToken
        );
    }
}

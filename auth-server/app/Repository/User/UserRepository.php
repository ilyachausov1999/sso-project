<?php

declare(strict_types = 1);

namespace App\Repository\User;

use App\Dto\User\Request\UserRegisterDto;
use App\Models\User;

class UserRepository
{
    /**
     * @param UserRegisterDto $userDto
     * @return User
     */
    public function create(UserRegisterDto $userDto): User
    {
        return User::query()->create([
            'name' => $userDto->getName(),
            'login' => $userDto->getLogin(),
            'email' => $userDto->getEmail(),
            'password' => $userDto->getPassword(),
            'google_id' => $userDto->getGoogleId()
        ]);
    }

    public function getById(int $id): User|null
    {
        return User::query()->find($id);
    }

    public function getByGoogleId(string $getId): User|null
    {
        return User::query()->where('google_id', '=', $getId)->first();
    }

    public function getByEmail(string $email): User|null
    {
        return User::query()->where('email', '=', $email)->first();
    }
}

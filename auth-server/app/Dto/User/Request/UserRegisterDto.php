<?php

declare(strict_types = 1);

namespace App\Dto\User\Request;

use App\DTO\RequestDtoInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

readonly class UserRegisterDto implements RequestDtoInterface
{
    public function __construct(
        private string $name,
        private string $login,
        private string $email,
        private string $password,
        private string|null $googleId = null,
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getGoogleId(): string|null
    {
        return $this->googleId;
    }

    /**
     * @inheritDoc
     */
    public static function fromRequest(Request $request): static
    {
        $userData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'login' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'password_confirmation' => ['required', 'same:password', 'min:8', 'max:255']
        ]);

        return new static(
            $userData['name'],
            $userData['login'],
            $userData['email'],
            Hash::make($userData['password']),
            null
        );
    }
}

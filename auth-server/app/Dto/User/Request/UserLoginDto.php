<?php

declare(strict_types = 1);

namespace App\Dto\User\Request;

use App\DTO\RequestDtoInterface;
use Illuminate\Http\Request;

readonly class UserLoginDto implements RequestDtoInterface
{
    private function __construct(
        private string $email,
        private string $password
    ) {}

    /**
     * @inheritDoc
     */
    public static function fromRequest(Request $request): static
    {
        $userData = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        return new static(
            $userData['email'],
            $userData['password']
        );
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}

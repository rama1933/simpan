<?php

namespace App\Data\User;

use App\Data\BaseDTO;

class UserDTO extends BaseDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?array $roles = null
    ) {}

    public static function fromModel($model): static
    {
        return new static(
            name: $model->name,
            email: $model->email,
            roles: $model->getRoleNames()->toArray()
        );
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'roles' => 'array'
        ];
    }

    public function getFillable(): array
    {
        return ['name', 'email', 'password'];
    }
}

<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new User());
    }

    public function getByRole(string $role)
    {
        return $this->model->role($role)->get();
    }

    public function getWithRoles()
    {
        return $this->model->with('roles')->get();
    }

    public function updateRoles(int $userId, array $roles)
    {
        $user = $this->find($userId);
        $user->syncRoles($roles);
        return $user;
    }
}

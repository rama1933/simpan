<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getByRole(string $role);
    public function getWithRoles();
    public function updateRoles(int $userId, array $roles);
}

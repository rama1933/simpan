<?php

namespace App\Services\User;

use App\Services\BaseService;
use App\Repositories\User\UserRepositoryInterface;
use App\Data\User\UserDTO;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function createUser(UserDTO $data)
    {
        $userData = $data->toArray();
        $userData['password'] = Hash::make($data->password);
        
        $user = $this->userRepository->create($userData);
        
        if ($data->roles) {
            $user->assignRoles($data->roles);
        }
        
        return $this->success('User berhasil dibuat', $user);
    }

    public function updateUser(int $id, UserDTO $data)
    {
        $userData = $data->toArray();
        
        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($data->password);
        }
        
        $user = $this->userRepository->update($id, $userData);
        
        if ($data->roles) {
            $user->syncRoles($data->roles);
        }
        
        return $this->success('User berhasil diupdate', $user);
    }

    public function getUsersByRole(string $role)
    {
        $users = $this->userRepository->getByRole($role);
        return $this->success('Users berhasil diambil', $users);
    }
}

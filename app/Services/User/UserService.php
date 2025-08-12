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
    ) {
    }

    public function createUser(UserDTO $data)
    {
        $userData = $data->toArray();
        $userData['password'] = Hash::make($data->password);
        // Pastikan tidak mengirim key 'roles' ke repository/model
        unset($userData['roles']);

        $user = $this->userRepository->create($userData);

        // Selalu sync roles, meskipun array kosong
        if (isset($data->roles)) {
            $user->syncRoles($data->roles);
        }

        return $this->success('User berhasil dibuat', $user);
    }

    public function updateUser(int $id, UserDTO $data)
    {
        $userData = $data->toArray();

        // Jika password kosong/null, jangan update kolom password
        if (array_key_exists('password', $userData)) {
            if ($userData['password'] === null || $userData['password'] === '') {
                unset($userData['password']);
            } else {
                $userData['password'] = Hash::make($data->password);
            }
        }

        // Hindari mengirim key 'roles' ke repository/model
        unset($userData['roles']);

        $updated = $this->userRepository->update($id, $userData);
        $user = $this->userRepository->find($id);

        // Selalu sync roles, meskipun array kosong
        if (isset($data->roles)) {
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

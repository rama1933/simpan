<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\User\UserService;
use App\Repositories\User\UserRepositoryInterface;
use App\Data\User\UserDTO;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Models\MasterSKPD;
use App\Models\User; // added for query builder

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'role', 'skpd_id']);
        $users = $this->getFilteredUsers($filters);
        
        // Get roles and SKPDs for filters
        $roles = Role::query()->select('id', 'name')->get();
        $skpds = MasterSKPD::query()->select('id', 'nama_skpd')->orderBy('nama_skpd')->get();

        return Inertia::render('User/Index', [
            'users' => $users,
            'filters' => $filters,
            'roles' => $roles,
            'skpds' => $skpds,
            'user' => $request->user(),
        ]);
    }

    public function filter(Request $request)
    {
        $filters = $request->only(['search', 'role', 'skpd_id']);
        $users = $this->getFilteredUsers($filters);

        // Return JSON response for API
        return response()->json([
            'success' => true,
            'users' => [
                'success' => true,
                'data' => $users
            ],
            'filters' => $filters
        ]);
    }

    private function getFilteredUsers(array $filters = [])
    {
        $query = User::query()
            ->with(['roles:id,name', 'skpd:id,nama_skpd']);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        // SKPD filter
        if (!empty($filters['skpd_id'])) {
            $query->where('skpd_id', $filters['skpd_id']);
        }

        return $query->paginate(15);
    }

    public function listJson(Request $request)
    {
        $users = $this->userRepository->getWithRoles();
        return response()->json(['data' => $users]);
    }

    public function create(Request $request)
    {
        $roles = Role::query()->select('id', 'name')->get();
        $skpds = MasterSKPD::query()->select('id', 'nama_skpd')->orderBy('nama_skpd')->get();
        return Inertia::render('User/Create', [
            'user' => $request->user(),
            'roles' => $roles,
            'skpds' => $skpds,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['string'],
            'skpd_id' => ['required', 'exists:master_skpds,id']
        ]);

        $dto = new UserDTO(
            name: $validated['name'] ?? null,
            email: $validated['email'] ?? null,
            password: $validated['password'] ?? null,
            roles: $validated['roles'] ?? [],
            skpd_id: $validated['skpd_id'] ?? null,
        );

        $this->userService->createUser($dto);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat');
    }

    public function edit(Request $request, int $id)
    {
        $user = $this->userRepository->find($id);
        $user->load('roles:id,name');
        $roles = Role::select('id', 'name')->get();
        $skpds = MasterSKPD::query()->select('id', 'nama_skpd')->orderBy('nama_skpd')->get();

        return Inertia::render('User/Edit', [
            'user' => $request->user(),
            'editing' => $user,
            'roles' => $roles,
            'skpds' => $skpds,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => ['nullable', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['string'],
            'skpd_id' => ['nullable', 'exists:master_skpds,id']
        ]);

        $dto = new UserDTO(
            name: $validated['name'] ?? null,
            email: $validated['email'] ?? null,
            password: $validated['password'] ?? null,
            roles: $validated['roles'] ?? [],
            skpd_id: $validated['skpd_id'] ?? null,
        );

        $this->userService->updateUser($id, $dto);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(Request $request, int $id)
    {
        $this->userRepository->delete($id);
        return response()->json(['success' => true]);
    }

    public function getByRole(Request $request, string $role)
    {
        $result = $this->userService->getUsersByRole($role);
        return response()->json($result);
    }
}

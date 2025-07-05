<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UsersDataTable;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends AppController
{
    private UserService $userService;
    private RoleService $roleService;

    public function __construct(Request $request, UserService $userService, RoleService $roleService)
    {
        parent::__construct($request);

        $this->middleware('can:view user')->only(['index']);
        $this->middleware('can:create user')->only(['create', 'store']);
        $this->middleware('can:edit user')->only(['edit', 'update']);
        $this->middleware('can:delete user')->only(['destroy']);

        $this->userService = $userService;
        $this->roleService = $roleService;

        config([
            'site.header' => 'Account List',
            'site.breadcrumbs' => [
                ['name' => 'Accounts', 'url' => route('users.index')],
            ]
        ]);
    }
    /**
     * Display a listing of the users.
     */
    public function index(UsersDataTable $dataTable)
    {
        $currentRoles = auth()->user()->roles->pluck('name')->toArray();
        return $dataTable
            ->setCurrentRoles($currentRoles)
            ->render('users.index');

    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        config([
            'site.header' => 'Account List | Create',
            'site.breadcrumbs' => [
                ['name' => 'Accounts', 'url' => route('users.index')],
                ['name' => 'Create', 'url' => route('users.create')],
            ]
        ]);

        $currentRole = auth()->user()->roles->pluck('name')->toArray();
        // Get all roles to allow assignment
        $roles = $this->roleService->getAll($currentRole);
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request)
    {
        // Create the new user
        $user = $this->userService->create($request->validated());

        // Assign roles to the user
        if ($request->has('roles')) {
            $user->assignRole($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Get all roles and the roles currently assigned to the user
        $roles = $this->roleService->getAll();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        // Update user details
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Sync roles for the user
        $user->syncRoles($request->roles ?: []); // Detach all roles if none selected

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the currently authenticated user
        if (auth()->user()->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
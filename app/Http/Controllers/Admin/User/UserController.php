<?php

namespace App\Http\Controllers\Admin\User;

use App\DataTables\UsersDataTable;
use App\Http\Controllers\Admin\AppController;
use App\Http\Requests\UserRequest;
use App\Models\BusinessLocation;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\BusinessLocation\BusinessLocationService;
use App\Services\User\RoleService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends AppController
{
    private UserService $userService;
    private RoleService $roleService;
    private $businessLocationService;

    public function __construct(Request $request, UserService $userService, BusinessLocationService $businessLocationService, RoleService $roleService)
    {
        parent::__construct($request);

        $this->middleware('can:view account')->only(['index']);
        $this->middleware('can:create account')->only(['create', 'store']);
        $this->middleware('can:edit account')->only(['edit', 'update']);
        $this->middleware('can:delete account')->only(['destroy']);

        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->businessLocationService = $businessLocationService;

        config([
            'site.header' => 'Daftar Akun',
            'site.breadcrumbs' => [
                ['name' => 'Daftar Akun', 'url' => route('users.index')],
            ]
        ]);
    }
    /**
     * Display a listing of the users.
     */
    public function index(UsersDataTable $dataTable)
    {
        $ownedRole = auth()->user()->getRoleNames()[0];
        $currentRoles = auth()->user()->roles->pluck('name')->toArray();
        
        $businessLocationQuery = $this->businessLocationService->businessLocationQuery();

        if ( $ownedRole === 'gudang') {
            $businessLocationQuery->whereNot('type', 'online');
        } else if ($ownedRole === 'kasir') {
            $businessLocationQuery->where('type', 'store');
        }

        $businessLocations = $businessLocationQuery->get();

        return $dataTable
            ->setCurrentRoles($currentRoles)
            ->render('users.index', compact('businessLocations'));

    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        config([
            'site.header' => 'Daftar Akun | Buat',
            'site.breadcrumbs' => [
                ['name' => 'Akun', 'url' => route('users.index')],
                ['name' => 'Buat', 'url' => route('users.create')],
            ]
        ]);
        $ownedRole = auth()->user()->getRoleNames()[0];

        $businessLocationQuery = $this->businessLocationService->businessLocationQuery();
        if ( $ownedRole === 'gudang') {
            $businessLocationQuery->whereNot('type', 'online');
        } else if ($ownedRole === 'kasir') {
            $businessLocationQuery->where('type', 'store');
        }
        $locations = $businessLocationQuery->get();

        $currentRole = auth()->user()->roles->pluck('name')->toArray();
        // Get all roles to allow assignment
        $roles = $this->roleService->getAll($currentRole);
        return view('users.create', compact('roles', 'locations'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request)
    {
        // Create the new user
        $user = $this->userService->create($request);

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
        $locations = BusinessLocation::all();

        $currentRole = auth()->user()->roles->pluck('name')->toArray();
        // Get all roles and the roles currently assigned to the user
        $roles = $this->roleService->getAll($currentRole);
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles', 'locations'));
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
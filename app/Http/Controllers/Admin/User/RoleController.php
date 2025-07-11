<?php

namespace App\Http\Controllers\Admin\User;

use App\DataTables\RolesDataTable;
use App\Http\Controllers\Admin\AppController;
use App\Http\Requests\RolePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->middleware('can:view role')->only(['index']);
        $this->middleware('can:create role')->only(['create', 'store']);
        $this->middleware('can:edit role')->only(['edit', 'update']);
        $this->middleware('can:delete role')->only(['destroy']);
        $this->middleware('can:assign permissions to roles')->only(['edit', 'update']);

        config([
            'site.header' => 'Role & Permission',
            'site.breadcrumbs' => [
                ['name' => 'Role & Permission', 'url' => route('roles.index')],
            ]
        ]);
    }

    /**
     * Display a listing of the role.
     */
    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render('roles.index');
    }

        /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(RolePermissionRequest $request)
    {
        Role::create(['name' => $request->name]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        // Get all permissions and the permissions currently assigned to the role
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(RolePermissionRequest $request, Role $role)
    {
        $role->name = $request->name;
        $role->save();

        // Sync permissions for the role
        $role->syncPermissions($request->permissions ?: []); // Detach all permissions if none selected

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting core roles if needed, e.g., 'owner'
        if (in_array($role->name, ['owner', 'admin', 'gudang', 'kasir'])) {
            return redirect()->route('roles.index')->with('error', 'Cannot delete core system roles.');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }
}

<?php

namespace App\Services;

use App\Http\Requests\PermissionRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    protected $permissionModel;

    public function __construct(Permission $permissionModel) {
      $this->permissionModel = $permissionModel;
    }

    /**
     * Create a new query builder for the Permission model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
      return $this->permissionModel->newQuery();
    }

    /**
     * Get all permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
      return $this->permissionModel->all();
    }

    /**
     * Create a new permission.
     *
     * @param array $data
     * @return \Spatie\Permission\Models\Permission
     */
    public function create(PermissionRequest $request): Permission
    {
        return $this->permissionModel->create([
            'name' => $request->name,
        ]);
    }

    /**
     * Get the permissions for a specific role.
     *
     * @param string $roleName
     * @return \Illuminate\Support\Collection
     */
    public function getPermissionsForRole(string $roleName)
    {
        $role = \Spatie\Permission\Models\Role::findByName($roleName);
        return $role->permissions;
    }

    /**
     * Check if a user has a specific permission.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string $permission
     * @return bool
     */
    public function hasPermission(User $user, string $permission)
    {
        return $user->can($permission);
    }
}
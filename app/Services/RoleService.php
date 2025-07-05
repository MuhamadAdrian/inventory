<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService
{
  protected $roleModel;

  public function __construct(Role $roleModel) {
    $this->roleModel = $roleModel;
  }

  public function roleQuery() {
    return $this->roleModel->newQuery();
  }

  public function getAll($currentRoles)
  {
      $exclusions = [
          'owner'  => [],
          'admin'  => ['owner'],
          'gudang' => ['owner', 'admin'],
          'kasir'  => ['owner', 'admin', 'gudang'],
      ];

      // Gather all roles to exclude based on current roles
      $exclude = [];

      foreach ($currentRoles as $role) {
          $exclude = array_merge($exclude, $exclusions[$role] ?? []);
      }

      // Eliminate duplicates
      $exclude = array_unique($exclude);

      // Filter the roles using object notation
      return $this->roleQuery()
          ->get()
          ->filter(function ($role) use ($exclude) {
              return !in_array($role->name, $exclude);
          })
          ->values(); // reset indexes if you need a clean array
  }
}
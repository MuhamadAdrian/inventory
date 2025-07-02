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

  public function getAll() {
    return $this->roleQuery()->get();
  }
}
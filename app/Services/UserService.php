<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService {
  protected $userModel;

  public function __construct(User $userModel) {
    $this->userModel = $userModel;
  }

  public function userQuery() {
    return $this->userModel->newQuery();
  }

  public function getUserWith(array $with = []) {
    return $this->userQuery()
      ->with($with)
      ->get();
  }

  public function create(Request $request) {
    return User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);
  }

  public function update() {}
  
  public function delete() {}
}
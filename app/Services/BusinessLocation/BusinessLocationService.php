<?php

namespace App\Services\BusinessLocation;

use App\Models\BusinessLocation;
use Illuminate\Foundation\Http\FormRequest;

class BusinessLocationService {
  protected BusinessLocation $businessLocationModel;

  public function __construct(BusinessLocation $businessLocationModel) {
    $this->businessLocationModel = $businessLocationModel;
  }

  public function businessLocationQuery() {
    return $this->businessLocationModel->newQuery();
  }

  public function getBusinessLocationsWith(array $with = []) {
    return $this->businessLocationQuery()
      ->with($with)
      ->get();
  }
  
  public function create(FormRequest $request) {
    return $this->businessLocationModel->create([
      'name' => $request->name,
      'code' => $request->code,
      'city' => $request->city,
      'area' => $request->area,
      'phone' => $request->phone,
      'type' => $request->type
    ]);
  }

  public function update(int $id, FormRequest $request) {
    $businessLocation = $this->businessLocationModel->findOrFail($id);

    $businessLocation->update([
      'name' => $request->name,
      'code' => $request->code,
      'city' => $request->city,
      'area' => $request->area,
      'phone' => $request->phone,
      'type' => $request->type
    ]);

    return $businessLocation;
  }

  public function delete(int $id) {
    $businessLocation = $this->businessLocationModel->findOrFail($id);
    return $businessLocation->delete();
  }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PermissionRequest;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends AppController
{
    protected $permissionService;
    public function __construct(Request $request, PermissionService $permissionService)
    {
        parent::__construct($request);
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        $permissions = $this->permissionService->getAll();
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(PermissionRequest $request)
    {
        $this->permissionService->create($request);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully!');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully!');
    }
}

@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Role: {{ $role->name }}</h1>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Back to Roles
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text" class="form-control rounded-md @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Assign Permissions</label>
                    <div class="row">
                        @forelse ($permissions as $permission)
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}"
                                    {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-muted">No permissions defined. You might need to run `php artisan db:seed` or create them.</p>
                        </div>
                        @endforelse
                    </div>
                    @error('permissions')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary rounded-md shadow-sm">Update Role</button>
            </form>
        </div>
    </div>
</div>
@endsection

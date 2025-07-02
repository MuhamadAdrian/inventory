@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Create New Role</h1>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Back to Roles
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text" class="form-control rounded-md @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary rounded-md shadow-sm">Create Role</button>
            </form>
        </div>
    </div>
</div>
@endsection

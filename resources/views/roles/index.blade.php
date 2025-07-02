{{-- Assumes you have a layout file like 'layouts/app.blade.php' --}}
@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Role Management</h1>
        @can('create role')
        <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-md shadow-sm">
            Add New Role
        </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-md shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-md shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            {{ $dataTable->table() }}
        </div>
    </div>

    {{-- Optional: Add a link to manage individual permissions directly --}}
    @can('create permission') {{-- Assuming you'd have a permission for this --}}
    <div class="mt-4 text-center">
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-info rounded-md">Manage Individual Permissions</a>
    </div>
    @endcan
</div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush

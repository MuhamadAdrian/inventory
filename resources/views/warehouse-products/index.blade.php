{{-- Assumes you have a layout file like 'layouts/app.blade.php' --}}
@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Warehouse Stock Management</h1>
        @can('stock request')
        <a href="{{ route('stock-out-requests.create') }}" class="btn btn-primary rounded-md shadow-sm">
            Request New Stock
        </a>
        @endcan
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            {{ $dataTable->table() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush

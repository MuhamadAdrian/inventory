@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ config('site.header') }}</h1>
        @can('create account')
        <a href="{{ route('users.create') }}" class="btn btn-primary rounded-md shadow-sm">
            Buat Akun
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
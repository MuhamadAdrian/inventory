@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Akun: {{ $user->name }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            {{ __('Kembali') }}
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- Include the shared form partial, passing the user and roles data --}}
                @include('users.template.form', ['user' => $user, 'roles' => $roles, 'userRoles' => $userRoles])

                <button type="submit" class="btn btn-primary rounded-md shadow-sm">{{ __('Ubah User') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

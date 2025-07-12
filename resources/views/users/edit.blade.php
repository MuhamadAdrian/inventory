@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">edit account: {{ $user->name }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Back to Users
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- Include the shared form partial, passing the user and roles data --}}
                @include('users.template.form', ['user' => $user, 'roles' => $roles, 'userRoles' => $userRoles])

                <button type="submit" class="btn btn-primary rounded-md shadow-sm">Update User</button>
            </form>
        </div>
    </div>
</div>
@endsection

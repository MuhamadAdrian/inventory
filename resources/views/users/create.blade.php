@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Buat Akun Baru</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Kembali
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                {{-- Include the shared form partial --}}
                @include('users.template.form', ['roles' => $roles])

                <button type="submit" class="btn btn-primary rounded-md shadow-sm">Buat Akun</button>
            </form>
        </div>
    </div>
</div>
@endsection

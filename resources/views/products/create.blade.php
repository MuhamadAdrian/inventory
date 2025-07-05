{{-- Assumes you have a layout file like 'layouts/admin.blade.php' --}}
@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Create New Product</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Back to Products
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Include the shared form partial --}}
                @include('products.template.form')

                <button type="submit" class="btn btn-primary rounded-md shadow-sm">Create Product</button>
            </form>
        </div>
    </div>
</div>
@endsection

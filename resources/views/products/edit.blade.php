{{-- Assumes you have a layout file like 'layouts/admin.blade.php' --}}
@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Product: {{ $product->name }}</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Back to Products
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                {{-- Include the shared form partial, passing the product data --}}
                @include('products.template.form', ['product' => $product])

                <button type="submit" class="btn btn-primary rounded-md shadow-sm">Update Product</button>
            </form>
        </div>
    </div>
</div>
@endsection

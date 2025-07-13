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

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-md shadow-sm">Update Product</button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-warning rounded-md shadow-sm">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="{{ route('products.update_stock') }}" method="post">
        @csrf
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Create New Stock</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="col-md-12 mb-3">
            <input type="hidden" value="{{ $product->id }}" name="product_id" />
            <label for="stock" class="form-label">Stock</label>
            <input type="number" class="form-control rounded-md @error('stock') is-invalid @enderror" id="stock" name="stock" required>
            @error('stock')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create</button>
      </div>
    </form>
  </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">View Product: {{ $product->name }}</h1>
        <a href="{{ route('products.store.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Kembali
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input readonly type="text" class="form-control rounded-md" id="name" name="name" value="{{ $product->name }}" required autofocus>

                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="item_code" class="form-label">Kode Barang</label>
                        <input readonly type="text" class="form-control rounded-md" id="item_code" name="item_code" value="{{ $product->item_code }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control rounded-md" id="description" name="description" rows="3">{{ $product->description }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Harga</label>
                        <input readonly type="text" class="form-control rounded-md" id="price" name="price" value="{{ $product->formatted_price }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <input readonly type="text" class="form-control rounded-md" id="category" name="category" value="{{ $product->category }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="color" class="form-label">Warna</label>
                        <input readonly type="text" class="form-control rounded-md" id="color" name="color" value="{{ $product->color }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="series" class="form-label">Series</label>
                        <input readonly type="text" class="form-control rounded-md" id="series" name="series" value="{{ $product->series }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="brand" class="form-label">Brand</label>
                        <input readonly type="text" class="form-control rounded-md" id="brand" name="brand" value="{{ $product->brand }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="material" class="form-label">Material</label>
                        <input readonly type="text" class="form-control rounded-md" id="material" name="material" value="{{ $product->material }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="size" class="form-label">Size</label>
                        <input readonly type="text" class="form-control rounded-md" id="size" name="size" value="{{ $product->size }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="weight" class="form-label">Weight (kg)</label>
                        <input readonly type="number" step="0.01" class="form-control rounded-md" id="weight" name="weight" value="{{ $product->weight }}" min="0">
                    </div>
                </div>

                {{-- Product Images Section --}}
                <div class="mb-3">
                    @if ($product->images->count() > 0)
                        <div class="mt-3 row">
                            <label class="form-label">Existing Images:</label>
                            @foreach ($product->images as $image)
                                <div class="col-6 col-md-4 col-lg-3 mb-3">
                                    <div class="card h-100 shadow-sm rounded-md">
                                        <img src="{{ $image->url }}" class="card-img-top img-fluid rounded-top-md" alt="Product Image" style="object-fit: cover; height: 150px;">
                                        <div class="card-body p-2">
                                            @if ($image->is_main)
                                                <span class="badge bg-primary text-white mt-1">Main Image</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
        </div>
    </div>
</div>
@endsection
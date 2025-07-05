{{-- This partial is used for both creating and editing products. --}}
{{-- It expects the following variables: --}}
{{-- - $product (optional): The App\Models\Product instance if editing. If not present, assumes creation. --}}
{{-- - $categories, $colors, $sizes, $brands: Collections for datalist options. --}}
{{-- - $barcodeSvg (optional): SVG string of the barcode, passed from edit view. --}}

@php
    $isEdit = isset($product);
    // Ensure collections are defined, even if empty for create view
    $categories = $categories ?? collect();
    $colors = $colors ?? collect();
    $sizes = $sizes ?? collect();
    $brands = $brands ?? collect();
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control rounded-md @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $isEdit ? $product->name : '') }}" required autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="item_code" class="form-label">Item Code</label>
        <input type="text" class="form-control rounded-md @error('item_code') is-invalid @enderror" id="item_code" name="item_code" value="{{ old('item_code', $isEdit ? $product->item_code : '') }}">
        @error('item_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control rounded-md @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control rounded-md @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $isEdit ? $product->price : '') }}" required min="0">
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="stock" class="form-label">Stock</label>
        <input type="number" class="form-control rounded-md @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $isEdit ? $product->stock : '') }}" required min="0">
        @error('stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="category" class="form-label">Category</label>
        <input type="text" class="form-control rounded-md @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $isEdit ? $product->category : '') }}" list="categoryOptions">
        <datalist id="categoryOptions">
            @foreach ($categories as $cat)
                <option value="{{ $cat->name }}">
            @endforeach
        </datalist>
        @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="color" class="form-label">Color</label>
        <input type="text" class="form-control rounded-md @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $isEdit ? $product->color : '') }}" list="colorOptions">
        <datalist id="colorOptions">
            @foreach ($colors as $col)
                <option value="{{ $col->name }}">
            @endforeach
        </datalist>
        @error('color')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="series" class="form-label">Series</label>
        <input type="text" class="form-control rounded-md @error('series') is-invalid @enderror" id="series" name="series" value="{{ old('series', $isEdit ? $product->series : '') }}">
        @error('series')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="brand" class="form-label">Brand</label>
        <input type="text" class="form-control rounded-md @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand', $isEdit ? $product->brand : '') }}" list="brandOptions">
        <datalist id="brandOptions">
            @foreach ($brands as $br)
                <option value="{{ $br->name }}">
            @endforeach
        </datalist>
        @error('brand')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="material" class="form-label">Material</label>
        <input type="text" class="form-control rounded-md @error('material') is-invalid @enderror" id="material" name="material" value="{{ old('material', $isEdit ? $product->material : '') }}">
        @error('material')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="size" class="form-label">Size</label>
        <input type="text" class="form-control rounded-md @error('size') is-invalid @enderror" id="size" name="size" value="{{ old('size', $isEdit ? $product->size : '') }}" list="sizeOptions">
        <datalist id="sizeOptions">
            @foreach ($sizes as $sz)
                <option value="{{ $sz->name }}">
            @endforeach
        </datalist>
        @error('size')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="weight" class="form-label">Weight (kg)</label>
        <input type="number" step="0.01" class="form-control rounded-md @error('weight') is-invalid @enderror" id="weight" name="weight" value="{{ old('weight', $isEdit ? $product->weight : '') }}" min="0">
        @error('weight')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Product Images Section --}}
<div class="mb-3">
    <label for="images" class="form-label">Product Images</label>
    <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
    @error('images.*')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Upload multiple images (JPEG, PNG, JPG, GIF, SVG, max 2MB each).</small>

    @if ($isEdit && $product->images->count() > 0)
        <div class="mt-3 row">
            <label class="form-label">Existing Images:</label>
            @foreach ($product->images as $image)
                <div class="col-6 col-md-4 col-lg-3 mb-3">
                    <div class="card h-100 shadow-sm rounded-md">
                        <img src="{{ $image->url }}" class="card-img-top img-fluid rounded-top-md" alt="Product Image" style="object-fit: cover; height: 150px;">
                        <div class="card-body p-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="existing_images_to_delete[]" value="{{ $image->id }}" id="deleteImage{{ $image->id }}">
                                <label class="form-check-label" for="deleteImage{{ $image->id }}">
                                    Delete
                                </label>
                            </div>
                            @if ($image->is_main)
                                <span class="badge bg-primary text-white mt-1">Main Image</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <small class="form-text text-muted">Check the box to delete an existing image.</small>
    @endif
</div>

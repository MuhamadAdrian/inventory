@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ config('site.header') }}</h1>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <div class="mb-3">
                    <label for="store-filter" class="form-label">Toko:</label>
                    <select class="form-select rounded-md" id="store-filter">
                        <option value="">-- Semua --</option>
                        @foreach ($stores as $businessLocation)
                        <option value="{{ $businessLocation->id }}" @if(optional(auth()->user()->businessLocation ?? null)->id === $businessLocation->id) selected @endif>{{ $businessLocation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="product-filter" class="form-label">Produk:</label>
                    <select class="form-select rounded-md" id="product-filter">
                        <option value="">-- Semua --</option>
                        @foreach ($productStores as $productStore)
                        <option value="{{ $productStore->product_id }}">{{ $productStore->product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="products-store-table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Kode Barang</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Stok</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DataTables akan mengisi tbody ini --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(function() {
        var productStoreTable = $('#products-store-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
              url: '{!! route('products.store.data') !!}',
              data: function (d) {
                  d.store_filter = $('#store-filter').val();
                  d.product_filter = $('#product-filter').val();
              }
            },
            columns: [
                { data: 'product.id', name: 'product.id' },
                { data: 'product.name', name: 'product.name' },
                { data: 'product.item_code', name: 'product.item_code' },
                { data: 'product.price', name: 'product.price' },
                { data: 'stock', name: 'stock' },
                { data: 'action', name: 'action', 'orderable': false, searchable: false, class: 'text-end' },
            ],
            order: [[0, 'desc']]
        });

        $('#store-filter').on('change', function() {
            productStoreTable.draw();
        });

        $('#product-filter').on('change', function() {
            productStoreTable.draw();
        });
    });
</script>
@endpush

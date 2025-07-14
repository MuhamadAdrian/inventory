@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ config('site.header') }}</h1>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="stock-history-table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Nama Produk</th>
                            <th scope="col">Lokasi Barang</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Tipe</th>
                            <th scope="col">PIC</th>
                            <th scope="col">Stok Akhir</th>
                            {{-- <th scope="col" class="text-center">Aksi</th> --}}
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
        $('#stock-history-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('stock-history.data') !!}',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'created_at', name: 'created_at' },
                { data: 'product.name', name: 'product.name' },
                { data: 'business_location.name', name: 'businessLocation.name' },
                { data: 'quantity', name: 'quantity' },
                { data: 'type', name: 'type', orderable: false, searchable: false },
                { data: 'causer.name', name: 'causer.name' },
                { data: 'stock', name: 'stock' },
                // { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']]
        });
    });

</script>
@endpush

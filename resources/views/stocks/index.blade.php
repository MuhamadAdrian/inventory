@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manajemen Transfer Stok</h1>
        @can('stock input to warehouse')
        <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary rounded-md shadow-sm">
            Buat Permintaan Transfer Baru
        </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-md shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-md shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="stock-transfers-table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Tanggal Permintaan</th>
                            <th scope="col">Tanggal Tiba Diinginkan</th>
                            <th scope="col">Gudang Pengirim</th>
                            <th scope="col">Gudang Penerima</th>
                            <th scope="col">Status</th>
                            <th scope="col">Dibuat Oleh</th>
                            <th scope="col">Tanggal Dibuat</th>
                            <th scope="col" class="text-center">Aksi</th>
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
        $('#stock-transfers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('stock-transfers.data') !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'request_date', name: 'request_date' },
                { data: 'desired_arrival_date', name: 'desired_arrival_date' },
                { data: 'sender_warehouse_name', name: 'senderWarehouse.name' },
                { data: 'receiver_warehouse_name', name: 'receiverWarehouse.name' },
                { data: 'status', name: 'status' },
                { data: 'created_by_name', name: 'createdBy.name', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush

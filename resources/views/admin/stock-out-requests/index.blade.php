@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ config('site.header') }}</h1>
        @can('create stock request')
        <a href="{{ route('stock-out-requests.create') }}" class="btn btn-primary rounded-md shadow-sm">
            Buat Permintaan Baru
        </a>
        @endcan
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="stock-out-requests-table">
                    <thead>
                        <tr>
                            <th scope="col">Tgl Permintaan</th>
                            <th scope="col">Estimasi Tiba</th>
                            <th scope="col">Pengirim</th>
                            <th scope="col">Penerima</th>
                            <th scope="col">Status</th>
                            <th scope="col">Dibuat Oleh</th>
                            <th scope="col">Tgl Dibuat</th>
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
        $('#stock-out-requests-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('stock-out-requests.data') !!}',
            columns: [
                { data: 'request_date', name: 'request_date' },
                { data: 'desired_arrival_date', name: 'desired_arrival_date' },
                { data: 'sender_name', name: 'sender.name' },
                { data: 'receiver_name', name: 'receiver.name' },
                { data: 'status', name: 'status' },
                { data: 'created_by_name', name: 'createdBy.name', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[6, 'desc']]
        });
    });
</script>
@endpush

@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ config('site.header') }}</h1>
        @can('create business location')
        <a href="{{ route('business-locations.create') }}" class="btn btn-primary rounded-md shadow-sm">
            Buat Lokasi Baru
        </a>
        @endcan
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <div class="d-flex gap-2">
                <div class="mb-3">
                    <label for="type-filter" class="form-label">Tipe:</label>
                    <select class="form-select rounded-md" id="type-filter">
                        <option value="">-- Semua --</option>
                        <option value="online">Online</option>
                        <option value="warehouse">Gudang</option>
                        <option value="store">Toko</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="business-locations-table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nama Lokasi</th>
                            <th scope="col">Kode</th>
                            <th scope="col">Kota</th>
                            <th scope="col">Area</th>
                            <th scope="col">No Telp</th>
                            <th scope="col">Tipe</th>
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
        var businessLocationsTable = $('#business-locations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('business-locations.data') !!}',
                data: function (d) {
                    d.type_filter = $('#type-filter').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'code', name: 'code' },
                { data: 'city', name: 'city' },
                { data: 'area', name: 'area' },
                { data: 'phone', name: 'phone' },
                { data: 'type', name: 'type' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'asc']]
        });

        $('#type-filter').on('change', function() {
            businessLocationsTable.draw();
        });
    });

</script>
@endpush

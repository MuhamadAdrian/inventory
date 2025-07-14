@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ config('site.header') }}</h1>
        @can('create account')
        <a href="{{ route('users.create') }}" class="btn btn-primary rounded-md shadow-sm">
            Buat Akun
        </a>
        @endcan
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <div class="d-flex gap-2">
                <div class="mb-3">
                    <label for="location-filter" class="form-label">Lokasi:</label>
                    <select class="form-select rounded-md" id="location-filter">
                        <option value="">-- Semua --</option>
                        @foreach ($businessLocations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{ $dataTable->table() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script type="module">
        var dataTableInstance = null;

        $('#users-table').on('init.dt', function() {
            dataTableInstance = $(this).DataTable();

            dataTableInstance.on('preXhr.dt', function (e, settings, data) {
                data.location_filter = $('#location-filter').val();
            });

            $('#location-filter').on('change', function() {
                dataTableInstance.ajax.reload(null, false);
            });
        });
    </script>
@endpush
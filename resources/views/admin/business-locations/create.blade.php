@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Create New Location</h1>
        <a href="{{ route('business-locations.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Back to Locations
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('business-locations.store') }}" method="POST">
                @csrf
                {{-- Include the shared form partial --}}
                @include('admin.business-locations.template.form')

                <button type="submit" class="btn btn-primary rounded-md shadow-sm">Create Location</button>
            </form>
        </div>
    </div>
</div>
@endsection

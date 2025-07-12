@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Location: {{ $businessLocation->name }}</h1>
        <a href="{{ route('business-locations.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Back to Locations
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('business-locations.update', $businessLocation->id) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- Include the shared form partial --}}
                @include('admin.business-locations.template.form', ['businessLocation' => $businessLocation])

                <button type="submit" class="btn btn-primary rounded-md shadow-sm">Update Location</button>
            </form>
        </div>
    </div>
</div>
@endsection

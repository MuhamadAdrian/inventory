<div class="d-flex justify-content-center">
    @can('edit business location')
    <a href="{{ route('business-locations.edit', $location->id) }}" class="btn btn-sm btn-warning rounded-3 me-2 text-white" title="edit business location">
        <i class="bi bi-pencil-fill"></i>
    </a>
    @endcan
    @can('delete business location')
    <form action="{{ route('business-locations.destroy', $location->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this location?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger rounded-3 text-white" title="delete location">
            <i class="bi bi-trash-fill"></i>
        </button>
    </form>
    @endcan
</div>

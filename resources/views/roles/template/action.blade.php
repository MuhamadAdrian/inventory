<div class="d-flex justify-content-center">
    @can('edit role')
    <a href="{{ route('roles.edit', $id) }}" class="btn btn-sm btn-warning rounded-3 me-2 text-white" title="Edit Role">
        <i class="bi bi-pencil-fill"></i>
    </a>
    @endcan
    @can('delete role')
    <form action="{{ route('roles.destroy', $id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger rounded-3 text-white" title="Delete Role">
            <i class="bi bi-trash-fill"></i>
        </button>
    </form>
    @endcan
</div>

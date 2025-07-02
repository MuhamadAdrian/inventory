<div class="d-flex justify-content-center">
    @can('edit user')
    <a href="{{ route('users.edit', $id) }}" class="btn btn-sm btn-warning rounded-3 me-2 text-white" title="Edit User">
        <i class="bi bi-pencil-fill"></i>
    </a>
    @endcan
    @can('delete user')
    <form action="{{ route('users.destroy', $id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger rounded-3 text-white" title="Delete User">
            <i class="bi bi-trash-fill"></i>
        </button>
    </form>
    @endcan
</div>

<div class="d-flex justify-content-center">
    @can('print barcode')
    <a href="{{ route('products.print_barcode', ['product' => $id, 'count' => 3]) }}" class="btn btn-sm btn-primary rounded-3 me-2 text-white" target='_blank' title="Print Barcode">
        <i class="bi bi-printer-fill"></i>
    </a>
    @endcan
    @can('edit product')
    <a href="{{ route('products.edit', $id) }}" class="btn btn-sm btn-warning rounded-3 me-2 text-white" title="Edit Product">
        <i class="bi bi-pencil-fill"></i>
    </a>
    @endcan
    @can('delete product')
    <form action="{{ route('products.destroy', $id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger rounded-3 text-white" title="Delete Product">
            <i class="bi bi-trash-fill"></i>
        </button>
    </form>
    @endcan
</div>

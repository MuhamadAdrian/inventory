
  @can('view store product')
    <a href="{{ route('products.store.show', $product->id) }}" class='btn btn-sm btn-info rounded-md me-2' title='Lihat Detail'>
        <i class='bi bi-eye-fill'></i> Lihat
    </a>
  @endcan


  @can('view product location')
    <a href="{{ route('products.store.show', $productBusiness->id) }}" class='btn btn-sm btn-info rounded-md me-2' title='Lihat Detail'>
        <i class='bi bi-eye-fill'></i> Lihat
    </a>
  @endcan

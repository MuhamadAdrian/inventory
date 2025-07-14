
  @can('view product stock history')
    <a href="{{ route('stock-history.show', $productStockHistory->id) }}" class='btn btn-sm btn-info rounded-md me-2' title='Lihat Detail'>
        <i class='bi bi-eye-fill'></i> Lihat
    </a>
  @endcan

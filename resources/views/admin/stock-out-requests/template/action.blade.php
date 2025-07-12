<div class="d-flex">
  <a href="{{ route('stock-out-requests.show', $request->id) }}" class='btn btn-sm btn-info rounded-md me-2' title='Lihat Detail'>
      <i class='bi bi-eye-fill'></i> Lihat
  </a>
  @can('approval stock request')
  @if($request->status === 'pending')
  <form action="{{ route('stock-out-requests.process', $request->id) }}" method='POST' onsubmit='return confirm("Apakah Anda yakin ingin memproses transfer stok ini? Ini akan mengurangi stok dari gudang pengirim dan menambahkannya ke gudang penerima");' style='display:inline-block;'>
      @csrf
      @method('put')
      <button type='submit' class='btn btn-sm btn-success rounded-md me-2' title='Proses Transfer'>
          <i class='bi bi-check-circle-fill'></i> Proses
      </button>
  </form>
  <form action="{{ route('stock-out-requests.cancel', $request->id) }}" method='POST' onsubmit='return confirm("Apakah Anda yakin ingin menghapus permintaan transfer ini?");' style='display:inline-block;'>
      @csrf
      @method('put')
      <button type='submit' class='btn btn-sm btn-danger rounded-md' title='Batalkan'>
          <i class='bi bi-x-lg'></i> Batal
      </button>
  </form>
  @endif
  @endcan
</div>
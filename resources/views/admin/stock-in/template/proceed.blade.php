@extends('layouts.blank')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ config('site.header') }}</h1>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <table>
                <tr>
                    <th>Nama Produk</th>
                    <td>:</td>
                    <td>{{ $item->product->name }}</td>
                </tr>
                <tr>
                    <th>Kode</th>
                    <td>:</td>
                    <td>{{ $item->product->item_code }}</td>
                </tr>
            </table>

            <p class="mt-4">Pastikan jumlah stok sudah sesuai dengan pengecekan Anda</p>

            <form action="{{ route('stock-out-requests.stock-in-store-proceed', $item->id) }}" method="POST" onsubmit="return confirm('Barang akan otomatis bertambah, apakah anda yakin dengan ini ?');">
              @csrf
              <div class="mb-3">
                  <label for="quantity" class="form-label">{{ __('Jumlah') }}</label>
                  <input type="text" class="form-control rounded-md @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $item->quantity) }}" required autofocus>
                  @error('quantity')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>

              <button type="submit" class="btn btn-primary rounded-md shadow-sm">Kirim</button>
            </form>

        </div>
    </div>
</div>
@endsection

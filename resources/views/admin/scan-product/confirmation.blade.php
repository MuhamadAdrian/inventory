@extends('layouts.blank')

@section('content')
@php
    $currentPath = request()->path(); // e.g. 'admin/stock-in-scan'

    $urlSubmit = '';

    if(Str::contains($currentPath, 'stock-in-scan')){
        $urlSubmit = 'stock-in-scan.proceed';
    }
    else {
        $urlSubmit = 'stock-out-scan.proceed';
    }
    
@endphp

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        @if(Str::contains($currentPath, 'stock-in-scan'))
            <h1 class="h3 mb-0">{{ __('Konfirmasi Produk Masuk') }}</h1>
        @elseif(Str::contains($currentPath, 'stock-out-scan'))
            <h1 class="h3 mb-0">{{ __('Konfirmasi Produk Keluar') }}</h1>
        @else
            <h1 class="h3 mb-0">{{ __('Konfirmasi Produk yang telah Scan') }}</h1>
        @endif
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <table class="mb-4">
                <tr>
                    <th>Nama Produk</th>
                    <td class="px-1">:</td>
                    <td>{{ $product->name }}</td>
                </tr>
                <tr>
                    <th>Kode</th>
                    <td class="px-1">:</td>
                    <td>{{ $product->item_code }}</td>
                </tr>
            </table>

            <form action="{{ route($urlSubmit, $product->id) }}" method="POST" onsubmit="return confirm('Barang akan otomatis berkurang dan berpindah sesuai stoknya, apakah anda yakin dengan ini ?');">
              @csrf
              <input type="hidden" name="item_code" value="{{ $itemCode }}" />
              <input type="hidden" name="product_id" value="{{ $product->id }}" />
              <div class="mb-3">
                    @if(Str::contains($currentPath, 'stock-in-scan'))
                    <label for="quantity" class="form-label">{{ __('Di Ambil dari') }}</label>
                    @elseif(Str::contains($currentPath, 'stock-out-scan'))
                    <label for="quantity" class="form-label">{{ __('Berikan Ke') }}</label>
                    @endif
                  <select class="form-select rounded-md @error('business_location_id') is-invalid @enderror" id="business_location_id" name="business_location_id">
                      <option value="">-- {{ __('Pilih Lokasi') }} --</option>
                      @foreach ($businessLocations as $location)
                          <option value="{{ $location->id }}"
                              {{ old('business_location_id', $user->business_location_id ?? null) == $location->id ? 'selected' : '' }}
                          >
                              {{ $location->name }} ({{"{$location->name} ({$location->area} - {$location->city})"}})
                          </option>
                      @endforeach
                  </select>
                  @error('business_location_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>
              <div class="mb-3">
                  <label for="quantity" class="form-label">{{ __('Jumlah') }}</label>
                  <input type="text" class="form-control rounded-md @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" required autofocus>
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

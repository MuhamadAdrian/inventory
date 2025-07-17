@extends('layouts.admin')

@section('content')
            <div class="container-lg px-4">
          <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
              <div class="card text-white bg-primary">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold">{{ $jumlahLokasi }}</div>
                    <div>Jumlah Lokasi Barang</div>
                  </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                  <canvas class="chart" id="card-chart1" height="70"></canvas>
                </div>
              </div>
            </div>
            <!-- /.col-->
            <div class="col-sm-6 col-xl-3">
              <div class="card text-white bg-info">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold">{{ $productStats->total_product }}<span class="fs-6 fw-normal">({{ $productStats->growth_percentage }}%
                        <svg class="icon">
                          @if($productStats->status === 'Naik')
                          <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-arrow-top') }}"></use>
                          @else
                          <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-arrow-bottom') }}"></use>
                          @endif
                        </svg>)</span></div>
                    <div>Total Seluruh Produk</div>
                  </div>
                  <div class="dropdown">
                    <button class="btn btn-transparent text-white p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <svg class="icon">
                        <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-options') }}"></use>
                      </svg>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="#">Bulan ini : {{$productStats->this_month_count}}</a><a class="dropdown-item" href="#">Bulan Lalu : {{ $productStats->last_month_count }}</a></div>
                  </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                  <canvas class="chart" id="card-chart2" height="70"></canvas>
                </div>
              </div>
            </div>
            <!-- /.col-->
            <div class="col-sm-6 col-xl-3">
              <div class="card text-white bg-warning">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold">{{$historiPerpindahanBarangHariIni}}</div>
                    <div>Stok Barang Berpindah</div>
                  </div>
                  <i class="bi bi-archive-fill"></i>
                </div>
                <div class="c-chart-wrapper mt-3" style="height:70px;">
                  <canvas class="chart" id="card-chart3" height="70"></canvas>
                </div>
              </div>
            </div>
            <!-- /.col-->
            <div class="col-sm-6 col-xl-3">
              <div class="card text-white bg-danger">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fs-4 fw-semibold">{{$stokProdukTerkecilDariSeluruhLokasiYangAda->stock}} Stok <span class="fs-6 fw-normal"></div>
                    <div>{{ $stokProdukTerkecilDariSeluruhLokasiYangAda->product->name }} | {{ $stokProdukTerkecilDariSeluruhLokasiYangAda->businessLocation->name }}</div>
                  </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                  <canvas class="chart" id="card-chart4" height="70"></canvas>
                </div>
              </div>
            </div>
            <!-- /.col-->
          </div>
          <!-- /.row-->
          <div class="card mb-4">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h4 class="card-title mb-0">Aktivitas Keluar Masuk Barang ( Top 10 ) {{ $stockMovement['now'] }}</h4>
                  <div class="small text-body-secondary">{{ $stockMovement['monthRange'] }}</div>
                </div>
                <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                  <div class="btn-group btn-group-toggle mx-3" data-coreui-toggle="buttons">
                    <input class="btn-check" id="option1" type="radio" name="options" autocomplete="off">
                    <label class="btn btn-outline-secondary"> Day</label>
                    <input class="btn-check" id="option2" type="radio" name="options" autocomplete="off" checked="">
                    <label class="btn btn-outline-secondary active"> Month</label>
                    <input class="btn-check" id="option3" type="radio" name="options" autocomplete="off">
                    <label class="btn btn-outline-secondary"> Year</label>
                  </div>
                  <button class="btn btn-primary" type="button">
                    <svg class="icon">
                      <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-cloud-download"></use>
                    </svg>
                  </button>
                </div>
              </div>
              <canvas id="productActivityChart" height="300"></canvas>
              <div class="c-chart-wrapper" style="height:300px;margin-top:40px;">
              </div>
            </div>
          </div>
          <!-- /.row-->
        </div>
@endsection

{{-- @php
  dd($stockMovement);
@endphp --}}

@push('scripts')
<script type="module">
    const labels = @json($stockMovement['labels']);
    const data = @json($stockMovement['data']);

    renderProductActivityChart(labels, data);
</script>
@endpush


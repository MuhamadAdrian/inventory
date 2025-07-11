{{-- Asumsi Anda memiliki file layout seperti 'layouts/admin.blade.php' --}}
@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        @role('gudang')
        <h1 class="h3 mb-0">Buat Permintaan Transfer Stok Baru</h1>
        @endrole

        @role('admin')
        <h1 class="h3 mb-0">Tambah Stock Gudang</h1>
        @endrole

        @role('owner')
        <h1 class="h3 mb-0">Tambah Stock Gudang</h1>
        @endrole
        <a href="{{ route('stock-transfers.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Kembali ke Daftar Transfer
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-md shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-md shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <form action="{{ route('stock-transfers.store') }}" method="POST" id="stockTransferForm">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="request_date" class="form-label">Tanggal Permintaan</label>
                        <input type="date" class="form-control rounded-md @error('request_date') is-invalid @enderror" id="request_date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" required>
                        @error('request_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="desired_arrival_date" class="form-label">Tanggal Tiba Diinginkan</label>
                        <input type="date" class="form-control rounded-md @error('desired_arrival_date') is-invalid @enderror" id="desired_arrival_date" name="desired_arrival_date" value="{{ old('desired_arrival_date') }}">
                        @error('desired_arrival_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sender_warehouse_id" class="form-label">Gudang Pengirim</label>
                        <select class="form-select rounded-md @error('sender_warehouse_id') is-invalid @enderror" id="sender_warehouse_id" name="sender_warehouse_id" required>
                            <option value="">-- Pilih Gudang Pengirim --</option>
                            @foreach ($warehouseSenders as $warehouse)
                                <option value="{{ $warehouse->id }}"
                                    {{ old('sender_warehouse_id') == $warehouse->id ? 'selected' : '' }}
                                >
                                    {{ $warehouse->name }} ({{ $warehouse->location }})
                                </option>
                            @endforeach
                        </select>
                        @error('sender_warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="receiver_warehouse_id" class="form-label">Gudang Penerima</label>
                        <select class="form-select rounded-md @error('receiver_warehouse_id') is-invalid @enderror" id="receiver_warehouse_id" name="receiver_warehouse_id" required>
                            <option value="">-- Pilih Gudang Penerima --</option>
                            @foreach ($warehouseReceivers as $warehouse)
                                <option value="{{ $warehouse->id }}"
                                    {{ old('receiver_warehouse_id', $userWarehouse->id ?? '') == $warehouse->id ? 'selected' : '' }}
                                    {{ $userWarehouse && $userWarehouse->id == $warehouse->id && !Auth::user()->hasRole('owner') ? 'disabled' : '' }}
                                >
                                    {{ $warehouse->name }} ({{ $warehouse->location }})
                                </option>
                            @endforeach
                        </select>
                        @error('receiver_warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if ($userWarehouse && !Auth::user()->hasRole('owner'))
                            <input type="hidden" name="receiver_warehouse_id" value="{{ old('receiver_warehouse_id', $userWarehouse->id) }}">
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control rounded-md @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                @role('admin')
                <h4>Pilih Produk untuk Ditambah</h4>
                @endrole
                @role('owner')
                <h4>Pilih Produk untuk Ditambah</h4>
                @endrole
                @role('gudang')
                <h4>Pilih Produk untuk Diminta</h4>
                @endrole
                <div class="alert alert-info" id="productSelectionMessage" style="display:none;">
                    Harap pilih setidaknya satu produk dan tentukan kuantitasnya.
                </div>
                <div class="table-responsive mb-4">
                    <table class="table table-hover table-striped mb-0" id="products-selection-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Kode Item</th>
                                <th scope="col">Nama Produk</th>
                                <th scope="col">Harga</th>
                                <th scope="col" class="text-center">Kuantitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables akan mengisi tbody ini --}}
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-md shadow-sm">
                        <i class="bi bi-send-fill me-2"></i> Kirim Permintaan Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(function() {
        // Objek untuk menyimpan produk yang dipilih dan kuantitasnya
        let selectedProducts = {};

        var productsSelectionTable = $('#products-selection-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('stock-transfers.products_for_selection_data') !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'item_code', name: 'item_code' },
                { data: 'name', name: 'name' },
                { data: 'price', name: 'price' },
                { data: 'quantity_input', name: 'quantity_input', orderable: false, searchable: false }
            ],
            order: [[0, 'asc']],
            drawCallback: function() {
                // Setelah tabel digambar ulang, perbarui nilai input kuantitas
                $('.product-quantity').each(function() {
                    const productId = $(this).data('product-id');
                    if (selectedProducts[productId]) {
                        $(this).val(selectedProducts[productId]);
                    } else {
                        $(this).val(0); // Atur ke 0 jika tidak dipilih
                    }
                });
            }
        });

        // Event listener untuk tombol plus (+)
        $('#products-selection-table tbody').on('click', '.btn-plus', function() {
            const productId = $(this).data('product-id');
            const quantityInput = $('.product-quantity[data-product-id="' + productId + '"]');
            let currentQuantity = parseInt(quantityInput.val());
            currentQuantity++;
            quantityInput.val(currentQuantity);
            selectedProducts[productId] = currentQuantity;
            updateHiddenProductsInput();
        });

        // Event listener untuk tombol minus (-)
        $('#products-selection-table tbody').on('click', '.btn-minus', function() {
            const productId = $(this).data('product-id');
            const quantityInput = $('.product-quantity[data-product-id="' + productId + '"]');
            let currentQuantity = parseInt(quantityInput.val());
            if (currentQuantity > 0) {
                currentQuantity--;
                quantityInput.val(currentQuantity);
                selectedProducts[productId] = currentQuantity;
                updateHiddenProductsInput();
            }
        });

        // Event listener untuk perubahan langsung pada input kuantitas
        $('#products-selection-table tbody').on('change', '.product-quantity', function() {
            const productId = $(this).data('product-id');
            let currentQuantity = parseInt($(this).val());
            if (isNaN(currentQuantity) || currentQuantity < 0) {
                currentQuantity = 0;
                $(this).val(0);
            }
            selectedProducts[productId] = currentQuantity;
            updateHiddenProductsInput();
        });

        // Fungsi untuk memperbarui input tersembunyi 'products[]'
        function updateHiddenProductsInput() {
            $('#stockTransferForm input[name^="products"]').remove(); // Hapus input lama
            let hasSelectedProducts = false;
            for (const productId in selectedProducts) {
                if (selectedProducts[productId] > 0) {
                    hasSelectedProducts = true;
                    // Tambahkan input tersembunyi untuk setiap produk yang dipilih
                    $('#stockTransferForm').append(
                        `<input type="hidden" name="products[${productId}][product_id]" value="${productId}">` +
                        `<input type="hidden" name="products[${productId}][quantity]" value="${selectedProducts[productId]}">`
                    );
                }
            }

            // Tampilkan atau sembunyikan pesan validasi produk
            if (!hasSelectedProducts) {
                $('#productSelectionMessage').show();
            } else {
                $('#productSelectionMessage').hide();
            }
        }

        // Panggil saat halaman dimuat untuk inisialisasi
        updateHiddenProductsInput();

        // Validasi formulir sebelum submit
        $('#stockTransferForm').on('submit', function(e) {
            updateHiddenProductsInput(); // Pastikan input tersembunyi diperbarui

            // Periksa apakah ada produk yang dipilih dengan kuantitas > 0
            let anyProductSelected = false;
            for (const productId in selectedProducts) {
                if (selectedProducts[productId] > 0) {
                    anyProductSelected = true;
                    break;
                }
            }

            if (!anyProductSelected) {
                e.preventDefault(); // Hentikan pengiriman formulir
                $('#productSelectionMessage').show();
                $('html, body').animate({
                    scrollTop: $('#productSelectionMessage').offset().top - 100
                }, 500);
                return false;
            }
            $('#productSelectionMessage').hide();
        });

        // Jika pengguna adalah gudang, atur gudang pengirim secara otomatis dan nonaktifkan
        @if ($userWarehouse && !Auth::user()->hasRole('owner'))
            $('#receiver_warehouse_id').val('{{ $userWarehouse->id }}').prop('disabled', true);
        @endif
    });
</script>
@endpush

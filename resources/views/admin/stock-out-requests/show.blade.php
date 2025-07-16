{{-- Asumsi Anda memiliki file layout seperti 'layouts/admin.blade.php' --}}
@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Permintaan Stok Keluar #{{ $stockOutRequest->id }}</h1>
        <a href="{{ route('stock-out-requests.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Kembali ke Daftar Permintaan
        </a>
    </div>

    <div class="card shadow-sm rounded-md mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informasi Permintaan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    @php
                        $statusColor = [
                            'completed' => 'success',
                            'pending' => 'warning',
                            'Perlu dikirim' => 'warning',
                            'shipping' => 'secondary',
                        ][$stockOutRequest->status] ?? 'info';

                        $statusColorItems = [
                            'requested' => 'secondary',
                            'transferred' => 'info',
                            'received' => 'success',
                            'cancelled' => 'danger'
                        ];
                    @endphp
                    <p><strong>Tanggal Permintaan:</strong> {{ $stockOutRequest->request_date ? $stockOutRequest->request_date->format('d M Y') : '-' }}</p>
                    <p><strong>Tanggal Tiba Diinginkan:</strong> {{ $stockOutRequest->desired_arrival_date ? $stockOutRequest->desired_arrival_date->format('d M Y') : '-' }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $statusColor }}">{{ ucfirst($stockOutRequest->status) }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Pengirim:</strong> 
                        {{ optional($stockOutRequest->sender)?->name 
                            ? "{$stockOutRequest->sender->name} ({$stockOutRequest->sender->area} - {$stockOutRequest->sender->city})" 
                            : 'N/A' }}
                    </p>
                    <p><strong>Penerima:</strong>
                        {{ optional($stockOutRequest->receiver)?->name 
                            ? "{$stockOutRequest->receiver->name} ({$stockOutRequest->receiver->area} - {$stockOutRequest->receiver->city})" 
                            : 'N/A' }}
                    </p>

                    <p><strong>Dibuat Oleh:</strong> {{ $stockOutRequest->createdBy->name ?? 'System' }}</p>
                </div>
            </div>
            <p><strong>Catatan:</strong> {{ $stockOutRequest->notes ?? '-' }}</p>

            @can('approval stock request')
                @if ($stockOutRequest->status === 'pending')
                    <form action="{{ route('stock-out-requests.process', $stockOutRequest->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memproses transfer stok ini? Ini akan mengurangi stok dari gudang pengirim dan menambahkannya ke gudang penerima.');" class="mt-3">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success rounded-md shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i> Proses Transfer Ini
                        </button>
                    </form>
                @elseif ($stockOutRequest->status === 'completed')
                    <div class="alert alert-success mt-3 rounded-md shadow-sm">Transfer stok ini telah selesai diproses.</div>
                @endif
            @endcan

            <div class="d-flex gap-1">
                @can('print stock request')
                <form action="{{ route('stock-out-requests.print', $stockOutRequest->id) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-warning rounded-md shadow-sm text-black">
                        <i class="bi bi-printer-fill me-2"></i> Cetak Dokumen
                    </button>
                </form>
                @endcan
                @if ($stockOutRequest->items()->first()?->status === 'requested' && $stockOutRequest->document_printed_at)
                    @role('gudang')
                        <form action="{{ route('stock-out-requests.send', $stockOutRequest->id) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-success rounded-md shadow-sm text-white">
                                <i class="bi bi-truck"></i> Kirim
                            </button>
                        </form>
                    @endrole
                @endif
                @if ($stockOutRequest->status === 'shipping')
                    @role('staff')
                        <form action="{{ route('stock-out-requests.force-update', $stockOutRequest->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Apakah Anda yakin ingin melakukan force update? Tindakan ini akan langsung memperbarui jumlah stok.');">
                            @csrf
                            <button type="submit" class="btn btn-danger rounded-md shadow-sm text-white">
                                <i class="bi bi-truck"></i> Force Update
                            </button>
                        </form>
                    @endrole
                @endif
            </div>
        </div>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-header">
            <h5 class="mb-0">Daftar Produk</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Produk</th>
                            <th scope="col">Kode Item</th>
                            <th scope="col">Kuantitas</th>
                            <th scope="col">Status Item</th>
                            <th scope="col">QR Scan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockOutRequest->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                <td>{{ $item->product->item_code ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td><span class="badge bg-{{ $statusColorItems[$item->status] ?? 'secondary' }}">{{ ucfirst($item->status) }}</span></td>
                                <td>
                                    <img src="{{ $item->qr_scan_url }}" width="60" alt="QR Code">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada item dalam permintaan transfer ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Asumsi Anda memiliki file layout seperti 'layouts/admin.blade.php' --}}
@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Permintaan Transfer Stok #{{ $stockTransferRequestModel->id }}</h1>
        <a href="{{ route('stock-transfers.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Kembali ke Daftar Transfer
        </a>
    </div>

    <div class="card shadow-sm rounded-md mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informasi Permintaan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tanggal Permintaan:</strong> {{ $stockTransferRequestModel->request_date ? $stockTransferRequestModel->request_date->format('d M Y') : '-' }}</p>
                    <p><strong>Tanggal Tiba Diinginkan:</strong> {{ $stockTransferRequestModel->desired_arrival_date ? $stockTransferRequestModel->desired_arrival_date->format('d M Y') : '-' }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $stockTransferRequestModel->status === 'completed' ? 'success' : ($stockTransferRequestModel->status === 'pending' ? 'warning' : 'info') }}">{{ ucfirst($stockTransferRequestModel->status) }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Gudang Pengirim:</strong> {{ $stockTransferRequestModel->senderWarehouse->name ?? 'N/A' }}</p>
                    <p><strong>Gudang Penerima:</strong> {{ $stockTransferRequestModel->receiverWarehouse->name ?? 'N/A' }}</p>
                    <p><strong>Dibuat Oleh:</strong> {{ $stockTransferRequestModel->createdBy->name ?? 'System' }}</p>
                </div>
            </div>
            <p><strong>Catatan:</strong> {{ $stockTransferRequestModel->notes ?? '-' }}</p>

            @can('process stock transfer')
                @if ($stockTransferRequestModel->status === 'pending')
                    <form action="{{ route('stock-transfers.process_transfer', $stockTransferRequestModel->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memproses transfer stok ini? Ini akan mengurangi stok dari gudang pengirim dan menambahkannya ke gudang penerima.');" class="mt-3">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success rounded-md shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i> Proses Transfer Ini
                        </button>
                    </form>
                @elseif ($stockTransferRequestModel->status === 'completed')
                    <div class="alert alert-success mt-3 rounded-md shadow-sm">Transfer stok ini telah selesai diproses.</div>
                @endif
            @endcan
        </div>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-header">
            <h5 class="mb-0">Item Transfer</h5>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockTransferRequestModel->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                <td>{{ $item->product->item_code ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($item->status) }}</span></td>
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

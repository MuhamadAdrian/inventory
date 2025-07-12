<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $stockOutRequest->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 20px;
        }

        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }

        .details-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .details-table td {
            padding: 5px;
            vertical-align: top;
        }

        .product-table th, .product-table td {
            border: 1px solid #ccc;
            padding: 6px;
            font-size: 0.9rem;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .signature-box {
            width: 30%;
        }

        .signature-box.middle {
            width: 35%;
        }

        @media print {
            body {
                margin: 0;
            }

            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h4>Surat Jalan</h4>
        <p>#{{ $stockOutRequest->id }} | Dicetak: {{ now()->format('d-m-Y H:i') }}</p>
    </div>

    <table class="details-table">
        <tr>
            <td><strong>Tanggal Pengiriman:</strong> {{ $stockOutRequest->request_date->format('d-m-Y') }}</td>
            <td><strong>Estimasi Tiba:</strong> {{ $stockOutRequest->desired_arrival_date->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td><strong>Status:</strong> Dikirim</td>
            <td><strong>Catatan:</strong> {{ $stockOutRequest->notes ?? '-' }}</td>
        </tr>
        <tr>
            <td>
              <p><strong>Pengirim:</strong> 
                {{ optional($stockOutRequest->sender)?->name
                  ? "{$stockOutRequest->sender->name} ({$stockOutRequest->sender->area} - {$stockOutRequest->sender->city})"
                  : 'N/A' }}
              </p>
            </td>
            <td>
              <p><strong>Penerima:</strong>
                {{ optional($stockOutRequest->receiver)?->name
                  ? "{$stockOutRequest->receiver->name} ({$stockOutRequest->receiver->area} - {$stockOutRequest->receiver->city})"
                  : 'N/A' }}
              </p>
            </td>
        </tr>
        <tr>
            <td colspan="2"><strong>Dibuat oleh:</strong> {{ $stockOutRequest->createdBy->name }}</td>
        </tr>
    </table>

    <table class="table product-table">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Kode Item</th>
                <th>Kuantitas</th>
                <th>QR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockOutRequest->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->product->item_code }}</td>
                <td>{{ $item->quantity }}</td>
                <td>
                    <img src="{{ $item->qr_scan_url }}" width="60" alt="QR Code">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Pengirim</strong> ( {{ ucfirst($stockOutRequest->createdBy->getRoleNames()[0]) }} )</p>
            <br><br><br>
            <p>( {{ $stockOutRequest->createdBy->name }} )</p>
        </div>
        <div class="signature-box middle">
            <p><strong>Mengetahui</strong> ( {{ ucfirst($stockOutRequest->approver->getRoleNames()[0]) }} )</p>
            <br><br><br>
            <p>( {{ $stockOutRequest->approver->name }} )</p>
        </div>
        <div class="signature-box">
            <p><strong>Penerima</strong></p>
            <br><br><br>
            <p>(..................................)</p>
        </div>
    </div>

    <div class="print-button text-center mt-4">
        <button class="btn btn-primary btn-lg" onclick="window.print()">
            <i class="bi bi-printer-fill me-2"></i> Cetak Surat Jalan
        </button>
    </div>
</body>
</html>
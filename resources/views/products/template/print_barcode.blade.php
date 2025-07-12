<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode - {{ $product->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #fff;
            margin: 0;
            padding: 0;
            margin: 0 auto;
            max-width: 800px;
        }

        .barcode-wrapper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 0;
            margin: 0;
        }

        .print-area {
            page-break-inside: avoid;
            box-shadow: none;
            border: none;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .barcode-image {
            max-width: 100%;
            height: auto;
            margin: 0 auto;
            padding: 5px;
        }

        .product-info h3 {
            font-size: 1rem;
            margin-bottom: 2px;
        }

        .product-info p {
            font-size: 0.7rem;
            margin-bottom: 2px;
        }

        .print-button-container {
            margin-top: 40px;
            text-align: center;
        }

        @media print {
            body {
                background-color: #fff;
                margin: 0;
                padding: 0;
            }

            .barcode-wrapper {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                padding: 0;
                margin: 0;
            }

            .print-area {
                page-break-inside: avoid;
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
                text-align: center;
                border: 1px dotted #ccc
            }

            .print-button-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="barcode-wrapper">
        @foreach(range(1, $barcodeCount ?? 1) as $i)
            <div class="print-area">
                @if ($barcodeBase64)
                    <img src="{{ $barcodeBase64 }}" alt="Product Barcode" class="barcode-image">
                @else
                    <p class="text-danger">Barcode not available for this product.</p>
                @endif

                <div class="product-info">
                    <h3>{{ $product->name }}</h3>
                    <p>{{ $product->item_code ?? '-' }}</p>
                    <p>{{ $product->formatted_price }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="print-button-container">
        <button type="button" class="btn btn-primary btn-lg" onclick="window.print()">
            <i class="bi bi-printer-fill me-2"></i> Print All Barcodes
        </button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-lg ms-2">
            Back to Products
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
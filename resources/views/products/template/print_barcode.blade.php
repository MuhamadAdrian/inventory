<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode - {{ $product->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .print-area {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px; /* Adjust max width for barcode label */
            width: 100%;
        }
        .barcode-image {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px auto;
            border: 1px solid #ccc; /* Add a subtle border for clarity */
            padding: 5px;
        }
        .product-info {
            margin-top: 15px;
            font-size: 1rem;
            color: #333;
        }
        .product-info h3 {
            margin-bottom: 5px;
            font-size: 1.25rem;
            color: #000;
        }
        .product-info p {
            margin-bottom: 3px;
            font-size: 0.9rem;
        }
        .print-button-container {
            margin-top: 30px;
        }

        /* Print specific styles */
        @media print {
            body {
                background-color: #fff;
                margin: 0;
                padding: 0;
                display: block; /* Override flex for print */
                min-height: auto;
            }
            .print-area {
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: none;
                width: auto;
            }
            .print-button-container {
                display: none; /* Hide print button when printing */
            }
        }
    </style>
</head>
<body>
    <div class="print-area">
        <h2 class="mb-3">Product Barcode</h2>
        
        @if ($barcodeBase64)
            <img src="{{ $barcodeBase64 }}" alt="Product Barcode" class="barcode-image">
        @else
            <p class="text-danger">Barcode not available for this product.</p>
        @endif

        <div class="product-info">
            <h3>{{ $product->name }}</h3>
            <p><strong>Item Code:</strong> {{ $product->item_code ?? '-' }}</p>
            <p><strong>Price:</strong> Rp {{ number_format($product->price, 2, ',', '.') }}</p>
            <p><strong>Category:</strong> {{ $product->category ?? '-' }}</p>
            <p><strong>Brand:</strong> {{ $product->brand ?? '-' }}</p>
        </div>

        <div class="print-button-container">
            <button type="button" class="btn btn-primary btn-lg" onclick="window.print()">
                <i class="bi bi-printer-fill me-2"></i> Print Barcode
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-lg ms-2">
                Back to Products
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Include Bootstrap Icons if you use them, e.g., for the print icon --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>

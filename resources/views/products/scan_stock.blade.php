@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Scan Barcode & Adjust Stock</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-md shadow-sm">
            Back to Products
        </a>
    </div>

    <div class="card shadow-sm rounded-md">
        <div class="card-body">
            <div class="mb-3">
                <label for="barcodeInput" class="form-label">Scan Barcode or Enter Item Code</label>
                <input type="text" class="form-control rounded-md" id="barcodeInput" placeholder="Scan or type here..." autofocus>
                <small class="form-text text-muted">Press Enter after typing the code.</small>
            </div>

            <hr>

            <div id="productDetails" class="mt-4" style="display: none;">
                <h4 class="mb-3">Product Details:</h4>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="border p-3 rounded-md bg-light mb-3">
                            <img id="productBarcodeImage" src="" alt="Product Barcode" style="max-width: 150px; height: auto; display: block; margin: 0 auto;">
                            <small class="text-muted d-block mt-2">Barcode</small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <p><strong>Product Name:</strong> <span id="productName"></span></p>
                        <p><strong>Item Code:</strong> <span id="productItemCode"></span></p>
                        <p><strong>Current Stock:</strong> <span id="productCurrentStock" class="fw-bold"></span></p>
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Adjust Stock:</h5>
                <form id="stockAdjustmentForm">
                    @csrf
                    <input type="hidden" id="hiddenItemCode" name="item_code_or_barcode">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="quantityChange" class="form-label">Quantity Change</label>
                            <input type="number" class="form-control rounded-md" id="quantityChange" name="quantity_change" value="1" required>
                            <small class="form-text text-muted">Use positive for increase, negative for decrease.</small>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success rounded-md w-100">
                                <i class="bi bi-arrow-repeat me-2"></i> Update Stock
                            </button>
                        </div>
                    </div>
                </form>
                <div id="stockMessage" class="mt-3"></div>
            </div>

            <div id="noProductFound" class="alert alert-warning mt-4" style="display: none;">
                No product found for the scanned barcode/item code.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- jQuery (if not already loaded) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(function() {
        const barcodeInput = $('#barcodeInput');
        const productDetails = $('#productDetails');
        const noProductFound = $('#noProductFound');
        const stockAdjustmentForm = $('#stockAdjustmentForm');
        const stockMessage = $('#stockMessage');

        let debounceTimer;

        // Function to fetch product details
        function fetchProductDetails(identifier) {
            if (!identifier) {
                productDetails.hide();
                noProductFound.hide();
                return;
            }

            $.ajax({
                url: '{{ route('products.adjust_stock') }}', // Use the adjust_stock route for lookup
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    item_code_or_barcode: identifier,
                    quantity_change: 0 // Pass 0 to only lookup, not change stock
                },
                beforeSend: function() {
                    productDetails.hide();
                    noProductFound.hide();
                    stockMessage.empty();
                    barcodeInput.prop('disabled', true); // Disable input during request
                },
                success: function(response) {
                    if (response.success && response.product) {
                        $('#productName').text(response.product.name);
                        $('#productItemCode').text(response.product.item_code);
                        $('#productCurrentStock').text(response.product.current_stock);
                        $('#productBarcodeImage').attr('src', response.product.barcode_image);
                        $('#hiddenItemCode').val(response.product.item_code); // Set hidden field for form submission
                        productDetails.show();
                        stockMessage.html('<div class="alert alert-info">Product loaded. Adjust quantity below.</div>');
                    } else {
                        noProductFound.show();
                    }
                },
                error: function(xhr) {
                    noProductFound.show();
                    let errorMessage = 'An error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    stockMessage.html('<div class="alert alert-danger">' + errorMessage + '</div>');
                },
                complete: function() {
                    barcodeInput.prop('disabled', false); // Re-enable input
                    barcodeInput.focus(); // Keep focus on the barcode input
                }
            });
        }

        // Handle barcode input on keyup (simulates scanner or manual type + Enter)
        barcodeInput.on('keyup', function(e) {
            clearTimeout(debounceTimer); // Clear previous timer
            const identifier = $(this).val().trim();

            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent form submission if input is inside a form
                fetchProductDetails(identifier);
                $(this).val(''); // Clear input after processing
            } else {
                // Optional: debounce for continuous typing if not using Enter
                // debounceTimer = setTimeout(() => {
                //     fetchProductDetails(identifier);
                // }, 500);
            }
        });

        // Handle stock adjustment form submission
        stockAdjustmentForm.on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const currentItemCode = $('#hiddenItemCode').val(); // Get the item code of the product being adjusted

            $.ajax({
                url: '{{ route('products.adjust_stock') }}',
                method: 'POST',
                data: formData,
                beforeSend: function() {
                    stockAdjustmentForm.find('button').prop('disabled', true).text('Updating...');
                    stockMessage.empty();
                },
                success: function(response) {
                    if (response.success) {
                        $('#productCurrentStock').text(response.product.current_stock); // Update displayed stock
                        stockMessage.html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#quantityChange').val('1'); // Reset quantity change input
                    } else {
                        stockMessage.html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred during stock adjustment.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        // Handle validation errors
                        errorMessage = 'Validation Error:';
                        for (const field in xhr.responseJSON.errors) {
                            errorMessage += '<br>' + xhr.responseJSON.errors[field].join(', ');
                        }
                    }
                    stockMessage.html('<div class="alert alert-danger">' + errorMessage + '</div>');
                },
                complete: function() {
                    stockAdjustmentForm.find('button').prop('disabled', false).html('<i class="bi bi-arrow-repeat me-2"></i> Update Stock');
                    barcodeInput.focus(); // Keep focus on the barcode input
                }
            });
        });

        // Initial focus on barcode input when page loads
        barcodeInput.focus();
    });
</script>
@endpush

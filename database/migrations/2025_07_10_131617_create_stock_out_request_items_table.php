<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('stock_out_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_request_id')->constrained('stock_out_requests')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->enum('status', ['requested', 'transferred', 'received', 'rejected'])->default('requested');
            $table->timestamps();

            $table->unique(['stock_out_request_id', 'product_id'], 'stock_out_request_item_unique');
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_out_request_items');
    }
};

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
        Schema::create('stock_out_requests', function (Blueprint $table) {
            $table->id();
            $table->date('request_date')->default(now()); // Tanggal permintaan dibuat
            $table->date('desired_arrival_date')->nullable(); // Tanggal diinginkan barang sampai
            $table->foreignId('sender_id')->constrained('business_locations')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('business_locations')->onDelete('cascade');
            $table->enum('status', ['pending', 'processing', 'shipping', 'completed', 'cancelled'])->default('pending'); // Status: pending, processing, completed, cancelled
            $table->text('notes')->nullable();
            $table->morphs('created_by');
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_out_requests');
    }
};

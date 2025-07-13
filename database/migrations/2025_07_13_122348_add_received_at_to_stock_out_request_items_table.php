<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_out_request_items', function (Blueprint $table) {
            $table->timestamp('received_at')->nullable()->after('status')->comment('Timestamp when the stock out request item was received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_out_request_items', function (Blueprint $table) {
            $table->dropColumn('received_at');
        });
    }
};

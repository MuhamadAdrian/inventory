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
        Schema::table('products', function (Blueprint $table) {
            $table->string('item_code')->unique()->nullable()->after('name');
            $table->string('color')->nullable()->after('item_code');
            $table->string('series')->nullable()->after('color');
            $table->string('category')->nullable()->after('series');
            $table->string('material')->nullable()->after('category');
            $table->string('size')->nullable()->after('material');
            $table->decimal('weight', 8, 2)->nullable()->after('size');
            $table->string('brand')->nullable()->after('weight');
            $table->timestamp('barcode_print_at')->nullable()->after('brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'item_code',
                'color',
                'series',
                'category',
                'material',
                'size',
                'weight',
                'brand',
            ]);
        });
    }
};

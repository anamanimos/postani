<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('sku')->unique()->nullable();
            $table->string('name');
            $table->foreignId('buy_unit_id')->constrained('units')->onDelete('restrict');
            $table->foreignId('sell_unit_id')->constrained('units')->onDelete('restrict');
            $table->decimal('conversion_factor', 12, 4)->default(1.0000);
            $table->decimal('last_purchase_price', 15, 2)->default(0.00);
            $table->decimal('avg_purchase_price', 15, 2)->default(0.00);
            $table->decimal('selling_price', 15, 2)->default(0.00);
            $table->decimal('stock', 12, 2)->default(0.00);
            $table->decimal('min_stock', 12, 2)->default(0.00);
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

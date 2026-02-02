<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('shipping_package_id')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('cost_per_item', 10, 2)->nullable();
            $table->string('inventory_management')->nullable();
            $table->string('inventory_policy')->nullable();
            $table->string('inventory_status')->nullable();
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('weight_unit')->nullable();
            $table->unsignedBigInteger('inventoryItem_id')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('image_id')->references('id')->on('uploads')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable()->unique();
            $table->string('name');
            $table->string('type')->default('product'); // product | service
            $table->string('hsn_sac')->nullable();
            $table->string('unit')->nullable(); // pcs, nos, hrs, etc.
            $table->decimal('price', 15, 2)->default(0); // selling price
            $table->decimal('cost_price', 15, 2)->nullable(); // optional
            $table->decimal('tax_percent', 8, 2)->default(0); // GST %
            $table->boolean('is_inventory')->default(true); // track stock for products
            $table->decimal('stock', 12, 2)->default(0); // qty in stock
            $table->text('description')->nullable();
            $table->json('meta')->nullable(); // for extra fields
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->integer('position')->nullable();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('handle')->unique();
            $table->string('collection_type')->nullable();
            $table->string('condition_type')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->integer('level')->default(0);
            $table->string('path')->nullable();
            $table->integer('path_levels')->default(0);
            $table->boolean('is_subcategory')->default(0);
            $table->integer('hierarchy_depth')->default(0);
            $table->integer('display_order')->default(0);
            $table->boolean('show_products_at_level')->default(1);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('collections')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};

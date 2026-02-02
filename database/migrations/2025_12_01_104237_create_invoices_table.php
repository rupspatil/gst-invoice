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
        Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number')->unique();
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
    $table->date('invoice_date');
    $table->date('due_date')->nullable();
    $table->decimal('sub_total', 15, 2)->default(0);
    $table->decimal('discount', 12,2)->default(0);
    $table->decimal('tax_total', 15,2)->default(0);
    $table->decimal('round_off', 10,2)->default(0);
    $table->decimal('grand_total', 15,2)->default(0);
    $table->decimal('cgst', 10,2)->default(0);
    $table->decimal('sgst', 10,2)->default(0);
    $table->decimal('igst', 10,2)->default(0);
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

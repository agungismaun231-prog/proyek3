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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict');
            $table->integer('quantity'); // Jumlah service
            $table->decimal('unit_price', 12, 2); // Harga satuan saat transaksi
            $table->decimal('subtotal', 12, 2); // quantity * unit_price
            $table->text('description')->nullable(); // Deskripsi detail service
            $table->timestamps();
            
            // Index
            $table->index('transaction_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};

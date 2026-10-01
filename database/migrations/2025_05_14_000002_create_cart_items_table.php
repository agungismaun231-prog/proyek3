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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_cart_id')->constrained('shopping_carts')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->integer('quantity')->default(1); // Jumlah service
            $table->decimal('unit_price', 12, 2); // Harga per unit pada saat ditambahkan
            $table->decimal('subtotal', 12, 2); // quantity * unit_price
            $table->text('notes')->nullable(); // Catatan khusus untuk service
            $table->timestamps();
            
            // Index
            $table->index('shopping_cart_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};

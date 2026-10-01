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
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('subtotal', 12, 2)->default(0); // Total sebelum pajak
            $table->decimal('tax', 12, 2)->default(0); // Pajak
            $table->decimal('discount_amount', 12, 2)->default(0); // Besaran diskon
            $table->string('discount_code')->nullable(); // Kode diskon yang dipakai
            $table->decimal('total', 12, 2)->default(0); // Total akhir
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_carts');
    }
};

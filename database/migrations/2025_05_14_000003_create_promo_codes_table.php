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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode promo unik (misal: SUMMER2025)
            $table->text('description')->nullable(); // Deskripsi promo
            $table->enum('discount_type', ['percentage', 'fixed_amount']); // % atau Rp
            $table->decimal('discount_value', 12, 2); // Nilai diskon (% atau Rp)
            $table->decimal('max_discount', 12, 2)->nullable(); // Max diskon (utk percentage)
            $table->decimal('min_purchase', 12, 2)->default(0); // Minimal pembelian
            $table->integer('usage_limit')->nullable(); // Total limit penggunaan
            $table->integer('usage_per_customer')->default(1); // Limit per customer
            $table->integer('times_used')->default(0); // Sudah dipakai berapa kali
            $table->dateTime('valid_from'); // Mulai berlaku
            $table->dateTime('valid_until'); // Berakhir berlaku
            $table->boolean('is_active')->default(true); // Aktif atau tidak
            $table->timestamps();
            
            // Index
            $table->index('code');
            $table->index('is_active');
            $table->index('valid_from');
            $table->index('valid_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};

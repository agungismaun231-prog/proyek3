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
        Schema::create('payment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->decimal('amount', 12, 2); // Jumlah pembayaran pada event ini
            $table->string('payment_method'); // Metode pembayaran
            $table->string('payment_reference')->nullable(); // Ref dari bank/gateway
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable(); // Keterangan/catatan
            $table->json('gateway_data')->nullable(); // Response dari gateway
            $table->timestamps();
            
            // Index
            $table->index('transaction_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_history');
    }
};

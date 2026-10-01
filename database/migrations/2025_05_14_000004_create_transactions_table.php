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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('transaction_number')->unique(); // Nomor transaksi: TRX-YYYYMMDD-001
            $table->decimal('subtotal', 12, 2); // Total sebelum pajak/diskon
            $table->decimal('tax', 12, 2)->default(0); // Pajak
            $table->decimal('discount_amount', 12, 2)->default(0); // Besaran diskon
            $table->string('promo_code')->nullable(); // Kode promo yang dipakai
            $table->decimal('total', 12, 2); // Total akhir yang harus dibayar
            
            // Payment Gateway Integration
            $table->enum('payment_method', [
                'credit_card',
                'bank_transfer', 
                'e_wallet',
                'cash',
                'check'
            ])->default('credit_card');
            $table->string('payment_gateway')->nullable(); // Midtrans, Xendit, dll
            $table->string('gateway_transaction_id')->nullable(); // ID dari payment gateway
            $table->decimal('amount_paid', 12, 2)->default(0); // Jumlah yang sudah dibayar
            $table->string('payment_reference')->nullable(); // Referensi pembayaran bank/kartu
            $table->string('payment_proof')->nullable(); // Path bukti pembayaran
            
            // Transaction Status
            $table->enum('status', [
                'pending',
                'processing', 
                'awaiting_payment',
                'paid',
                'partially_paid',
                'completed',
                'cancelled',
                'failed',
                'refunded'
            ])->default('pending');
            
            // Payment Status Details
            $table->text('gateway_response')->nullable(); // Response lengkap dari gateway
            $table->string('gateway_status')->nullable(); // Status dari gateway
            
            // Timeline
            $table->dateTime('submitted_date')->nullable(); // Saat transaksi dibuat
            $table->dateTime('payment_date')->nullable(); // Saat pembayaran diterima
            $table->dateTime('completed_date')->nullable(); // Saat transaksi selesai
            
            // Admin Fields
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_date')->nullable(); // Saat approval
            $table->text('admin_notes')->nullable(); // Catatan admin
            
            // Invoice Link
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            
            // Metadata
            $table->json('metadata')->nullable(); // Custom data, browser info, dll
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index('user_id');
            $table->index('transaction_number');
            $table->index('status');
            $table->index('payment_method');
            $table->index('gateway_transaction_id');
            $table->index('promo_code');
            $table->index('created_at');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

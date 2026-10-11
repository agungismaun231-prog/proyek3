<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Menambahkan metode pembayaran 'qris' pada kolom payments.payment_method.
 * Metode lama (cash, bank_transfer, credit_card, e_wallet, check) tidak berubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('cash','bank_transfer','credit_card','e_wallet','check','qris') NOT NULL DEFAULT 'bank_transfer' COMMENT 'Payment method: cash, bank_transfer, credit_card, e_wallet, check, qris'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::table('payments')->where('payment_method', 'qris')->update(['payment_method' => 'e_wallet']);

        DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('cash','bank_transfer','credit_card','e_wallet','check') NOT NULL DEFAULT 'bank_transfer' COMMENT 'Payment method: cash, bank_transfer, credit_card, e_wallet, check'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom category pada tabel services.
 * Layanan yang sudah ada otomatis menjadi kategori 'AC',
 * layanan baru (misalnya 'Gadget') mengisi kategori sendiri.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'category')) {
                $table->string('category', 100)->default('AC')->after('duration_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update bookings status enum to include 'en_route'
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'en_route', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");

        // 2. Add service_location and cancellation_reason to bookings
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'service_location')) {
                $table->string('service_location')->nullable()->after('scheduled_date');
            }
            if (!Schema::hasColumn('bookings', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('completion_notes');
            }
        });

        // 3. Add description, years_experience, photo to technicians
        Schema::table('technicians', function (Blueprint $table) {
            if (!Schema::hasColumn('technicians', 'description')) {
                $table->text('description')->nullable()->after('specialization');
            }
            if (!Schema::hasColumn('technicians', 'years_experience')) {
                $table->unsignedSmallInteger('years_experience')->default(0)->after('description');
            }
            if (!Schema::hasColumn('technicians', 'photo')) {
                $table->string('photo')->nullable()->after('years_experience');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technicians', function (Blueprint $table) {
            if (Schema::hasColumn('technicians', 'photo')) {
                $table->dropColumn('photo');
            }
            if (Schema::hasColumn('technicians', 'years_experience')) {
                $table->dropColumn('years_experience');
            }
            if (Schema::hasColumn('technicians', 'description')) {
                $table->dropColumn('description');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
            if (Schema::hasColumn('bookings', 'service_location')) {
                $table->dropColumn('service_location');
            }
        });

        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};

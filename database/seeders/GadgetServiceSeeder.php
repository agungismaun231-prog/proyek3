<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Seeder layanan kategori Gadget.
 * Aman dijalankan berulang kali (firstOrCreate berdasarkan nama),
 * sehingga bisa dipakai di database yang sudah berisi data:
 *   php artisan db:seed --class=GadgetServiceSeeder
 */
class GadgetServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Service Smartphone',
                'description' => 'Diagnosa dan perbaikan kerusakan umum smartphone (mati total, restart sendiri, tidak bisa charging)',
                'price' => 150000,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Ganti Layar Smartphone',
                'description' => 'Penggantian layar LCD/touchscreen smartphone yang pecah atau tidak responsif',
                'price' => 350000,
                'duration_minutes' => 90,
            ],
            [
                'name' => 'Ganti Baterai Gadget',
                'description' => 'Penggantian baterai smartphone atau tablet yang boros, drop, atau menggembung',
                'price' => 200000,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'Service Laptop',
                'description' => 'Pembersihan, penggantian thermal paste, dan pengecekan performa laptop',
                'price' => 250000,
                'duration_minutes' => 120,
            ],
            [
                'name' => 'Instal Ulang & Software Gadget',
                'description' => 'Instal ulang sistem operasi, update software, dan pembersihan virus pada smartphone, tablet, atau laptop',
                'price' => 100000,
                'duration_minutes' => 60,
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                $service + ['category' => 'Gadget']
            );
        }
    }
}

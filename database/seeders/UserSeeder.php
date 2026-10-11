<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates test accounts for all roles:
     * - Admin (2 accounts)
     * - Customer (4 accounts)
     * - Technician (5 accounts)
     */
    public function run(): void
    {
        // ============================================
        // ADMIN ACCOUNTS (2)
        // ============================================
        
        User::firstOrCreate(
            ['email' => 'admin@jasaku.com'],
            [
                'name' => 'Admin Super',
                'password' => Hash::make('Admin@123456'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin2@jasaku.com'],
            [
                'name' => 'Admin Secondary',
                'password' => Hash::make('SecurePass123!'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // ============================================
        // CUSTOMER ACCOUNTS (4)
        // ============================================
        
        $customerUsers = [
            [
                'name' => 'Customer One',
                'email' => 'customer1@example.com',
                'password' => Hash::make('Customer@123'),
                'phone' => '081234567890',
                'address' => 'Jakarta Selatan',
                'city' => 'Jakarta',
                'postal_code' => '12000',
            ],
            [
                'name' => 'Budi Customer',
                'email' => 'budi@example.com',
                'password' => Hash::make('Budi@12345'),
                'phone' => '082345678901',
                'address' => 'Jakarta Pusat',
                'city' => 'Jakarta',
                'postal_code' => '10110',
            ],
            [
                'name' => 'Siti Customer',
                'email' => 'siti@example.com',
                'password' => Hash::make('Siti@12345'),
                'phone' => '085678901234',
                'address' => 'Tangerang',
                'city' => 'Tangerang',
                'postal_code' => '15111',
            ],
            [
                'name' => 'Test Customer',
                'email' => 'test.customer@example.com',
                'password' => Hash::make('TestPass@123'),
                'phone' => '081234567893',
                'address' => 'Bekasi',
                'city' => 'Bekasi',
                'postal_code' => '17111',
            ],
        ];

        foreach ($customerUsers as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ]
            );

            \App\Models\Customer::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'postal_code' => $data['postal_code'],
                ]
            );
        }

        // ============================================
        // TECHNICIAN ACCOUNTS (5)
        // ============================================
        
        $technicians = [
            [
                'name' => 'Ahmad Teknisi',
                'email' => 'ahmad.teknisi@provider.com',
                'password' => Hash::make('TechPass@123'),
                'role' => 'technician',
            ],
            [
                'name' => 'Budi Service',
                'email' => 'budi.service@provider.com',
                'password' => Hash::make('Service@123'),
                'role' => 'technician',
            ],
            [
                'name' => 'Doni Maintenance',
                'email' => 'doni.maintenance@provider.com',
                'password' => Hash::make('Maintain@123'),
                'role' => 'technician',
            ],
            [
                'name' => 'Roni Junior',
                'email' => 'roni.junior@provider.com',
                'password' => Hash::make('Junior@123'),
                'role' => 'technician',
            ],
            [
                'name' => 'Hendra Professional',
                'email' => 'hendra.pro@provider.com',
                'password' => Hash::make('ProTech@123'),
                'role' => 'technician',
            ],
        ];

        foreach ($technicians as $tech) {
            User::firstOrCreate(
                ['email' => $tech['email']],
                [
                    'name' => $tech['name'],
                    'password' => $tech['password'],
                    'role' => $tech['role'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

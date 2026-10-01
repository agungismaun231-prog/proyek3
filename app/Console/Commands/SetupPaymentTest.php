<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetupPaymentTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:setup-test {--status=pending} {--count=1}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Setup test payment transactions for quick testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->option('status');
        $count = (int) $this->option('count');

        // Validate status
        $validStatuses = ['pending', 'paid', 'failed', 'awaiting_payment', 'completed'];
        if (!in_array($status, $validStatuses)) {
            $this->error("Invalid status. Must be one of: " . implode(', ', $validStatuses));
            return Command::FAILURE;
        }

        // Create or get test customer
        $user = User::firstOrCreate(
            ['email' => 'testcustomer@example.com'],
            [
                'name' => 'Test Customer',
                'password' => bcrypt('password123'),
                'role' => 'customer'
            ]
        );

        Customer::firstOrCreate(
            ['user_id' => $user->id],
            ['phone' => '081234567890']
        );

        // Get or create test services
        $services = Service::limit(3)->get();
        if ($services->isEmpty()) {
            $service = Service::create([
                'name' => 'Test Service - AC Maintenance',
                'description' => 'Test service for payment simulation',
                'price' => 500000,
                'duration' => 2
            ]);
            $services = collect([$service]);
        }

        // Create transactions
        $this->info("Creating {$count} test transaction(s) with status: {$status}");
        $bar = $this->output->createProgressBar($count);

        for ($i = 1; $i <= $count; $i++) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_number' => 'TEST-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'subtotal' => 500000,
                'tax' => 50000,
                'discount_amount' => 0,
                'total' => 550000,
                'payment_method' => collect(['credit_card', 'e_wallet', 'bank_transfer'])->random(),
                'status' => $status,
                'submitted_date' => now()->subDays(rand(0, 30)),
                'payment_date' => in_array($status, ['paid', 'completed']) ? now()->subDays(rand(0, 10)) : null,
            ]);

            // Add transaction items
            foreach ($services->take(2) as $service) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'unit_price' => $service->price,
                    'subtotal' => $service->price,
                    'description' => $service->description
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Display credentials
        $this->info("✓ Test transactions created successfully!");
        $this->newLine();
        $this->table(['Field', 'Value'], [
            ['Customer Email', $user->email],
            ['Customer Password', 'password123'],
            ['Test Transactions', $count],
            ['Status', $status],
            ['Total Amount (each)', 'Rp 550.000'],
        ]);

        $this->newLine();
        $this->info("Next steps:");
        $this->line("1. Login: http://localhost/login");
        $this->line("2. Email: {$user->email}");
        $this->line("3. Password: password123");
        $this->line("4. View transactions: http://localhost/ecommerce/transactions");
        $this->line("5. Test payment: Click on any transaction → Click E-Wallet icon");

        return Command::SUCCESS;
    }
}

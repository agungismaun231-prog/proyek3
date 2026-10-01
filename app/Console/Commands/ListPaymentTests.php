<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;

class ListPaymentTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:list {--status=} {--limit=10}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'List test transactions for payment simulation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $status = $this->option('status');

        $query = Transaction::query();

        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->latest('created_at')->limit($limit)->get();

        if ($transactions->isEmpty()) {
            $this->warn('No transactions found. Run: php artisan payment:setup-test');
            return Command::SUCCESS;
        }

        // Build table data
        $data = $transactions->map(fn($t) => [
            'ID' => $t->id,
            'Number' => $t->transaction_number,
            'Status' => $this->formatStatus($t->status),
            'Amount' => 'Rp ' . number_format($t->total, 0, ',', '.'),
            'Method' => ucfirst(str_replace('_', ' ', $t->payment_method)),
            'Created' => $t->created_at->format('Y-m-d H:i'),
        ])->toArray();

        $this->table(['ID', 'Number', 'Status', 'Amount', 'Method', 'Created'], $data);

        $this->newLine();
        $this->info('Usage:');
        $this->line('  View transaction: http://localhost/ecommerce/transactions/{id}');
        $this->line('  Simulate payment: php artisan payment:simulate {id}');
        $this->line('  With notify:      php artisan payment:simulate {id} --notify');
        $this->line('  Change status:    php artisan payment:simulate {id} --status=failed');

        return Command::SUCCESS;
    }

    private function formatStatus($status)
    {
        return match($status) {
            'pending' => '🟡 ' . $status,
            'paid' => '🟢 ' . $status,
            'completed' => '✅ ' . $status,
            'failed' => '🔴 ' . $status,
            'awaiting_payment' => '⏳ ' . $status,
            'cancelled' => '❌ ' . $status,
            default => $status,
        };
    }
}

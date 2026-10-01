<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Console\Command;

class SimulatePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:simulate {transaction_id} {--status=paid} {--notify}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Simulate payment status change for a transaction';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactionId = $this->argument('transaction_id');
        $status = $this->option('status');
        $shouldNotify = $this->option('notify');

        // Find transaction
        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            $this->error("Transaction #{$transactionId} not found");
            return Command::FAILURE;
        }

        // Validate status
        $validStatuses = ['pending', 'paid', 'failed', 'awaiting_payment', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            $this->error("Invalid status. Must be one of: " . implode(', ', $validStatuses));
            return Command::FAILURE;
        }

        // Show current state
        $this->info("Current transaction state:");
        $this->table(['Field', 'Value'], [
            ['Transaction Number', $transaction->transaction_number],
            ['Amount', 'Rp ' . number_format($transaction->total, 0, ',', '.')],
            ['Current Status', $transaction->status],
            ['Payment Date', $transaction->payment_date ?? '-'],
        ]);

        // Update status
        $this->info("\nUpdating status to: {$status}");
        $updates = ['status' => $status];

        if (in_array($status, ['paid', 'completed'])) {
            $updates['payment_date'] = now();
        }

        $transaction->update($updates);
        $this->line("✓ Status updated");

        // Send notification if requested
        if ($shouldNotify && in_array($status, ['paid', 'completed'])) {
            $this->info("\nSending notification...");
            try {
                $transaction->user->notify(new PaymentSuccessNotification($transaction));
                $this->line("✓ Notification sent");
            } catch (\Exception $e) {
                $this->warn("⚠ Notification failed: " . $e->getMessage());
            }
        }

        // Show new state
        $this->info("\nNew transaction state:");
        $transaction->refresh();
        $this->table(['Field', 'Value'], [
            ['Transaction Number', $transaction->transaction_number],
            ['Amount', 'Rp ' . number_format($transaction->total, 0, ',', '.')],
            ['New Status', $transaction->status],
            ['Payment Date', $transaction->payment_date ?? '-'],
        ]);

        $this->newLine();
        $this->info("View transaction: http://localhost/ecommerce/transactions/{$transaction->id}");

        return Command::SUCCESS;
    }
}

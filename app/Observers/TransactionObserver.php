<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Handle the Transaction "updated" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        // Check jika status berubah menjadi paid atau completed
        if ($transaction->isDirty('status') && 
            in_array($transaction->status, ['paid', 'completed'])) {
            
            // Send notification ke user
            try {
                $transaction->user->notify(new PaymentSuccessNotification($transaction));
                
                Log::info('Payment success notification sent for transaction', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send payment notification', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}

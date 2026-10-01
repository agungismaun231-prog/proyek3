<?php

namespace App\Services\Payment;

use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Midtrans\Snap;

/**
 * MidtransPaymentService - Integrasi Midtrans Payment Gateway
 * 
 * Service untuk mengelola transaksi pembayaran melalui Midtrans (PT. Midtrans).
 * Midtrans mendukung: credit card, bank transfer, e-wallet, dan metode lokal lainnya.
 * 
 * Konfigurasi di .env:
 * MIDTRANS_MERCHANT_ID=G123456
 * MIDTRANS_CLIENT_KEY=VCXxxx...
 * MIDTRANS_SERVER_KEY=SB-Mid-server-xxx...
 * MIDTRANS_ENVIRONMENT=sandbox (or production)
 */
class MidtransPaymentService extends PaymentGatewayService
{
    protected string $merchantId;
    protected string $clientKey;
    protected string $serverKey;
    protected string $environment;

    public function __construct()
    {
        $this->merchantId = config('services.midtrans.merchant_id');
        $this->clientKey = config('services.midtrans.client_key');
        $this->serverKey = config('services.midtrans.server_key');
        $this->environment = config('services.midtrans.environment', 'sandbox');

        $this->setupMidtrans();
    }

    /**
     * Setup Midtrans library configuration
     */
    private function setupMidtrans(): void
    {
        Config::$serverKey = $this->serverKey;
        Config::$clientKey = $this->clientKey;
        Config::$isProduction = $this->environment === 'production';
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create transaction dan generate payment URL
     */
    public function createTransaction(Transaction $transaction): array
    {
        try {
            $transactionDetails = [
                'order_id' => $transaction->transaction_number,
                'gross_amount' => (int) $transaction->total,
            ];

            $customerDetails = [
                'first_name' => $transaction->user->name,
                'email' => $transaction->user->email,
                'phone' => $transaction->user->phone ?? '',
            ];

            // Build transaction items
            $items = [];
            foreach ($transaction->items as $item) {
                $items[] = [
                    'id' => 'item-' . $item->service_id,
                    'price' => (int) $item->unit_price,
                    'quantity' => $item->quantity,
                    'name' => $item->service->name,
                ];
            }

            // Tambahkan line item untuk tax
            if ($transaction->tax > 0) {
                $items[] = [
                    'id' => 'tax',
                    'price' => (int) $transaction->tax,
                    'quantity' => 1,
                    'name' => 'Pajak (10%)',
                ];
            }

            // Tambahkan line item untuk diskon (negative price)
            if ($transaction->discount_amount > 0) {
                $items[] = [
                    'id' => 'discount',
                    'price' => -((int) $transaction->discount_amount),
                    'quantity' => 1,
                    'name' => 'Diskon - ' . ($transaction->promo_code ?? 'Manual'),
                ];
            }

            $payload = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $items,
                'callbacks' => [
                    'finish' => route('payment.finish'),
                    'error' => route('payment.error'),
                    'pending' => route('payment.pending'),
                ],
            ];

            // Generate Snap token
            $snapToken = Snap::getSnapToken($payload);

            $this->log('Midtrans transaction created', [
                'transaction_number' => $transaction->transaction_number,
                'snap_token' => substr($snapToken, 0, 20) . '...',
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken,
            ];
        } catch (Exception $e) {
            $this->log('Midtrans error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment URL untuk redirect
     */
    public function getPaymentUrl(Transaction $transaction): string
    {
        try {
            $result = $this->createTransaction($transaction);
            
            if ($result['success']) {
                // Update transaction dengan snap token
                $transaction->update([
                    'payment_gateway' => 'midtrans',
                    'payment_url' => $result['redirect_url'],
                    'gateway_response' => json_encode([
                        'snap_token' => $result['snap_token'],
                    ]),
                ]);

                return $result['redirect_url'];
            }

            throw new Exception($result['error'] ?? 'Failed to generate payment URL');
        } catch (Exception $e) {
            $this->log('Get payment URL failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process Midtrans callback
     */
    public function processCallback(array $data): bool
    {
        try {
            // Verify signature
            $orderId = $data['order_id'] ?? null;
            $statusCode = $data['status_code'] ?? null;
            $transactionStatus = $data['transaction_status'] ?? null;

            if (!$orderId) {
                throw new Exception('Order ID not found in callback');
            }

            // Find transaction
            $transaction = Transaction::where('transaction_number', $orderId)->first();
            if (!$transaction) {
                throw new Exception("Transaction {$orderId} not found");
            }

            // Update gateway status
            $transaction->gateway_status = $transactionStatus;
            $transaction->gateway_response = json_encode($data);

            // Handle different statuses
            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    // Payment successful
                    $transaction->status = 'paid';
                    $transaction->amount_paid = $transaction->total;
                    $transaction->payment_date = now();
                    $transaction->completed_date = now();
                    
                    $transaction->recordPayment(
                        (float) ($data['gross_amount'] ?? 0),
                        'credit_card',
                        $data['reference_id'] ?? null,
                        'Payment via Midtrans - ' . $transactionStatus,
                        $data
                    );

                    $this->log('Payment settled via Midtrans', [
                        'transaction_number' => $transaction->transaction_number,
                        'amount' => $data['gross_amount'] ?? 0,
                    ]);
                    break;

                case 'pending':
                    $transaction->status = 'awaiting_payment';
                    $this->log('Payment pending', [
                        'transaction_number' => $transaction->transaction_number,
                    ]);
                    break;

                case 'cancel':
                case 'deny':
                    $transaction->status = 'failed';
                    $this->log('Payment cancelled', [
                        'transaction_number' => $transaction->transaction_number,
                    ]);
                    break;

                case 'expire':
                    $transaction->status = 'failed';
                    $transaction->admin_notes = 'Payment expired';
                    $this->log('Payment expired', [
                        'transaction_number' => $transaction->transaction_number,
                    ]);
                    break;
            }

            $transaction->save();
            return true;
        } catch (Exception $e) {
            $this->log('Callback processing error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return false;
        }
    }

    /**
     * Check transaction status di Midtrans
     */
    public function checkTransactionStatus(string $gatewayTransactionId): array
    {
        try {
            // Get status from Midtrans API
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get('https://api.sandbox.midtrans.com/v2/' . $gatewayTransactionId . '/status');

            if (!$response->successful()) {
                throw new Exception('Failed to check transaction status');
            }

            return $response->json();
        } catch (Exception $e) {
            $this->log('Check status error', [
                'error' => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Refund transaction
     */
    public function refundTransaction(Transaction $transaction, float $amount): bool
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->post('https://api.sandbox.midtrans.com/v2/' . $transaction->gateway_transaction_id . '/refund', [
                    'refund_key' => 'refund-' . $transaction->id . '-' . now()->timestamp,
                    'amount' => (int) $amount,
                ]);

            if ($response->successful()) {
                $this->log('Refund processed', [
                    'transaction_number' => $transaction->transaction_number,
                    'amount' => $amount,
                ]);
                return true;
            }

            throw new Exception('Refund failed: ' . $response->body());
        } catch (Exception $e) {
            $this->log('Refund error', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

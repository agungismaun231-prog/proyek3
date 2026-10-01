<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\Payment\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * TransactionController - Mengelola Transaksi Pembayaran
 * 
 * Routes:
 * GET  /transactions                  - List transaksi user
 * GET  /transactions/{id}             - Detail transaksi
 * POST /transactions/{id}/pay         - Initiate payment
 * GET  /transactions/{id}/payment-status - Check payment status (AJAX)
 * POST /transactions/{id}/cancel      - Cancel transaksi
 */
class TransactionController extends Controller
{
    protected MidtransPaymentService $paymentService;

    public function __construct(MidtransPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('auth');
    }

    /**
     * List all transactions for user
     */
    public function index()
    {
        $transactions = Transaction::byUser(Auth::id())
            ->with('items.service')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ecommerce.transactions.index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show transaction detail
     */
    public function show(Transaction $transaction)
    {
        $this->authorizeUser($transaction);

        $transaction->load(['items.service', 'paymentHistory', 'user']);

        return view('ecommerce.transactions.show', [
            'transaction' => $transaction,
            'items' => $transaction->items,
            'paymentHistory' => $transaction->paymentHistory()->latest()->get(),
        ]);
    }

    /**
     * Initiate payment dengan Midtrans
     */
    public function pay(Request $request, Transaction $transaction)
    {
        $this->authorizeUser($transaction);

        // Check if already paid
        if ($transaction->isPaidFull()) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi sudah dibayar penuh',
            ], 422);
        }

        try {
            $paymentUrl = $this->paymentService->getPaymentUrl($transaction);
            
            // Update status to awaiting payment
            $transaction->update([
                'status' => 'awaiting_payment',
                'payment_gateway' => 'midtrans',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Silakan lakukan pembayaran',
                'payment_url' => $paymentUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(Transaction $transaction)
    {
        $this->authorizeUser($transaction);

        if ($transaction->gateway_transaction_id) {
            $status = $this->paymentService->checkTransactionStatus(
                $transaction->gateway_transaction_id
            );

            return response()->json([
                'status' => $transaction->status,
                'gateway_status' => $transaction->gateway_status,
                'amount_paid' => $transaction->amount_paid,
                'remaining' => $transaction->getRemainingAmount(),
                'gateway_info' => $status,
            ]);
        }

        return response()->json([
            'status' => $transaction->status,
            'amount_paid' => $transaction->amount_paid,
            'remaining' => $transaction->getRemainingAmount(),
        ]);
    }

    /**
     * Cancel transaction
     */
    public function cancel(Request $request, Transaction $transaction)
    {
        $this->authorizeUser($transaction);

        if ($transaction->status === 'paid' || $transaction->status === 'completed') {
            return back()->with('error', 'Tidak bisa membatalkan transaksi yang sudah dibayar');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $transaction->cancel($request->reason ?? 'Dibatalkan oleh user');

        return back()->with('success', 'Transaksi berhasil dibatalkan');
    }

    /**
     * Authorize user owns this transaction
     */
    private function authorizeUser(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}

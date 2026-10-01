<?php

namespace App\Http\Controllers;

use App\Models\ShoppingCart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CheckoutController - Proses Checkout dan Pembuatan Transaksi
 * 
 * Routes:
 * GET  /checkout                      - Show checkout form
 * POST /checkout/process              - Process checkout
 * GET  /checkout/success/{id}         - Success page
 * GET  /checkout/failed/{id}          - Failed page
 */
class CheckoutController extends Controller
{
    /**
     * Show checkout form
     */
    public function show()
    {
        $user = Auth::user();
        $cart = ShoppingCart::byUser($user->id)->first();

        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('ecommerce.cart.index')->with('warning', 'Keranjang Anda kosong');
        }

        return view('ecommerce.checkout.index', [
            'cart' => $cart,
            'items' => $cart->items()->with('service')->get(),
        ]);
    }

    /**
     * Process checkout dan buat transaction
     */
    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,bank_transfer,e_wallet,cash',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $cart = ShoppingCart::byUser($user->id)->first();

        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('ecommerce.cart.index')->with('error', 'Keranjang kosong');
        }

        try {
            DB::beginTransaction();

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_number' => Transaction::generateTransactionNumber(),
                'subtotal' => $cart->subtotal,
                'tax' => $cart->tax,
                'discount_amount' => $cart->discount_amount,
                'promo_code' => $cart->discount_code,
                'total' => $cart->total,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'submitted_date' => now(),
                'metadata' => [
                    'notes' => $request->notes,
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                ],
            ]);

            // Create transaction items
            foreach ($cart->items as $cartItem) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'service_id' => $cartItem->service_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'subtotal' => $cartItem->subtotal,
                    'description' => $cartItem->service->description ?? null,
                ]);
            }

            // Mark cart as completed
            $cart->update(['status' => 'completed']);

            // Clear cart items (move to completed cart)
            // Don't delete, keep for audit trail
            // $cart->items()->delete();

            DB::commit();

            // Redirect ke halaman transaksi detail (bukan payment page)
            // User akan lihat E-Wallet icons di sana untuk pembayaran
            return redirect()->route('ecommerce.transactions.show', $transaction)->with(
                'success',
                'Checkout berhasil. Pilih metode pembayaran untuk melanjutkan.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat checkout: ' . $e->getMessage()]);
        }
    }

    /**
     * Show checkout success page
     */
    public function success(Transaction $transaction)
    {
        $this->authorizeUser($transaction);

        return view('ecommerce.checkout.success', [
            'transaction' => $transaction,
            'items' => $transaction->items()->with('service')->get(),
        ]);
    }

    /**
     * Show checkout failed page
     */
    public function failed(Transaction $transaction)
    {
        $this->authorizeUser($transaction);

        return view('ecommerce.checkout.failed', [
            'transaction' => $transaction,
        ]);
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

<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Service;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CartController - Mengelola Shopping Cart
 * 
 * Routes:
 * GET  /cart                          - View cart
 * POST /cart/add                      - Add item to cart
 * POST /cart/update/{item}            - Update item quantity
 * POST /cart/remove/{item}            - Remove item from cart
 * POST /cart/apply-promo              - Apply promo code
 * POST /cart/clear                    - Clear cart
 * GET  /cart/summary                  - Cart summary (AJAX)
 */
class CartController extends Controller
{
    /**
     * View shopping cart
     */
    public function index()
    {
        $user = Auth::user();
        $cart = ShoppingCart::byUser($user->id)->first();

        if (!$cart) {
            return view('ecommerce.cart.empty');
        }

        return view('ecommerce.cart.index', [
            'cart' => $cart,
            'items' => $cart->items()->with('service')->get(),
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $service = Service::findOrFail($request->service_id);

        // Get atau create cart
        $cart = ShoppingCart::byUser($user->id)->first();
        if (!$cart) {
            $cart = ShoppingCart::create([
                'user_id' => $user->id,
                'status' => 'active',
            ]);
        }

        // Add item
        $cart->addItem($service, $request->quantity, $request->notes);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke keranjang',
                'cart_count' => $cart->items()->count(),
                'cart_total' => $cart->total,
            ]);
        }

        return redirect()->back()->with('success', 'Item berhasil ditambahkan ke keranjang');
    }

    /**
     * Update item quantity
     */
    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $item->shoppingCart;
        $this->authorizeUser($cart);

        $cart->updateItemQuantity($item->id, $request->quantity);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Quantity berhasil diupdate',
                'item_subtotal' => $item->fresh()->subtotal,
                'cart_total' => $cart->fresh()->total,
            ]);
        }

        return redirect()->back()->with('success', 'Quantity berhasil diupdate');
    }

    /**
     * Remove item from cart
     */
    public function remove(CartItem $item)
    {
        $cart = $item->shoppingCart;
        $this->authorizeUser($cart);

        $cart->removeItem($item->id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus',
                'cart_total' => $cart->fresh()->total,
            ]);
        }

        return redirect()->back()->with('success', 'Item berhasil dihapus');
    }

    /**
     * Apply promo code
     */
    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string|max:50',
        ]);

        $user = Auth::user();
        $cart = ShoppingCart::byUser($user->id)->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang tidak ditemukan',
            ], 404);
        }

        $applied = $cart->applyPromoCode($request->promo_code);

        if ($applied) {
            return response()->json([
                'success' => true,
                'message' => 'Kode promo berhasil diterapkan',
                'discount_amount' => $cart->discount_amount,
                'cart_total' => $cart->total,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode promo tidak valid atau sudah kadaluarsa',
        ], 422);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $user = Auth::user();
        $cart = ShoppingCart::byUser($user->id)->first();

        if ($cart) {
            $cart->clear();
        }

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil dikosongkan');
    }

    /**
     * Get cart summary (AJAX)
     */
    public function summary()
    {
        $user = Auth::user();
        $cart = ShoppingCart::byUser($user->id)->first();

        if (!$cart) {
            return response()->json([
                'count' => 0,
                'total' => 0,
            ]);
        }

        return response()->json([
            'count' => $cart->items()->count(),
            'total' => $cart->total,
            'subtotal' => $cart->subtotal,
            'tax' => $cart->tax,
            'discount' => $cart->discount_amount,
        ]);
    }

    /**
     * Authorize user owns this cart
     */
    private function authorizeUser(ShoppingCart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ShoppingCart Model - Keranjang Belanja E-commerce
 * 
 * Menyimpan data keranjang belanja untuk pelanggan yang masih dalam proses shopping.
 * Setiap user hanya bisa memiliki 1 active shopping cart pada saat yang sama.
 * 
 * @property int $id
 * @property int $user_id - Foreign key ke users
 * @property decimal $subtotal - Total sebelum pajak
 * @property decimal $tax - Jumlah pajak
 * @property decimal $discount_amount - Besaran diskon
 * @property string|null $discount_code - Kode promo yang dipakai
 * @property decimal $total - Total akhir
 * @property string $status - active, completed, abandoned
 * @property datetime $created_at
 * @property datetime $updated_at
 */
class ShoppingCart extends Model
{
    /**
     * Tabel yang diassociate dengan model
     */
    protected $table = 'shopping_carts';

    /**
     * Atribut yang dapat di-assign massal
     */
    protected $fillable = [
        'user_id',
        'subtotal',
        'tax',
        'discount_amount',
        'discount_code',
        'total',
        'status',
    ];

    /**
     * Casting untuk atribut
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: Pemilik keranjang
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Item dalam keranjang
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Menambahkan item ke keranjang
     */
    public function addItem(Service $service, int $quantity = 1, ?string $notes = null): CartItem
    {
        // Cek apakah item sudah ada
        $existingItem = $this->items()->where('service_id', $service->id)->first();
        
        if ($existingItem) {
            // Update quantity
            $existingItem->quantity += $quantity;
            $existingItem->subtotal = $existingItem->quantity * $existingItem->unit_price;
            $existingItem->save();
            $item = $existingItem;
        } else {
            // Tambah item baru
            $item = $this->items()->create([
                'service_id' => $service->id,
                'quantity' => $quantity,
                'unit_price' => $service->price,
                'subtotal' => $quantity * $service->price,
                'notes' => $notes,
            ]);
        }

        // Update cart totals
        $this->recalculateTotal();
        
        return $item;
    }

    /**
     * Menghapus item dari keranjang
     */
    public function removeItem(int $cartItemId): void
    {
        CartItem::find($cartItemId)?->delete();
        $this->recalculateTotal();
    }

    /**
     * Update quantity item
     */
    public function updateItemQuantity(int $cartItemId, int $quantity): void
    {
        $item = CartItem::find($cartItemId);
        if ($item && $item->shopping_cart_id === $this->id) {
            $item->quantity = $quantity;
            $item->subtotal = $quantity * $item->unit_price;
            $item->save();
            $this->recalculateTotal();
        }
    }

    /**
     * Menerapkan kode promo
     */
    public function applyPromoCode(string $code): bool
    {
        $promo = PromoCode::where('code', $code)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->first();

        if (!$promo) {
            return false;
        }

        // Cek minimum purchase
        if ($this->subtotal < $promo->min_purchase) {
            return false;
        }

        // Cek usage limit
        if ($promo->usage_limit && $promo->times_used >= $promo->usage_limit) {
            return false;
        }

        $this->discount_code = $code;
        $this->recalculateTotal();
        $this->save();

        return true;
    }

    /**
     * Menghitung ulang total keranjang
     */
    public function recalculateTotal(): void
    {
        // Hitung subtotal dari semua items
        $this->subtotal = $this->items()->sum('subtotal');

        // Hitung pajak (misal 10%)
        $this->tax = $this->subtotal * 0.10;

        // Hitung diskon jika ada kode promo
        $this->discount_amount = 0;
        if ($this->discount_code) {
            $promo = PromoCode::where('code', $this->discount_code)->first();
            if ($promo) {
                if ($promo->discount_type === 'percentage') {
                    $discount = ($this->subtotal * $promo->discount_value) / 100;
                    if ($promo->max_discount) {
                        $discount = min($discount, $promo->max_discount);
                    }
                    $this->discount_amount = $discount;
                } else {
                    $this->discount_amount = min($promo->discount_value, $this->subtotal);
                }
            }
        }

        // Total akhir
        $this->total = $this->subtotal + $this->tax - $this->discount_amount;
        $this->save();
    }

    /**
     * Mengosongkan keranjang
     */
    public function clear(): void
    {
        $this->items()->delete();
        $this->update([
            'subtotal' => 0,
            'tax' => 0,
            'discount_amount' => 0,
            'discount_code' => null,
            'total' => 0,
        ]);
    }

    /**
     * Scope: Keranjang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Keranjang user tertentu
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}

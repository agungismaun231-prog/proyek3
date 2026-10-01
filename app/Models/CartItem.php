<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CartItem Model - Item dalam Keranjang Belanja
 * 
 * Merepresentasikan service individual yang ada dalam shopping cart.
 * 
 * @property int $id
 * @property int $shopping_cart_id - Foreign key
 * @property int $service_id - Foreign key
 * @property int $quantity - Jumlah service
 * @property decimal $unit_price - Harga per unit
 * @property decimal $subtotal - Subtotal
 * @property string|null $notes - Catatan khusus
 * @property datetime $created_at
 * @property datetime $updated_at
 */
class CartItem extends Model
{
    /**
     * Tabel
     */
    protected $table = 'cart_items';

    /**
     * Atribut yang dapat di-assign massal
     */
    protected $fillable = [
        'shopping_cart_id',
        'service_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    /**
     * Casting
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: Keranjang induk
     */
    public function shoppingCart(): BelongsTo
    {
        return $this->belongsTo(ShoppingCart::class);
    }

    /**
     * Relasi: Service yang diambil
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}

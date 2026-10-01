<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TransactionItem Model - Item dalam Transaksi
 * 
 * Merepresentasikan service individual yang dibeli dalam satu transaksi.
 * 
 * @property int $id
 * @property int $transaction_id - Foreign key
 * @property int $service_id - Foreign key
 * @property int $quantity - Jumlah
 * @property decimal $unit_price - Harga satuan
 * @property decimal $subtotal - Subtotal
 * @property string|null $description - Deskripsi detail
 * @property datetime $created_at
 * @property datetime $updated_at
 */
class TransactionItem extends Model
{
    /**
     * Tabel
     */
    protected $table = 'transaction_items';

    /**
     * Atribut yang dapat di-assign massal
     */
    protected $fillable = [
        'transaction_id',
        'service_id',
        'quantity',
        'unit_price',
        'subtotal',
        'description',
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
     * Relasi: Transaksi induk
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relasi: Service yang dibeli
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}

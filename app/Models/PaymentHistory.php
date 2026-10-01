<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PaymentHistory Model - Riwayat Pembayaran Detail
 * 
 * Merekam setiap event pembayaran dalam satu transaksi.
 * Berguna untuk tracking partial payment dan payment attempts.
 * 
 * @property int $id
 * @property int $transaction_id - Foreign key
 * @property decimal $amount - Jumlah pembayaran
 * @property string $payment_method - Metode pembayaran
 * @property string|null $payment_reference - Referensi (bank, gateway)
 * @property string $status - pending, success, failed, cancelled
 * @property string|null $remarks - Keterangan
 * @property json|null $gateway_data - Response dari gateway
 * @property datetime $created_at
 * @property datetime $updated_at
 */
class PaymentHistory extends Model
{
    /**
     * Tabel
     */
    protected $table = 'payment_history';

    /**
     * Atribut yang dapat di-assign massal
     */
    protected $fillable = [
        'transaction_id',
        'amount',
        'payment_method',
        'payment_reference',
        'status',
        'remarks',
        'gateway_data',
    ];

    /**
     * Casting
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_data' => 'json',
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
     * Scope: Pembayaran sukses
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope: By method
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }
}

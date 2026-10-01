<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Transaction Model - Transaksi Pembayaran E-commerce
 * 
 * Merepresentasikan transaksi pembayaran lengkap dengan integrasi payment gateway.
 * Setiap transaksi bisa memiliki multiple payments (untuk partial payment).
 * 
 * @property int $id
 * @property int $user_id - Foreign key
 * @property string $transaction_number - Nomor unik transaksi
 * @property decimal $subtotal - Total sebelum pajak/diskon
 * @property decimal $tax - Pajak
 * @property decimal $discount_amount - Besaran diskon
 * @property string|null $promo_code - Kode promo
 * @property decimal $total - Total akhir
 * @property string $payment_method - credit_card, bank_transfer, dll
 * @property string|null $payment_gateway - Midtrans, Xendit, dll
 * @property string|null $gateway_transaction_id - ID dari gateway
 * @property decimal $amount_paid - Jumlah sudah dibayar
 * @property string|null $payment_reference - Referensi pembayaran
 * @property string|null $payment_proof - Path bukti
 * @property string $status - pending, processing, paid, dll
 * @property string|null $gateway_status - Status dari gateway
 * @property text|null $gateway_response - Response gateway
 * @property datetime|null $submitted_date - Kapan dibuat
 * @property datetime|null $payment_date - Kapan dibayar
 * @property datetime|null $completed_date - Kapan selesai
 * @property int|null $approved_by - User ID admin yang approve
 * @property datetime|null $approved_date - Kapan approval
 * @property text|null $admin_notes - Catatan admin
 * @property int|null $invoice_id - Link ke invoice
 * @property json|null $metadata - Metadata custom
 * @property datetime $created_at
 * @property datetime $updated_at
 */
class Transaction extends Model
{
    /**
     * Tabel
     */
    protected $table = 'transactions';

    /**
     * Atribut yang dapat di-assign massal
     */
    protected $fillable = [
        'user_id',
        'transaction_number',
        'subtotal',
        'tax',
        'discount_amount',
        'promo_code',
        'total',
        'payment_method',
        'payment_gateway',
        'gateway_transaction_id',
        'amount_paid',
        'payment_reference',
        'payment_proof',
        'status',
        'gateway_status',
        'gateway_response',
        'submitted_date',
        'payment_date',
        'completed_date',
        'approved_by',
        'approved_date',
        'admin_notes',
        'invoice_id',
        'metadata',
    ];

    /**
     * Casting
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'submitted_date' => 'datetime',
        'payment_date' => 'datetime',
        'completed_date' => 'datetime',
        'approved_date' => 'datetime',
        'gateway_response' => 'json',
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: User yang melakukan transaksi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Admin yang approve
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi: Item dalam transaksi
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Relasi: Riwayat pembayaran
     */
    public function paymentHistory(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    /**
     * Relasi: Invoice terkait
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Generate nomor transaksi unik
     */
    public static function generateTransactionNumber(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', now())->count() + 1;
        return 'TRX-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cek apakah transaksi sudah dibayar penuh
     */
    public function isPaidFull(): bool
    {
        return $this->amount_paid >= $this->total;
    }

    /**
     * Cek apakah transaksi partial paid
     */
    public function isPartiallyPaid(): bool
    {
        return $this->amount_paid > 0 && $this->amount_paid < $this->total;
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmount(): float
    {
        return max(0, $this->total - $this->amount_paid);
    }

    /**
     * Record pembayaran
     */
    public function recordPayment(
        float $amount,
        string $method,
        ?string $reference = null,
        ?string $remarks = null,
        ?array $gatewayData = null
    ): PaymentHistory {
        $payment = $this->paymentHistory()->create([
            'amount' => $amount,
            'payment_method' => $method,
            'payment_reference' => $reference,
            'remarks' => $remarks,
            'gateway_data' => $gatewayData,
            'status' => 'success',
        ]);

        // Update transaction amount paid
        $this->amount_paid += $amount;
        
        // Update status
        if ($this->isPaidFull()) {
            $this->status = 'paid';
            $this->payment_date = now();
            $this->completed_date = now();
        } elseif ($this->isPartiallyPaid()) {
            $this->status = 'partially_paid';
        }

        $this->save();

        return $payment;
    }

    /**
     * Approve transaksi (admin action)
     */
    public function approve(int $adminId, ?string $notes = null): void
    {
        $this->status = 'completed';
        $this->approved_by = $adminId;
        $this->approved_date = now();
        $this->completed_date = now();
        $this->admin_notes = $notes;
        $this->save();
    }

    /**
     * Reject transaksi
     */
    public function reject(int $adminId, string $reason): void
    {
        $this->status = 'failed';
        $this->approved_by = $adminId;
        $this->approved_date = now();
        $this->admin_notes = 'Rejected: ' . $reason;
        $this->save();
    }

    /**
     * Cancel transaksi
     */
    public function cancel(string $reason): void
    {
        $this->status = 'cancelled';
        $this->admin_notes = 'Cancelled: ' . $reason;
        $this->save();
    }

    /**
     * Scope: Pending payment
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'processing', 'awaiting_payment']);
    }

    /**
     * Scope: By user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: By date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope: Paid transactions
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}

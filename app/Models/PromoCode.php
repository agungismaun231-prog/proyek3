<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PromoCode Model - Kode Promosi dan Diskon
 * 
 * Menyimpan data kode promo yang bisa digunakan pelanggan
 * untuk mendapatkan diskon pada pembelian.
 * 
 * @property int $id
 * @property string $code - Kode unik (misal: SUMMER2025)
 * @property string|null $description - Deskripsi promo
 * @property string $discount_type - percentage atau fixed_amount
 * @property decimal $discount_value - Nilai diskon
 * @property decimal|null $max_discount - Max diskon (utk percentage)
 * @property decimal $min_purchase - Minimal pembelian
 * @property int|null $usage_limit - Total limit penggunaan
 * @property int $usage_per_customer - Limit per customer
 * @property int $times_used - Sudah dipakai
 * @property datetime $valid_from - Mulai berlaku
 * @property datetime $valid_until - Berakhir berlaku
 * @property bool $is_active - Aktif atau tidak
 * @property datetime $created_at
 * @property datetime $updated_at
 */
class PromoCode extends Model
{
    /**
     * Tabel
     */
    protected $table = 'promo_codes';

    /**
     * Atribut yang dapat di-assign massal
     */
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_purchase',
        'usage_limit',
        'usage_per_customer',
        'times_used',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    /**
     * Casting
     */
    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'times_used' => 'integer',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: Transaksi yang menggunakan promo ini
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'promo_code', 'code');
    }

    /**
     * Cek apakah promo valid untuk digunakan
     */
    public function isValid(): bool
    {
        return $this->is_active 
            && now()->between($this->valid_from, $this->valid_until)
            && (!$this->usage_limit || $this->times_used < $this->usage_limit);
    }

    /**
     * Cek apakah user bisa menggunakan promo ini
     */
    public function canUseByUser(int $userId): bool
    {
        $usageCount = Transaction::where('user_id', $userId)
            ->where('promo_code', $this->code)
            ->where('status', '!=', 'cancelled')
            ->count();

        return $usageCount < $this->usage_per_customer;
    }

    /**
     * Hitung discount amount berdasarkan total
     */
    public function calculateDiscount(float $total): float
    {
        if ($total < $this->min_purchase) {
            return 0;
        }

        $discount = 0;
        
        if ($this->discount_type === 'percentage') {
            $discount = ($total * $this->discount_value) / 100;
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
        } else {
            $discount = min($this->discount_value, $total);
        }

        return $discount;
    }

    /**
     * Increment usage counter
     */
    public function incrementUsage(): void
    {
        $this->increment('times_used');
    }

    /**
     * Scope: Promo aktif dan valid
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());
    }

    /**
     * Scope: Cari berdasarkan kode
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }
}

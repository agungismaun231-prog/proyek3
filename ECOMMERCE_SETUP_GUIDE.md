# SETUP GUIDE - E-COMMERCE PAYMENT SYSTEM

## 📋 Checklist Implementasi

Panduan lengkap untuk mengimplementasikan sistem pembayaran e-commerce di aplikasi Anda.

---

## 1. DATABASE SETUP

### Step 1: Run Migrations

```bash
# Di terminal, jalankan semua migrations termasuk yang baru
php artisan migrate
```

Migrations yang akan dibuat:
- `2025_05_14_000001_create_shopping_carts_table`
- `2025_05_14_000002_create_cart_items_table`
- `2025_05_14_000003_create_promo_codes_table`
- `2025_05_14_000004_create_transactions_table`
- `2025_05_14_000005_create_transaction_items_table`
- `2025_05_14_000006_create_payment_history_table`

### Step 2: Verify Tables

Pastikan semua tabel sudah terbuat:
```sql
SHOW TABLES LIKE '%cart%';
SHOW TABLES LIKE '%promo%';
SHOW TABLES LIKE '%transaction%';
```

---

## 2. KONFIGURASI PAYMENT GATEWAY (Midtrans)

### Step 1: Daftar Midtrans Account

1. Pergi ke https://midtrans.com
2. Daftar akun merchant
3. Verifikasi email Anda
4. Login ke Midtrans Dashboard
5. Dapatkan credentials:
   - **Merchant ID** (contoh: G123456)
   - **Client Key** (untuk frontend)
   - **Server Key** (untuk backend - JANGAN dibagikan)

### Step 2: Update .env File

```env
# .env configuration
MIDTRANS_MERCHANT_ID=G123456
MIDTRANS_CLIENT_KEY=Mid-client-xxxxx
MIDTRANS_SERVER_KEY=Mid-server-xxxxx
MIDTRANS_ENVIRONMENT=sandbox

# Timezone
APP_TIMEZONE=Asia/Jakarta

# Tax rate
PAYMENT_TAX_RATE=10
```

### Step 3: Verify Configuration

```bash
# Test connection
php artisan tinker
> config('services.midtrans.merchant_id')
> config('services.midtrans.environment')
```

---

## 3. COMPOSER DEPENDENCIES

### Midtrans PHP Library

```bash
# Install Midtrans SDK
composer require midtrans/midtrans-php

# Verify installation
php artisan tinker
> new Midtrans\Snap()
```

---

## 4. MODEL RELATIONSHIPS

Verifikasi bahwa User model memiliki relationship dengan shopping cart dan transactions:

```php
// app/Models/User.php
public function shoppingCart() {
    return $this->hasOne(ShoppingCart::class);
}

public function transactions() {
    return $this->hasMany(Transaction::class);
}

public function paymentHistory() {
    return $this->hasManyThrough(
        PaymentHistory::class,
        Transaction::class
    );
}
```

---

## 5. SERVICE MODEL

Pastikan Service model memiliki atribut `price`:

```php
// app/Models/Service.php
class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',      // REQUIRED untuk e-commerce
        // ... other fields
    ];
}
```

---

## 6. TESTING ROUTES

### Test Cart Routes

```bash
# Add item to cart
curl -X POST http://localhost:8000/ecommerce/cart/add \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"service_id": 1, "quantity": 2}'

# View cart
curl http://localhost:8000/ecommerce/cart

# Cart summary
curl http://localhost:8000/ecommerce/cart/summary
```

### Test Promo Code

```bash
# First, create a promo in database
php artisan tinker
> $promo = PromoCode::create([
    'code' => 'TEST2025',
    'discount_type' => 'percentage',
    'discount_value' => 10,
    'min_purchase' => 100000,
    'valid_from' => now(),
    'valid_until' => now()->addMonth(),
    'is_active' => true
  ]);

# Apply promo code
curl -X POST http://localhost:8000/ecommerce/cart/apply-promo \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"promo_code": "TEST2025"}'
```

### Test Checkout

```bash
# Show checkout form
curl http://localhost:8000/ecommerce/checkout

# Process checkout
curl -X POST http://localhost:8000/ecommerce/checkout/process \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"payment_method": "credit_card", "notes": "Optional notes"}'
```

---

## 7. WEBHOOK CONFIGURATION

### Configure Webhook URL di Midtrans Dashboard

1. Login ke Midtrans Dashboard
2. Go to **Settings → Webhook**
3. Tambahkan endpoint webhook:
   ```
   URL: https://yourdomain.com/payment/callback
   Events: order.created, payment.success, payment.failed, payment.pending
   ```

4. Setup notification settings:
   ```
   URL: https://yourdomain.com/payment/callback
   HTTP Method: POST
   ```

### Test Webhook (Sandbox Mode)

Di Midtrans Dashboard:
1. Go ke **Monitoring → Webhook**
2. Klik **Send Test Webhook**
3. Check application logs untuk memverifikasi callback diterima

---

## 8. SEEDING DATA (OPTIONAL)

### Create Test Data

```php
// database/seeders/PromoCodeSeeder.php
<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        PromoCode::create([
            'code' => 'WELCOME2025',
            'description' => 'Welcome discount untuk customer baru',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'max_discount' => 200000,
            'min_purchase' => 100000,
            'usage_limit' => 100,
            'usage_per_customer' => 1,
            'valid_from' => now(),
            'valid_until' => now()->addYear(),
            'is_active' => true,
        ]);

        PromoCode::create([
            'code' => 'FLAT50K',
            'description' => 'Diskon flat Rp 50.000',
            'discount_type' => 'fixed_amount',
            'discount_value' => 50000,
            'min_purchase' => 200000,
            'usage_limit' => 50,
            'usage_per_customer' => 1,
            'valid_from' => now(),
            'valid_until' => now()->addMonth(),
            'is_active' => true,
        ]);
    }
}
```

Run seeder:
```bash
php artisan db:seed --class=PromoCodeSeeder
```

---

## 9. ADMIN DASHBOARD (FUTURE)

### Features to Add

- [ ] Admin panel untuk manage promo codes
- [ ] Admin panel untuk approve/reject manual payments
- [ ] Dashboard untuk view payment statistics
- [ ] Export transaction reports (CSV, PDF)
- [ ] Refund management interface
- [ ] Payment reconciliation tools

### Routes untuk Admin

```php
// routes/web.php - tambahkan ke admin middleware group
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Promo management
    Route::resource('promos', PromoCodeAdminController::class);
    
    // Payment management
    Route::get('/transactions', [TransactionAdminController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [TransactionAdminController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{transaction}/approve', [TransactionAdminController::class, 'approve'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [TransactionAdminController::class, 'reject'])->name('transactions.reject');
    
    // Reports
    Route::get('/reports/payments', [ReportController::class, 'payments'])->name('reports.payments');
    Route::get('/reports/promos', [ReportController::class, 'promos'])->name('reports.promos');
});
```

---

## 10. LOGGING & MONITORING

### Setup Payment Logs

```php
// config/logging.php - update atau tambahkan
'channels' => [
    'payment' => [
        'driver' => 'single',
        'path' => storage_path('logs/payment.log'),
        'level' => 'debug',
    ],
],
```

### Monitor Logs

```bash
# Real-time tail logs
tail -f storage/logs/payment.log

# View specific transaction logs
grep "TRX-20250514-0001" storage/logs/payment.log
```

---

## 11. SECURITY CHECKLIST

- [ ] Use HTTPS in production
- [ ] Never expose Server Key di frontend atau public
- [ ] Validate semua input dari user
- [ ] Use CSRF tokens di semua forms
- [ ] Implement rate limiting on payment endpoints
- [ ] Verify webhook signature dari Midtrans
- [ ] Keep payment logs secure
- [ ] Use environment variables untuk sensitive data
- [ ] Test SQL injection pada payment forms
- [ ] Implement proper error handling (jangan expose detailed errors)

---

## 12. TESTING SCENARIOS

### Scenario 1: Berhasil Pembayaran

1. Add item ke cart
2. View cart
3. Proceed to checkout
4. Select payment method: Credit Card
5. Process checkout
6. Di Midtrans Snap page, gunakan test card: `4811 1111 1111 1114`
7. Verify payment success page muncul

### Scenario 2: Pembayaran Gagal

1. Repeat steps 1-5
2. Use test card yang akan fail: `4111 1111 1111 1113`
3. Verify error page muncul

### Scenario 3: Partial Payment

1. Create transaction
2. Bayar sebagian (manual payment)
3. Verify status menjadi `partially_paid`
4. Bayar sisa
5. Verify status menjadi `paid`

### Scenario 4: Promo Code

1. Add item ke cart
2. Apply valid promo code
3. Verify discount calculation
4. Complete checkout
5. Verify discount tercatat di transaction

---

## 13. TROUBLESHOOTING

### Problem: "Midtrans server key not configured"

**Solution:**
```bash
# Check .env
cat .env | grep MIDTRANS

# Clear config cache
php artisan config:clear
php artisan config:cache
```

### Problem: "Payment callback not received"

**Solution:**
1. Verify webhook URL di Midtrans Dashboard
2. Check firewall/security groups allow POST requests
3. Verify domain is publicly accessible
4. Check logs: `tail -f storage/logs/payment.log`

### Problem: "Transaction not found after payment"

**Solution:**
1. Verify transaction number format matches
2. Check database untuk transaction record
3. Verify user ID matches

### Problem: "Cart calculation wrong"

**Solution:**
```php
// Manual recalculation
$cart = ShoppingCart::find($cartId);
$cart->recalculateTotal();
```

---

## 14. PERFORMANCE OPTIMIZATION

### Database Indexes

Sudah ditambahkan di migrations untuk:
- User ID pada shopping_carts dan transactions
- Status fields
- Dates (created_at, payment_date)
- Gateway transaction ID

### Cache Configuration

```php
// Cache cart summary sebentar-sebentar
Cache::put('cart_' . $userId, $cart->summary(), now()->addHour());
```

### Query Optimization

```php
// Gunakan eager loading
Transaction::with('items.service', 'paymentHistory')->get();

// Gunakan pagination
Transaction::paginate(20);
```

---

## 15. DEPLOYMENT CHECKLIST

- [ ] Update .env dengan production credentials
- [ ] Change MIDTRANS_ENVIRONMENT ke 'production'
- [ ] Run migrations di production database
- [ ] Set APP_DEBUG=false
- [ ] Configure proper logging level
- [ ] Setup webhook URL ke production domain
- [ ] Test payment flow end-to-end
- [ ] Backup database
- [ ] Monitor error logs
- [ ] Setup alerts untuk failed payments

---

## NEXT STEPS

1. **Implement Admin Dashboard**
   - Promo code management
   - Transaction management
   - Payment reports

2. **Add Email Notifications**
   - Order confirmation
   - Payment receipt
   - Shipping notification

3. **Advanced Features**
   - Installment payment options
   - Subscription/recurring payment
   - Payment analytics dashboard

4. **Integration**
   - CRM integration
   - Accounting software
   - Shipping integration

---

## SUPPORT & RESOURCES

- **Midtrans Docs**: https://docs.midtrans.com/
- **Midtrans Dashboard**: https://app.midtrans.com/
- **Laravel Docs**: https://laravel.com/docs/
- **ECOMMERCE_PAYMENT_DOCUMENTATION.md**: Full API documentation

---

## NOTES

- Sistem mendukung partial payment (bisa bayar bertahap)
- Promo code bisa dihapus user sebelum checkout
- Tax rate fixed 10% (bisa dikonfigurasi)
- Payment gateway bisa ditambah (Xendit, GoPay, dll)

---

**Created:** 2025-05-14  
**Last Updated:** 2025-05-14  
**Version:** 1.0

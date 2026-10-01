# Panduan Simulasi Transaksi Pembayaran

**Tanggal:** May 20, 2026

---

## 1. SIMULASI PEMBAYARAN VIA MIDTRANS SANDBOX

### Langkah 1: Buka Halaman Checkout
1. Login sebagai customer
2. Add service ke cart: http://localhost/ecommerce/cart
3. Click "Checkout" → "Lanjut ke Pembayaran"

### Langkah 2: Lihat Transaction Detail
- Akan redirect ke halaman transaction detail
- Lihat **E-Wallet icon** (💳)
- Click icon → redirect ke Midtrans Snap

### Langkah 3: Proses Pembayaran di Midtrans Sandbox

#### **Test Credit Card**
```
Card Number: 4111 1111 1111 1111
Exp Month: 12
Exp Year: 2025
CVV: 123
```

#### **Test E-Wallet (GCash, OVO, Dana)**
- Pilih **E-Wallet** di Midtrans
- Pilih **GCash** / **OVO** / **Dana**
- Click **Continue**
- Akan auto-complete dalam sandbox

#### **Test Bank Transfer**
- Pilih **Bank Transfer**
- Permata Bank akan tampil
- Akan auto-complete dalam sandbox

### Langkah 4: Verifikasi Payment Success
Setelah payment berhasil:
1. ✅ Akan redirect kembali ke transaction detail
2. ✅ Lihat **Green success card** "Pembayaran Berhasil!"
3. ✅ Status badge berubah menjadi **"Paid"**
4. ✅ Check email untuk confirmation (jika SMTP configured)

---

## 2. SIMULASI DENGAN ARTISAN TINKER (Cepat)

Untuk testing cepat tanpa harus buka browser:

### Langkah 1: Buka Tinker
```bash
php artisan tinker
```

### Langkah 2: Create Test Customer
```php
$user = User::create([
    'name' => 'Test Customer',
    'email' => 'customer@test.com',
    'password' => bcrypt('password'),
    'role' => 'customer'
]);

$customer = Customer::create([
    'user_id' => $user->id,
    'phone' => '081234567890'
]);
```

### Langkah 3: Create Test Transaction
```php
$transaction = Transaction::create([
    'user_id' => $user->id,
    'transaction_number' => 'TRX-' . date('Ymd') . '-0001',
    'subtotal' => 500000,
    'tax' => 50000,
    'discount_amount' => 0,
    'total' => 550000,
    'payment_method' => 'e_wallet',
    'status' => 'pending',
    'submitted_date' => now(),
]);
```

### Langkah 4: Add Transaction Items
```php
$service = Service::first(); // Get any service

TransactionItem::create([
    'transaction_id' => $transaction->id,
    'service_id' => $service->id,
    'quantity' => 1,
    'unit_price' => 500000,
    'subtotal' => 500000,
    'description' => 'Test Service'
]);
```

### Langkah 5: Simulasi Payment Success
```php
// Simulate payment success - ubah status langsung
$transaction->update([
    'status' => 'paid',
    'payment_date' => now(),
]);

// Atau jika ingin test notification saja:
// $transaction->user->notify(new \App\Notifications\PaymentSuccessNotification($transaction));
```

### Langkah 6: Verify
```php
$transaction->refresh();
echo "Status: " . $transaction->status;
echo "Paid: " . $transaction->payment_date;

// Check notifications
$transaction->user->notifications;
```

---

## 3. SIMULASI WEBHOOK DARI MIDTRANS

Gunakan untuk test payment callback handler tanpa Midtrans:

### Langkah 1: Setup Test Route (temporary)
Edit `routes/web.php`:
```php
Route::post('/test-payment-webhook', function (\Illuminate\Http\Request $request) {
    $paymentData = [
        'order_id' => 'TRX-20260520-0001',
        'transaction_id' => 'transaction-id-123',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'signature_key' => 'test-signature',
        'gross_amount' => '550000',
        'payment_type' => 'credit_card'
    ];
    
    // Call webhook handler
    $controller = new \App\Http\Controllers\PaymentWebhookController();
    return $controller->midtransNotification(new \Illuminate\Http\Request($paymentData));
});
```

### Langkah 2: POST ke Route
```bash
curl -X POST http://localhost/test-payment-webhook \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": "TRX-20260520-0001",
    "transaction_id": "123456789",
    "transaction_status": "settlement",
    "fraud_status": "accept"
  }'
```

### Langkah 3: Check Logs
```bash
tail -f storage/logs/laravel.log | grep -i "payment\|transaction"
```

---

## 4. SEEDING DATABASE DENGAN TEST DATA

### Langkah 1: Edit Seeder
Create file `database/seeders/TestPaymentSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;

class TestPaymentSeeder extends Seeder
{
    public function run()
    {
        // Create test customer
        $user = User::firstOrCreate([
            'email' => 'testcustomer@example.com'
        ], [
            'name' => 'Test Customer',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);

        Customer::firstOrCreate(['user_id' => $user->id], [
            'phone' => '081234567890'
        ]);

        // Create test services
        $services = Service::limit(3)->get();
        if ($services->isEmpty()) {
            $service = Service::create([
                'name' => 'Test Service',
                'description' => 'Test Service Description',
                'price' => 500000,
                'duration' => 2
            ]);
            $services = collect([$service]);
        }

        // Create multiple test transactions
        foreach (range(1, 5) as $i) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_number' => 'TRX-TEST-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'subtotal' => 500000,
                'tax' => 50000,
                'discount_amount' => 0,
                'total' => 550000,
                'payment_method' => collect(['credit_card', 'e_wallet', 'bank_transfer'])->random(),
                'status' => collect(['pending', 'paid', 'failed', 'completed'])->random(),
                'submitted_date' => now()->subDays(rand(1, 30)),
                'payment_date' => rand(0, 1) ? now()->subDays(rand(0, 10)) : null,
            ]);

            // Add items
            foreach ($services->take(2) as $service) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'unit_price' => $service->price,
                    'subtotal' => $service->price,
                    'description' => $service->description
                ]);
            }
        }

        $this->command->info('Test payment data created successfully!');
    }
}
```

### Langkah 2: Run Seeder
```bash
php artisan db:seed --class=TestPaymentSeeder
```

### Langkah 3: Lihat Data
```bash
php artisan tinker
>>> Transaction::all();
>>> Transaction::where('status', 'paid')->count();
```

---

## 5. TEST BERBAGAI PAYMENT STATES

### Scenario A: Transaksi PENDING → PAID
```php
// 1. Create pending transaction
$transaction = Transaction::create([/* ... */]);

// 2. Simulate 2 jam kemudian
$transaction->update(['submitted_date' => now()->subHours(2)]);

// 3. Pembayaran berhasil
$transaction->update([
    'status' => 'paid',
    'payment_date' => now()
]);
```

### Scenario B: Transaksi FAILED → RETRY
```php
// 1. Create failed transaction
$transaction = Transaction::create([/* ..., 'status' => 'failed' */]);

// 2. Lihat di halaman transaction detail
// 3. Coba pembayaran ulang
$transaction->update(['status' => 'awaiting_payment']);

// 4. Pembayaran kedua berhasil
$transaction->update([
    'status' => 'paid',
    'payment_date' => now()
]);
```

### Scenario C: Partial Payment (Invoice)
```php
$invoice = Invoice::first();

// Payment 1: 50% dari total
$payment1 = Payment::create([
    'invoice_id' => $invoice->id,
    'amount' => $invoice->total / 2,
    'payment_method' => 'bank_transfer',
    'status' => 'approved',
    'approved_date' => now()
]);

// Payment 2: Sisa 50%
$payment2 = Payment::create([
    'invoice_id' => $invoice->id,
    'amount' => $invoice->total / 2,
    'payment_method' => 'bank_transfer',
    'status' => 'approved',
    'approved_date' => now()
]);

// Invoice now fully paid
$invoice->update(['status' => 'paid', 'paid_date' => now()]);
```

---

## 6. TEST NOTIFICATION

### Test Email Notification Locally

#### Metode 1: Menggunakan Mailtrap
1. Buat akun di https://mailtrap.io (gratis)
2. Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
```

3. Test via Tinker:
```php
$user = User::first();
$transaction = Transaction::first();
$user->notify(new \App\Notifications\PaymentSuccessNotification($transaction));
```

4. Check inbox di Mailtrap dashboard

#### Metode 2: Menggunakan Log (Development)
Edit `.env`:
```env
MAIL_MAILER=log
```

Check email di: `storage/logs/laravel.log`

---

## 7. CHECKLIST SIMULASI LENGKAP

### ✅ Test E-wallet Payment
- [ ] Cart → Checkout → Transaction Detail
- [ ] Click E-Wallet icon
- [ ] Select payment method (GCash/OVO/Dana)
- [ ] Verify payment success card appears
- [ ] Check email notification received

### ✅ Test Credit Card
- [ ] Repeat E-wallet flow
- [ ] Select Credit Card
- [ ] Use test card: 4111 1111 1111 1111
- [ ] Complete payment
- [ ] Verify success

### ✅ Test Database Notifications
- [ ] View notifications table: `SELECT * FROM notifications;`
- [ ] Check notification has type 'payment_success'
- [ ] Verify data structure

### ✅ Test Failed Payment
- [ ] Manual update transaction status to 'failed'
- [ ] Verify halaman menampilkan error state
- [ ] Test retry payment flow

### ✅ Test Webhook
- [ ] Trigger webhook dengan curl/Postman
- [ ] Check logs untuk "Webhook Received"
- [ ] Verify transaction status updated

---

## 8. QUICK TEST COMMANDS

Combine semua dalam satu script test:

### File: `test-payment.php` (run manually)
```php
<?php
// cd ke project root dan run: php test-payment.php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionItem;

// Create test user
$user = User::firstOrCreate(['email' => 'payment-test@test.com'], [
    'name' => 'Payment Test User',
    'password' => bcrypt('password'),
    'role' => 'customer'
]);

// Create test transaction
$transaction = Transaction::create([
    'user_id' => $user->id,
    'transaction_number' => 'TEST-' . time(),
    'subtotal' => 100000,
    'tax' => 10000,
    'total' => 110000,
    'status' => 'pending',
]);

echo "✓ Test transaction created: {$transaction->transaction_number}\n";
echo "✓ Transaction URL: http://localhost/ecommerce/transactions/{$transaction->id}\n";
echo "✓ Test user: {$user->email} / password\n";

// Simulate payment
$transaction->update(['status' => 'paid', 'payment_date' => now()]);
echo "✓ Payment simulated as successful\n";
```

Run:
```bash
php test-payment.php
```

---

## 9. POSTMAN COLLECTION

Untuk testing API endpoints:

### Import ke Postman:
```json
{
  "info": {
    "name": "Payment System Tests",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Create Transaction",
      "request": {
        "method": "POST",
        "url": "http://localhost/ecommerce/checkout/process",
        "body": {
          "mode": "form-data",
          "formdata": [
            {"key": "payment_method", "value": "e_wallet"},
            {"key": "agree_terms", "value": "on"}
          ]
        }
      }
    },
    {
      "name": "Get Transaction Status",
      "request": {
        "method": "GET",
        "url": "http://localhost/ecommerce/transactions/1/payment-status"
      }
    },
    {
      "name": "Webhook Test",
      "request": {
        "method": "POST",
        "url": "http://localhost/webhooks/midtrans",
        "body": {
          "mode": "raw",
          "raw": "{\"order_id\":\"TRX-123\",\"transaction_status\":\"settlement\"}"
        }
      }
    }
  ]
}
```

---

## 10. TROUBLESHOOTING

### Problem: Email tidak terkirim
```bash
# Check if queue is running
ps aux | grep queue:work

# If not, start it
php artisan queue:work --daemon
```

### Problem: Notification tidak tersimpan
```php
// Check notifications table
php artisan migrate
php artisan migrate --path=database/migrations --database=notification

// Or create manually
DB::table('notifications')->get();
```

### Problem: Webhook signature invalid
- Verify Midtrans server key di `.env`
- Check Midtrans webhook URL configuration
- Logs: `storage/logs/laravel.log | grep "Invalid Midtrans"`

---

## Summary

**Fastest Way (< 5 menit):**
1. Login customer
2. Add service to cart
3. Checkout
4. Click E-Wallet → Use test card
5. Verify success card appears

**Complete Testing (30 menit):**
1. Run TestPaymentSeeder
2. Test multiple scenarios (pending, failed, retry)
3. Check email notifications
4. Verify webhook logs
5. Test from Tinker


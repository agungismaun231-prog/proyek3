# Payment Notification System

**Update Date:** May 20, 2026

## Overview

Sistem notifikasi pembayaran telah ditambahkan untuk memberitahu customer ketika pembayaran mereka berhasil diproses. Notifikasi dikirim melalui **email** dan ditampilkan di halaman **transaction detail** dengan status "paid".

---

## Komponen Notifikasi

### 1. **PaymentSuccessNotification Class** 
- **File:** `app/Notifications/PaymentSuccessNotification.php`
- **Fungsi:** Mengirim email dan database notification ke customer saat payment sukses
- **Delivery Channels:**
  - `mail` - Email notification ke inbox customer
  - `database` - Disimpan di Laravel notifications table untuk ditampilkan di UI

**Email Content:**
```
Subject: ✓ Pembayaran Berhasil - [Transaction Number]

Isi:
- Greeting dengan nama customer
- Konfirmasi pembayaran berhasil
- Nomor transaksi
- Total pembayaran
- Link ke halaman detail transaksi
```

### 2. **TransactionObserver**
- **File:** `app/Observers/TransactionObserver.php`
- **Fungsi:** Otomatis mengirim notifikasi ketika transaction status berubah menjadi 'paid' atau 'completed'
- **Event:** `updated` - Dipicu saat transaction diupdate

**How it works:**
```php
// When transaction status changes to 'paid' or 'completed'
if ($transaction->isDirty('status') && in_array($transaction->status, ['paid', 'completed'])) {
    $transaction->user->notify(new PaymentSuccessNotification($transaction));
}
```

### 3. **PaymentWebhookController Updates**
- **File:** `app/Http/Controllers/PaymentWebhookController.php`
- **Fungsi:** Handle Midtrans webhook dan update transaction status

**Perubahan:**
- Tambahan: `processTransactionNotification()` method untuk handle e-commerce transaction payment
- Auto-approve invoice payment + send notification
- Update transaction status saat payment sukses dari webhook
- Support untuk baik Payment (invoice) maupun Transaction (e-commerce)

### 4. **Transaction Detail Page**
- **File:** `resources/views/ecommerce/transactions/show.blade.php`
- **Tampilan:** 
  - Success message jika `status === 'paid' atau 'completed'`
  - Flash message untuk session notifications
  - Error message display

---

## Payment Flow dengan Notifikasi

### E-commerce Transaction (New)
```
1. User klik E-Wallet icon di transaction detail
   ↓
2. Redirect ke Midtrans Snap
   ↓
3. User lakukan pembayaran
   ↓
4. Midtrans send webhook → PaymentWebhookController
   ↓
5. Transaction status = 'paid' (database updated)
   ↓
6. TransactionObserver detect status change
   ↓
7. Send PaymentSuccessNotification (email + database)
   ↓
8. User lihat "Pembayaran Berhasil!" card di transaction detail
   ↓
9. Customer terima email konfirmasi
```

### Invoice Payment (Existing)
```
1. Customer submit payment → PaymentWebhookController (jika via gateway)
   ↓
2. Midtrans send webhook
   ↓
3. handlePaymentSuccess() dipicu
   ↓
4. Payment auto-approved + Invoice status = 'paid'
   ↓
5. Send PaymentSuccessNotification
```

---

## Notification Flow Diagram

```
Midtrans Webhook
      ↓
PaymentWebhookController::midtransNotification()
      ↓
   ├─→ Payment found? → processNotification() → handlePaymentSuccess()
   │                                          ↓
   │                              Auto-approve payment
   │                              + Send notification
   │
   └─→ Transaction found? → processTransactionNotification()
                           ↓
                   Update status to 'paid'
                   ↓
                   TransactionObserver::updated() triggered
                   ↓
                   Send PaymentSuccessNotification
```

---

## Teknologi yang Digunakan

- **Laravel Notifications Framework**: Email & database notifications
- **Queueable**: Notification diqueue untuk async processing
- **Model Observers**: Event-driven notifications
- **Blade Templates**: UI display notifications

---

## Configuration

### Email Configuration (`.env`)
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io (or your mail provider)
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Jasa Servis AC"
```

### Queue Configuration (`.env`) - Optional
Jika ingin async notifications:
```env
QUEUE_CONNECTION=async  # or redis, database, etc
```

---

## Testing Notifications

### 1. Test dengan Tinker
```bash
php artisan tinker

# Create test transaction
$transaction = App\Models\Transaction::first();

# Send notification
$transaction->user->notify(new App\Notifications\PaymentSuccessNotification($transaction));
```

### 2. Test Webhook Locally
```bash
# Install Laravel Tunnel untuk expose localhost
composer require --dev laravel/pint

# Atau gunakan ngrok
ngrok http 8000
# Update MIDTRANS_WEBHOOK_URL di Midtrans dashboard ke ngrok URL
```

### 3. Check Notifications di Database
```sql
SELECT * FROM notifications WHERE notifiable_id = [user_id] ORDER BY created_at DESC;
```

---

## Notification Content Example

### Email
```
Subject: ✓ Pembayaran Berhasil - TRX-20260520-0001

Halo John Doe,

Pembayaran Anda telah berhasil diproses!

**Nomor Transaksi:** TRX-20260520-0001
**Total Pembayaran:** Rp 500.000
**Tanggal:** 20 May 2026 14:30

[Lihat Detail Transaksi]

Terima kasih telah menggunakan layanan kami!
Salam hormat,
Tim Jasa Servis AC
```

### Database Notification (JSON)
```json
{
  "type": "payment_success",
  "transaction_id": 123,
  "transaction_number": "TRX-20260520-0001",
  "amount": 500000,
  "message": "Pembayaran Rp 500.000 untuk transaksi TRX-20260520-0001 telah berhasil!"
}
```

---

## Files Modified/Created

| File | Type | Changes |
|------|------|---------|
| `app/Notifications/PaymentSuccessNotification.php` | NEW | Notification class untuk payment success |
| `app/Observers/TransactionObserver.php` | NEW | Observer untuk transaction updates |
| `app/Providers/AppServiceProvider.php` | MODIFIED | Register TransactionObserver |
| `app/Http/Controllers/PaymentWebhookController.php` | MODIFIED | Add transaction payment handling + notifications |
| `resources/views/ecommerce/transactions/show.blade.php` | MODIFIED | Display success message + flash notifications |

---

## Future Enhancements

1. **SMS Notifications** - Tambah SMS notification via Twilio
2. **In-App Notifications** - Real-time toast/alert notifications
3. **Payment Reminders** - Send reminder untuk pending payments
4. **Admin Notifications** - Notify admin saat ada payment baru
5. **Payment Receipt** - Generate dan email payment receipt PDF
6. **Notification Preferences** - Allow users untuk customize notification channels

---

## Troubleshooting

### Email Tidak Terkirim
- Check `.env` MAIL_* configuration
- Verify SMTP credentials
- Check Laravel logs: `storage/logs/laravel.log`
- Ensure `queue:listen` atau `queue:work` running jika menggunakan queue

### Webhook Tidak Diterima
- Verify Midtrans signature
- Check webhook URL di Midtrans dashboard
- Verify IP whitelist di Midtrans settings
- Check logs: `Log::info()` calls di PaymentWebhookController

### Observer Tidak Trigger
- Verify observer registered di AppServiceProvider::boot()
- Check model update actually changes the status
- Add logging: `Log::info('Observer fired')`

---

## Reference

- [Laravel Notifications](https://laravel.com/docs/notifications)
- [Laravel Model Observers](https://laravel.com/docs/eloquent#observers)
- [Midtrans Webhook Documentation](https://docs.midtrans.com/en/after-payment/http-notification)


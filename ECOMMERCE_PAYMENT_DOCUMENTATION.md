# E-COMMERCE PAYMENT SYSTEM DOCUMENTATION

## Gambaran Umum

Sistem pembayaran e-commerce lengkap dengan integrasi Payment Gateway (Midtrans), shopping cart, checkout, dan tracking pembayaran real-time.

---

## Fitur Utama

### 1. **Shopping Cart Management**
- Pelanggan dapat menambahkan service ke keranjang
- Update quantity items
- Remove items
- View cart summary
- Apply promo codes untuk diskon

### 2. **Payment Methods**
- 💳 **Credit Card / Debit Card** (via Midtrans)
- 🏦 **Bank Transfer** (Manual atau Midtrans)
- 📱 **E-wallet** (GCash, OVO, Dana, etc via Midtrans)
- 💰 **Cash on Delivery (COD)**

### 3. **Promo & Discount System**
- Kode promo dengan validasi date range
- Fixed amount atau percentage discount
- Minimum purchase requirement
- Usage limit per customer
- Max discount cap untuk percentage promo

### 4. **Transaction Management**
- Real-time payment status tracking
- Multiple payment history per transaction (untuk partial payments)
- Automatic invoice generation
- Payment receipt generation

### 5. **Payment Gateway Integration**
- **Midtrans** integration dengan Snap token
- Webhook callback handling
- Transaction status reconciliation
- Support untuk 3D Secure

---

## Database Schema

### Tables

#### `shopping_carts`
Menyimpan keranjang belanja aktif user.
```
- id (PK)
- user_id (FK) -> users
- subtotal (decimal)
- tax (decimal)
- discount_amount (decimal)
- discount_code (string, nullable)
- total (decimal)
- status (enum: active, completed, abandoned)
- timestamps
```

#### `cart_items`
Items individual dalam shopping cart.
```
- id (PK)
- shopping_cart_id (FK) -> shopping_carts
- service_id (FK) -> services
- quantity (integer)
- unit_price (decimal)
- subtotal (decimal)
- notes (text, nullable)
- timestamps
```

#### `promo_codes`
Master data untuk promo codes.
```
- id (PK)
- code (string, unique) - SUMMER2025, NEW_CUSTOMER, dll
- description (text)
- discount_type (enum: percentage, fixed_amount)
- discount_value (decimal)
- max_discount (decimal, nullable) - untuk percentage
- min_purchase (decimal)
- usage_limit (integer, nullable) - total limit
- usage_per_customer (integer) - limit per customer
- times_used (integer)
- valid_from (datetime)
- valid_until (datetime)
- is_active (boolean)
- timestamps
```

#### `transactions`
Main transaction record untuk pembayaran e-commerce.
```
- id (PK)
- user_id (FK) -> users
- transaction_number (string, unique) - TRX-YYYYMMDD-0001
- subtotal, tax, discount_amount (decimal)
- promo_code (string, nullable)
- total (decimal)
- payment_method (enum: credit_card, bank_transfer, e_wallet, cash, check)
- payment_gateway (string, nullable) - midtrans, xendit, dll
- gateway_transaction_id (string, nullable)
- amount_paid (decimal)
- payment_reference (string, nullable)
- payment_proof (string, nullable) - file path
- status (enum: pending, processing, awaiting_payment, paid, partially_paid, completed, cancelled, failed, refunded)
- gateway_status (string, nullable)
- gateway_response (json, nullable)
- submitted_date (datetime, nullable)
- payment_date (datetime, nullable)
- completed_date (datetime, nullable)
- approved_by (FK) -> users (nullable)
- approved_date (datetime, nullable)
- admin_notes (text, nullable)
- invoice_id (FK, nullable) -> invoices
- metadata (json) - browser info, ip, dll
- timestamps
```

#### `transaction_items`
Items dalam transaksi.
```
- id (PK)
- transaction_id (FK) -> transactions
- service_id (FK) -> services
- quantity (integer)
- unit_price (decimal)
- subtotal (decimal)
- description (text, nullable)
- timestamps
```

#### `payment_history`
Riwayat pembayaran detail untuk setiap transaksi.
```
- id (PK)
- transaction_id (FK) -> transactions
- amount (decimal)
- payment_method (string)
- payment_reference (string, nullable)
- status (enum: pending, success, failed, cancelled)
- remarks (string, nullable)
- gateway_data (json, nullable)
- timestamps
```

---

## API Routes

### Shopping Cart Routes

#### Add Item to Cart
```
POST /cart/add
Headers: Authorization, Content-Type: application/json
Body: {
    "service_id": 1,
    "quantity": 2,
    "notes": "Optional notes" (optional)
}
Response: {
    "success": true,
    "message": "Item berhasil ditambahkan ke keranjang",
    "cart_count": 5,
    "cart_total": 1500000
}
```

#### View Cart
```
GET /cart
Response: view dengan cart items dan summary
```

#### Update Item Quantity
```
POST /cart/update/{item_id}
Body: {
    "quantity": 3
}
```

#### Remove Item
```
POST /cart/remove/{item_id}
```

#### Apply Promo Code
```
POST /cart/apply-promo
Body: {
    "promo_code": "SUMMER2025"
}
Response: {
    "success": true,
    "discount_amount": 150000,
    "cart_total": 1350000
}
```

#### Clear Cart
```
POST /cart/clear
```

#### Cart Summary (AJAX)
```
GET /cart/summary
Response: {
    "count": 5,
    "total": 1500000,
    "subtotal": 1363636.36,
    "tax": 136363.64,
    "discount": 0
}
```

---

### Checkout Routes

#### Show Checkout Form
```
GET /checkout
Requires: auth, active cart with items
Response: checkout page dengan payment method options
```

#### Process Checkout
```
POST /checkout/process
Headers: Content-Type: application/json
Body: {
    "payment_method": "credit_card",
    "notes": "Optional notes"
}
Response: redirect ke payment page
```

#### Checkout Success
```
GET /checkout/success/{transaction_id}
Response: success page dengan transaction details
```

#### Checkout Failed
```
GET /checkout/failed/{transaction_id}
Response: failed page
```

---

### Transaction Routes

#### List User Transactions
```
GET /transactions
Response: paginated list transaksi user
```

#### Show Transaction Detail
```
GET /transactions/{id}
Response: transaction detail dengan items dan payment history
```

#### Initiate Payment
```
POST /transactions/{id}/pay
Response: {
    "success": true,
    "payment_url": "https://app.midtrans.com/snap/v2/vtweb/...",
    "message": "Silakan lakukan pembayaran"
}
```

#### Check Payment Status
```
GET /transactions/{id}/payment-status
Response: {
    "status": "paid",
    "amount_paid": 1500000,
    "remaining": 0,
    "gateway_status": "settlement"
}
```

#### Cancel Transaction
```
POST /transactions/{id}/cancel
Body: {
    "reason": "Changed my mind"
}
```

---

### Payment Routes

#### Payment Checkout Page
```
GET /payment/checkout/{transaction_id}
- Redirect ke Midtrans payment page
```

#### Midtrans Webhook
```
POST /payment/callback
- Automatic callback dari Midtrans saat payment selesai
- Auto process transaction update
- No authentication required (server-to-server)
```

#### Payment Finish
```
GET /payment/finish?order_id=TRX-20250514-0001
- Callback setelah payment sukses
- Menampilkan status dan invoice
```

#### Payment Error
```
GET /payment/error?order_id=TRX-20250514-0001
- Callback jika payment gagal
```

#### Payment Pending
```
GET /payment/pending?order_id=TRX-20250514-0001
- Callback jika payment pending (menunggu konfirmasi)
```

---

## Payment Flow

### 1. Shopping & Cart
```
User selects services 
→ Click "Add to Cart"
→ View cart
→ Modify quantity/remove items
→ Apply promo code (optional)
→ Review cart total
```

### 2. Checkout
```
Click "Checkout"
→ Review order details
→ Select payment method
→ Add notes (optional)
→ Click "Proceed to Payment"
→ Create transaction record
```

### 3. Payment (Credit Card / E-wallet / Bank Transfer)
```
System redirects to Midtrans Snap page
→ Customer selects payment method
→ Enter payment details
→ Midtrans process payment
→ Callback to our server
→ Update transaction status
→ Redirect to success/error page
```

### 4. Payment (Cash / Manual)
```
Create transaction
→ Customer manually transfer or pay cash
→ Upload payment proof (optional)
→ System in "awaiting_approval" status
→ Admin review & approve
→ Transaction complete
```

### 5. Post-Payment
```
System generates invoice
→ Send email with receipt
→ Update booking status
→ Schedule service appointment
```

---

## Models & Methods

### ShoppingCart Model

**Key Methods:**
```php
// Add item ke cart
$cart->addItem($service, $quantity, $notes);

// Remove item
$cart->removeItem($cartItemId);

// Update quantity
$cart->updateItemQuantity($cartItemId, $quantity);

// Apply promo
$cart->applyPromoCode($code);

// Recalculate total
$cart->recalculateTotal();

// Clear cart
$cart->clear();

// Query scopes
ShoppingCart::active()->byUser($userId);
```

### Transaction Model

**Key Methods:**
```php
// Generate unique transaction number
Transaction::generateTransactionNumber(); // TRX-20250514-0001

// Check payment status
$transaction->isPaidFull();
$transaction->isPartiallyPaid();
$transaction->getRemainingAmount();

// Record payment
$transaction->recordPayment($amount, $method, $reference, $remarks, $gatewayData);

// Admin actions
$transaction->approve($adminId, $notes);
$transaction->reject($adminId, $reason);
$transaction->cancel($reason);

// Query scopes
Transaction::pending()->byUser($userId);
Transaction::paid()->byDateRange($start, $end);
```

### PromoCode Model

**Key Methods:**
```php
// Check validity
$promo->isValid();
$promo->canUseByUser($userId);

// Calculate discount
$promo->calculateDiscount($total);

// Update usage
$promo->incrementUsage();

// Query scopes
PromoCode::active()->byCode($code);
```

---

## Configuration

### .env Settings

```env
# Midtrans Configuration
MIDTRANS_MERCHANT_ID=G123456789
MIDTRANS_CLIENT_KEY=Mid-client-xxxxx
MIDTRANS_SERVER_KEY=Mid-server-xxxxx
MIDTRANS_ENVIRONMENT=sandbox

# Timezone untuk transaction timestamps
APP_TIMEZONE=Asia/Jakarta

# Tax rate (percentage)
PAYMENT_TAX_RATE=10
```

### config/services.php

```php
'midtrans' => [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'),
],
```

---

## Admin Dashboard Features

### Payment Management
- View all transactions dengan filtering
- Approve/reject manual payments
- View payment history & details
- Generate payment reports
- Refund management

### Promo Management
- Create/edit/delete promo codes
- Set discount type dan value
- Set validity date range
- Monitor promo usage

### Reports
- Daily/weekly/monthly payment reports
- Top products/services
- Customer payment behavior analysis
- Revenue reports

---

## Security Considerations

1. **Authentication & Authorization**
   - All routes protected dengan auth middleware
   - User-specific data access only
   - Admin-only approval endpoints

2. **Payment Security**
   - Server-to-server Midtrans callback verification
   - Never expose server key di frontend
   - Use HTTPS only
   - Implement rate limiting on payment endpoints

3. **Data Validation**
   - Validate semua input dari user
   - Sanitize file uploads
   - Use prepared statements untuk queries

4. **Audit Trail**
   - Log semua payment transactions
   - Keep payment history immutable
   - Soft delete untuk audit purposes

---

## Implementation Checklist

- [x] Create database migrations
- [x] Create models (ShoppingCart, Transaction, PromoCode, etc)
- [x] Create controllers (CartController, CheckoutController, TransactionController, PaymentController)
- [ ] Create views (cart, checkout, payment, transaction history)
- [ ] Create routes (web.php)
- [ ] Configure Midtrans integration
- [ ] Setup webhook handling
- [ ] Create admin dashboard for promo management
- [ ] Create admin dashboard for payment management
- [ ] Add email notifications
- [ ] Add validation & error handling
- [ ] Add pagination & filtering
- [ ] Add testing

---

## API Testing

### cURL Examples

#### Add Item to Cart
```bash
curl -X POST http://localhost:8000/cart/add \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"service_id": 1, "quantity": 2}'
```

#### Apply Promo
```bash
curl -X POST http://localhost:8000/cart/apply-promo \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"promo_code": "SUMMER2025"}'
```

#### Checkout
```bash
curl -X POST http://localhost:8000/checkout/process \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"payment_method": "credit_card"}'
```

---

## Troubleshooting

### Payment tidak terproses
1. Check Midtrans server key di .env
2. Verify merchant account status
3. Check payment gateway logs
4. Verify transaction record di database

### Promo tidak bisa dipakai
1. Check promo validity (valid_from, valid_until)
2. Check usage limit (usage_limit, times_used)
3. Check usage per customer
4. Check min_purchase requirement

### Cart tidak update
1. Check browser console untuk JavaScript errors
2. Verify CSRF token di form
3. Check database connection

---

## Contact & Support

Untuk questions atau issues terkait payment system, silakan contact admin atau refer ke Midtrans documentation.

- **Midtrans Docs**: https://docs.midtrans.com/
- **Midtrans Dashboard**: https://app.midtrans.com/

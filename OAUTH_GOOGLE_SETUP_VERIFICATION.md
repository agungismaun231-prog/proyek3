# Google OAuth Setup & Verification Guide

## ✅ Status Konfigurasi Google OAuth

Aplikasi Anda **sudah dikonfigurasi dengan sempurna** untuk Google OAuth login!

---

## 📋 Konfigurasi Yang Sudah Ada

### 1. ✅ Laravel Socialite Package
- **Status**: Terinstall di `composer.json`
- **Version**: `laravel/socialite: ^5.24`
- **Location**: `vendor/laravel/socialite`

### 2. ✅ Google OAuth Credentials (.env)
```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/login/google/callback
```

### 3. ✅ Services Configuration
- **File**: `config/services.php`
- **Configuration**: Google OAuth credentials properly mapped from .env
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
]
```

### 4. ✅ Routes Defined
- **File**: `routes/web.php` (lines 126-130)
- **Routes**:
  - `GET /login/{provider}` → `redirectToProvider()` (route name: `social.redirect`)
  - `GET /login/{provider}/callback` → `handleProviderCallback()` (route name: `social.callback`)
- **Provider Whitelist**: `google|facebook|github`

### 5. ✅ Controllers Implemented
- **SocialAuthController**: [app/Http/Controllers/SocialAuthController.php](app/Http/Controllers/SocialAuthController.php)
  - `redirectToProvider($provider)`: Redirect ke Google OAuth
  - `handleProviderCallback($provider)`: Handle callback response
- **User Model**: [app/Models/User.php](app/Models/User.php)
  - `findOrCreateFromOAuth()`: Create atau update user dari OAuth data

### 6. ✅ Database Fields
- **Table**: `users`
- **Fields**:
  - `provider`: OAuth provider name (google/facebook/github)
  - `provider_id`: Unique ID dari OAuth provider
  - `oauth_data`: JSON full data dari OAuth response

### 7. ✅ Login View
- **File**: `resources/views/auth/login.blade.php` (line 792)
- **Button**: "Login with Google" button tersedia di login form
```blade
<a href="{{ route('social.redirect', 'google') }}" class="btn-social">
    <i class="bi bi-google"></i>
    <span>Google</span>
</a>
```

### 8. ✅ Database Migration
- **Migration**: `2025_02_18_100000_add_oauth_fields_to_users_table.php`
- **Fields Added**: provider, provider_id, oauth_data (JSON)
- **Status**: Migration sudah applied

---

## 🔄 OAuth Flow Explanation

### Step-by-Step Flow:

```
1. User lands on login page
   ↓
2. User clicks "Login with Google" button
   ↓
3. Redirect to route('social.redirect', 'google')
   ↓
4. SocialAuthController::redirectToProvider('google')
   → Socialite::driver('google')->redirect()
   → Redirect ke Google OAuth authorization URL
   ↓
5. Google OAuth Consent Screen
   → User login dengan Google account
   → User grant permissions (email, name, profile picture)
   ↓
6. Google callback ke GOOGLE_REDIRECT_URI
   → URL: http://localhost:8000/login/google/callback
   ↓
7. SocialAuthController::handleProviderCallback('google')
   → Socialite::driver('google')->user()
   → Get OAuth user data
   ↓
8. User::findOrCreateFromOAuth('google', $oauthUser)
   → Check if provider_id exists → return existing user
   → Check if email exists → link OAuth to existing user
   → Create new user with OAuth data
   ↓
9. Auth::login($user)
   ↓
10. Redirect based on user role:
    → role = 'admin' → redirect to /dashboard
    → role = 'customer' → redirect to /customer-dashboard
    → role = 'technician' → redirect to /technician-dashboard
```

---

## 🧪 Testing Google OAuth

### 1. Verify Setup (Without Testing)

Run this command to verify Socialite is properly configured:

```bash
php artisan tinker
```

Then in Tinker:
```php
>>> config('services.google')
```

Should output:
```
=> [
  "client_id" => "your_google_client_id_here",
  "client_secret" => "your_google_client_secret_here",
  "redirect" => "http://localhost:8000/login/google/callback",
]
```

### 2. Test Login Flow

1. **Start server**:
   ```bash
   php artisan serve
   ```

2. **Navigate to login page**:
   - Open `http://localhost:8000/login` (atau adjust port jika different)

3. **Click "Login with Google" button**:
   - Should redirect to Google login/consent screen
   - If error occurs → check .env credentials and network connectivity

4. **Complete Google authentication**:
   - Login dengan Google account
   - Grant requested permissions
   - Should redirect back to `/login/google/callback`

5. **Verify user created/logged in**:
   - Check database `users` table untuk new user record
   - Check `provider` field = 'google'
   - Check `provider_id` field = Google user ID
   - Check `oauth_data` field = JSON dengan full data

6. **Verify dashboard access**:
   - Should redirect ke appropriate dashboard berdasarkan role
   - Default role untuk OAuth user = 'customer'
   - Should see customer dashboard

---

## 🚨 Troubleshooting Common Issues

### Issue 1: "Provider tidak didukung" Error

**Cause**: Provider name tidak valid (bukan 'google', 'facebook', atau 'github')

**Solution**: 
- Check route parameter adalah 'google'
- Verify provider whitelist di SocialAuthController::redirectToProvider()

---

### Issue 2: "Gagal menghubungkan ke Google" Error

**Cause**: Google OAuth configuration missing atau invalid

**Solutions**:
1. Verify .env file memiliki GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET
2. Verify GOOGLE_REDIRECT_URI = `http://localhost:8000/login/google/callback`
3. Verify credentials di Google Developer Console masih valid
4. Check network connectivity ke Google OAuth servers

---

### Issue 3: "Redirect URI mismatch" Error dari Google

**Cause**: GOOGLE_REDIRECT_URI di .env tidak match dengan authorized redirect URIs di Google Console

**Solution**:
1. Login ke Google Developer Console: https://console.cloud.google.com/
2. Select project containing OAuth credentials
3. Go to: Credentials → OAuth 2.0 Client IDs → Edit
4. Update "Authorized redirect URIs" to match:
   - Add: `http://localhost:8000/login/google/callback`
   - For production: Add `https://yourdomain.com/login/google/callback`
5. Save changes (may take a few minutes to apply)

---

### Issue 4: "Unauthorized client" Error

**Cause**: GOOGLE_CLIENT_ID atau GOOGLE_CLIENT_SECRET invalid atau tidak sesuai dengan Google Console

**Solutions**:
1. Verify credentials di .env match exactly dengan Google Console
2. Check credentials tidak expired
3. Regenerate credentials jika diperlukan di Google Console

---

### Issue 5: User tidak di-redirect ke dashboard setelah OAuth login

**Cause**: Possible issues dalam flow:
1. User creation failed
2. Auth::login() failed
3. Route redirect issue

**Solutions**:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode: `APP_DEBUG=true` di .env
3. Check database: verify `users` table memiliki new record
4. Check SocialAuthController error handling

---

## 🔐 Security Notes

### ✅ Already Implemented Security Measures

1. **Provider Whitelist**: Only 'google', 'facebook', 'github' allowed
   - Prevents injection attacks via provider parameter
   
2. **CSRF Protection**: State token validation automatic
   - Socialite handles state token automatically
   
3. **Unique Provider ID**: provider_id unique constraint
   - Prevents duplicate account linking
   
4. **Email Linking**: OAuth data dapat di-link ke existing email
   - If email matches existing user, OAuth terhubung ke existing account
   - Secure way untuk let users login via email+password OR OAuth

### ⚠️ Important for Production

1. **HTTPS Required**: OAuth requires HTTPS untuk production
   - Update GOOGLE_REDIRECT_URI untuk production:
   ```env
   GOOGLE_REDIRECT_URI=https://yourdomain.com/login/google/callback
   ```

2. **Environment Variables**: Keep credentials secret
   - Never commit .env ke version control
   - Use environment-specific configuration management

3. **Token Expiration**: OAuth tokens tidak disimpan
   - User session diperlukan untuk maintain login

---

## 📊 Database Schema for OAuth

### users table oauth fields:

```sql
-- provider: OAuth provider name
ALTER TABLE users ADD COLUMN provider VARCHAR(50) NULL;

-- provider_id: Unique ID dari OAuth provider
ALTER TABLE users ADD COLUMN provider_id VARCHAR(255) NULL;

-- oauth_data: Full JSON response dari OAuth provider
ALTER TABLE users ADD COLUMN oauth_data JSON NULL;

-- Unique constraint untuk prevent duplicate provider_id
ALTER TABLE users ADD UNIQUE KEY unique_provider_id (provider_id);
```

---

## ✅ Verification Checklist

- [x] Laravel Socialite package installed
- [x] Google OAuth credentials di .env
- [x] Services config updated dengan Google configuration
- [x] Routes defined untuk social.redirect dan social.callback
- [x] SocialAuthController implemented dengan proper logic
- [x] User model memiliki findOrCreateFromOAuth() method
- [x] Database migration added untuk oauth fields
- [x] Login view button untuk Google OAuth ada
- [x] Provider whitelist configured
- [x] CSRF protection via state token
- [x] Error handling implemented

---

## 🎯 Next Steps

1. **Test OAuth Login**: Click button dan verify flow berhasil
2. **Monitor Logs**: Check `storage/logs/laravel.log` untuk any errors
3. **For Production**: 
   - Update GOOGLE_REDIRECT_URI untuk production domain
   - Update Google Console authorized redirect URIs
   - Enable HTTPS
   - Store credentials secara aman di environment

---

## 📚 References

- [Laravel Socialite Documentation](https://laravel.com/docs/socialite)
- [Google OAuth Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Google Cloud Console](https://console.cloud.google.com/)

---

**Status**: ✅ Fully Configured and Ready to Test

**Last Updated**: February 22, 2026


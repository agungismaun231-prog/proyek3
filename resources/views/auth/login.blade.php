<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - JASAKU | Layanan AC Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --blue-300:#93c5fd;--blue-400:#60a5fa;--blue-500:#3b82f6;--blue-600:#2563eb;--blue-700:#1d4ed8;
            --success:#10b981;--danger:#ef4444;--warning:#f59e0b;--info:#06b6d4;
            --bg-dark:#0b1120;--bg-panel:#111827;--bg-elevated:#1a2234;
            --border:rgba(255,255,255,0.08);--text-primary:#f1f5f9;
            --text-secondary:#94a3b8;--text-muted:#64748b;
            --radius-sm:6px;--radius-md:10px;--radius-lg:14px;--radius-xl:20px;
            --shadow-lg:0 20px 60px rgba(0,0,0,0.5);--tr:all 0.2s cubic-bezier(0.4,0,0.2,1);
        }
        html,body{height:100%;font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;-webkit-font-smoothing:antialiased;}
        body{display:flex;flex-direction:column;min-height:100vh;background:var(--bg-dark);color:var(--text-primary);}
        .top-nav{background:var(--bg-panel);border-bottom:1px solid var(--border);padding:0 32px;height:60px;display:flex;align-items:center;flex-shrink:0;}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
        .brand-logo-sm{width:32px;height:32px;background:linear-gradient(135deg,var(--blue-600),var(--blue-400));border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(37,99,235,0.35);}
        .brand-logo-sm i{color:#fff;font-size:16px;}
        .brand-name{font-size:16px;font-weight:800;color:var(--text-primary);letter-spacing:-.5px;}
        .brand-name span{color:var(--blue-400);}
        .nav-links{margin-left:auto;display:flex;align-items:center;gap:4px;}
        .nav-link-item{display:flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid transparent;border-radius:var(--radius-md);color:var(--text-secondary);text-decoration:none;font-size:13px;font-weight:500;transition:var(--tr);}
        .nav-link-item:hover{background:var(--bg-elevated);border-color:var(--border);color:var(--text-primary);}
        .nav-link-item.accent{color:var(--warning);}
        .auth-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 24px;}
        .auth-container{width:100%;max-width:1100px;display:grid;grid-template-columns:1fr 420px;gap:80px;align-items:center;}
        .hero-side{color:var(--text-primary);}
        .hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(37,99,235,.12);border:1px solid rgba(37,99,235,.25);border-radius:20px;padding:4px 14px;font-size:12px;font-weight:600;color:var(--blue-400);margin-bottom:24px;letter-spacing:.5px;text-transform:uppercase;}
        .hero-side h1{font-size:48px;font-weight:800;line-height:1.1;letter-spacing:-1.5px;color:var(--text-primary);margin-bottom:18px;}
        .hero-side h1 span{color:var(--blue-400);}
        .hero-side p{font-size:16px;color:var(--text-secondary);line-height:1.7;max-width:420px;margin-bottom:36px;}
        .feature-list{display:flex;flex-direction:column;gap:12px;}
        .feature-item{display:flex;align-items:center;gap:14px;padding:14px 16px;background:var(--bg-panel);border:1px solid var(--border);border-radius:var(--radius-md);transition:var(--tr);}
        .feature-item:hover{border-color:rgba(37,99,235,.3);transform:translateX(4px);}
        .feature-icon{width:36px;height:36px;border-radius:var(--radius-sm);background:rgba(37,99,235,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;color:var(--blue-400);}
        .feature-icon.green{background:rgba(16,185,129,.12);color:var(--success);}
        .feature-icon.amber{background:rgba(245,158,11,.12);color:var(--warning);}
        .feature-icon.cyan{background:rgba(6,182,212,.12);color:var(--info);}
        .feature-item span{font-size:13.5px;font-weight:500;color:var(--text-secondary);}
        .login-card{background:var(--bg-panel);border:1px solid var(--border);border-radius:var(--radius-xl);box-shadow:var(--shadow-lg);padding:40px;}
        .card-header-area{text-align:center;margin-bottom:32px;}
        .card-logo{width:48px;height:48px;background:linear-gradient(135deg,var(--blue-600),var(--blue-400));border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 4px 16px rgba(37,99,235,.4);}
        .card-logo i{color:#fff;font-size:22px;}
        .card-header-area h2{font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;margin-bottom:6px;}
        .card-header-area p{font-size:13px;color:var(--text-muted);}
        .form-group{margin-bottom:18px;}
        .f-label{display:block;font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--text-muted);margin-bottom:6px;}
        .f-control{width:100%;padding:10px 14px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text-primary);font-size:14px;font-family:'Inter',sans-serif;transition:var(--tr);outline:none;}
        .f-control::placeholder{color:var(--text-muted);}
        .f-control:focus{border-color:var(--blue-500);box-shadow:0 0 0 3px rgba(37,99,235,.15);}
        .f-control.is-invalid{border-color:var(--danger);}
        .f-error{font-size:12px;color:var(--danger);margin-top:5px;font-weight:500;}
        .remember-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;}
        .check-wrap{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-secondary);cursor:pointer;}
        .check-wrap input{width:16px;height:16px;accent-color:var(--blue-500);cursor:pointer;}
        .btn-login{width:100%;padding:11px;background:linear-gradient(135deg,var(--blue-600),var(--blue-500));border:none;border-radius:var(--radius-sm);color:white;font-size:14px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:var(--tr);display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 2px 10px rgba(37,99,235,.35);}
        .btn-login:hover{background:linear-gradient(135deg,var(--blue-700),var(--blue-600));transform:translateY(-1px);box-shadow:0 4px 16px rgba(37,99,235,.45);}
        .btn-login:active{transform:translateY(0);}
        .divider{display:flex;align-items:center;gap:12px;margin:22px 0;font-size:12px;color:var(--text-muted);font-weight:500;}
        .divider::before,.divider::after{flex:1;height:1px;background:var(--border);content:'';}
        .social-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:22px;}
        .btn-social{display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-elevated);color:var(--text-secondary);font-size:13px;font-weight:500;text-decoration:none;transition:var(--tr);}
        .btn-social:hover{border-color:rgba(37,99,235,.4);color:var(--blue-400);background:rgba(37,99,235,.08);}
        .card-footer-text{text-align:center;font-size:13px;color:var(--text-muted);}
        .card-footer-text a{color:var(--blue-400);text-decoration:none;font-weight:600;}
        .card-footer-text a:hover{color:var(--blue-300);}
        .login-alert{padding:10px 14px;border-radius:var(--radius-sm);background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#fca5a5;font-size:13px;margin-bottom:18px;}
        .page-footer{border-top:1px solid var(--border);padding:20px 32px;text-align:center;font-size:12px;color:var(--text-muted);}
        .footer-links{display:flex;justify-content:center;gap:20px;margin-bottom:8px;}
        .footer-links a{color:var(--text-muted);text-decoration:none;}
        .footer-links a:hover{color:var(--text-secondary);}
        @keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
        .hero-side{animation:fadeUp .5s ease forwards;}
        .login-card{animation:fadeUp .5s ease .15s both;}
        @media(max-width:900px){.auth-container{grid-template-columns:1fr;max-width:420px;gap:40px;}.hero-side h1{font-size:36px;}.hero-side{text-align:center;}.hero-side p{margin:0 auto 28px;}}
        @media(max-width:600px){.auth-wrap{padding:20px 16px;}.login-card{padding:28px 24px;}.hero-side{display:none;}.top-nav{padding:0 16px;}.social-grid{grid-template-columns:1fr;}}
    </style>
</head>
<body>
    <nav class="top-nav">
        <a class="nav-brand" href="/">
            <div class="brand-logo-sm"><i class="bi bi-snow"></i></div>
            <div class="brand-name">JASA<span>KU</span></div>
        </a>
        <div class="nav-links">
            <a class="nav-link-item" href="{{ route('login.technician') }}"><i class="bi bi-tools"></i> Teknisi</a>
            <a class="nav-link-item" href="{{ route('register') }}"><i class="bi bi-person-plus"></i> Daftar</a>
            <a class="nav-link-item accent" href="{{ route('register.provider') }}"><i class="bi bi-briefcase"></i> Penyedia Jasa</a>
        </div>
    </nav>

    <div class="auth-wrap">
        <div class="auth-container">
            <!-- Hero Side -->
            <div class="hero-side">
                <div class="hero-eyebrow"><i class="bi bi-snow"></i> Servis AC Profesional</div>
                <h1>Udara Sejuk,<br><span>Hidup Nyaman</span></h1>
                <p>Dapatkan layanan servis AC profesional dari teknisi berpengalaman dengan respons cepat dan harga terjangkau.</p>
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <span>Teknisi Profesional &amp; Tersertifikasi</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon cyan"><i class="bi bi-clock-history"></i></div>
                        <span>Tersedia 24/7 untuk Kebutuhan Anda</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon amber"><i class="bi bi-tag"></i></div>
                        <span>Harga Transparan Tanpa Biaya Tersembunyi</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon green"><i class="bi bi-hand-thumbs-up"></i></div>
                        <span>Jaminan Kepuasan dari Ribuan Pelanggan</span>
                    </div>
                </div>
            </div>

            <!-- Login Card -->
            <div class="login-card">
                <div class="card-header-area">
                    <div class="card-logo"><i class="bi bi-box-arrow-in-right"></i></div>
                    <h2>Selamat Datang</h2>
                    <p>Masuk untuk kelola pemesanan dan layanan Anda</p>
                </div>

                @if($errors->any())
                    <div class="login-alert">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        @foreach($errors->all() as $error){{ $error }} @endforeach
                    </div>
                @endif

                <form action="{{ route('auth.login') }}" method="POST" novalidate>
                    @csrf
                    <div class="form-group">
                        <label for="email" class="f-label">Email Address</label>
                        <input type="email" class="f-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}"
                               placeholder="nama@contoh.com" required autofocus>
                        @error('email')<div class="f-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password" class="f-label">Kata Sandi</label>
                        <input type="password" class="f-control @error('password') is-invalid @enderror"
                               id="password" name="password" placeholder="••••••••" required>
                        @error('password')<div class="f-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="remember-row">
                        <label class="check-wrap">
                            <input type="checkbox" name="remember" id="remember">
                            <span>Ingat saya</span>
                        </label>
                    </div>
                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                    </button>
                </form>

                <div class="divider">Atau lanjutkan dengan</div>

                <div class="social-grid">
                    <a href="{{ route('social.redirect', 'google') }}" class="btn-social">
                        <i class="bi bi-google"></i> <span>Google</span>
                    </a>
                    <a href="{{ route('social.redirect', 'facebook') }}" class="btn-social">
                        <i class="bi bi-facebook"></i> <span>Facebook</span>
                    </a>
                    <a href="{{ route('social.redirect', 'github') }}" class="btn-social">
                        <i class="bi bi-github"></i> <span>GitHub</span>
                    </a>
                </div>

                <div class="card-footer-text">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="page-footer">
        <div class="footer-links">
            <a href="#">Tentang Kami</a>
            <a href="#">Kebijakan Privasi</a>
            <a href="#">Syarat & Ketentuan</a>
            <a href="#">Bantuan</a>
        </div>
        &copy; {{ date('Y') }} JASAKU. Semua hak dilindungi.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

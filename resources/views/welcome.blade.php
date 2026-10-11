<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JASAKU - Layanan AC & Gadget Profesional</title>
    <meta name="description" content="Jasa servis AC dan gadget profesional terpercaya. Teknisi berpengalaman, harga transparan, dan layanan 24/7.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --blue-400:#60a5fa;--blue-500:#3b82f6;--blue-600:#2563eb;--blue-700:#1d4ed8;
            --success:#10b981;--warning:#f59e0b;--danger:#ef4444;--info:#06b6d4;
            --bg-dark:#0b1120;--bg-surface:#111827;--bg-elevated:#1a2234;
            --border:rgba(255,255,255,0.08);--text-primary:#f1f5f9;
            --text-secondary:#94a3b8;--text-muted:#64748b;
            --radius-sm:6px;--radius-md:10px;--radius-lg:14px;--radius-xl:24px;
            --shadow-lg:0 20px 60px rgba(0,0,0,0.5);
            --tr:all 0.2s cubic-bezier(0.4,0,0.2,1);
        }
        html,body{font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;-webkit-font-smoothing:antialiased;background:var(--bg-dark);color:var(--text-primary);}

        /* ── Navbar ─────────────────────────────────── */
        .site-nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(11,17,32,.85);backdrop-filter:blur(16px);border-bottom:1px solid var(--border);padding:0 40px;height:64px;display:flex;align-items:center;gap:32px;}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
        .brand-logo{width:36px;height:36px;background:linear-gradient(135deg,var(--blue-600),var(--blue-400));border-radius:10px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 10px rgba(37,99,235,.4);}
        .brand-logo i{color:#fff;font-size:18px;}
        .brand-n{font-size:17px;font-weight:800;color:var(--text-primary);letter-spacing:-.5px;}
        .brand-n span{color:var(--blue-400);}
        .nav-menu{margin-left:auto;display:flex;align-items:center;gap:4px;}
        .nav-btn{padding:7px 16px;border:1px solid transparent;border-radius:var(--radius-md);color:var(--text-secondary);text-decoration:none;font-size:13px;font-weight:500;transition:var(--tr);}
        .nav-btn:hover{background:var(--bg-elevated);border-color:var(--border);color:var(--text-primary);}
        .nav-btn.cta{background:linear-gradient(135deg,var(--blue-600),var(--blue-500));border:none;color:white;box-shadow:0 2px 8px rgba(37,99,235,.35);}
        .nav-btn.cta:hover{background:linear-gradient(135deg,var(--blue-700),var(--blue-600));transform:translateY(-1px);box-shadow:0 4px 14px rgba(37,99,235,.45);color:white;}

        /* ── Hero ────────────────────────────────────── */
        .hero{min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:120px 24px 80px;position:relative;overflow:hidden;}
        .hero-bg{position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% 0%,rgba(37,99,235,.12) 0%,transparent 70%);pointer-events:none;}
        .hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(37,99,235,.1);border:1px solid rgba(37,99,235,.25);border-radius:20px;padding:5px 16px;font-size:12px;font-weight:600;color:var(--blue-400);margin-bottom:28px;text-transform:uppercase;letter-spacing:.8px;}
        .hero h1{font-size:clamp(44px,6vw,80px);font-weight:900;line-height:1.05;letter-spacing:-3px;color:var(--text-primary);margin-bottom:24px;}
        .hero h1 span{color:var(--blue-400);}
        .hero-desc{font-size:18px;color:var(--text-secondary);line-height:1.7;max-width:560px;margin:0 auto 40px;}
        .hero-cta{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;}
        .btn-hero-primary{padding:14px 32px;background:linear-gradient(135deg,var(--blue-600),var(--blue-500));border:none;border-radius:var(--radius-md);color:white;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:var(--tr);text-decoration:none;box-shadow:0 4px 20px rgba(37,99,235,.4);display:inline-flex;align-items:center;gap:8px;}
        .btn-hero-primary:hover{background:linear-gradient(135deg,var(--blue-700),var(--blue-600));transform:translateY(-2px);box-shadow:0 8px 28px rgba(37,99,235,.5);color:white;}
        .btn-hero-ghost{padding:14px 32px;background:transparent;border:1px solid var(--border);border-radius:var(--radius-md);color:var(--text-secondary);font-size:15px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:var(--tr);text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
        .btn-hero-ghost:hover{background:var(--bg-surface);border-color:rgba(255,255,255,.2);color:var(--text-primary);}
        .hero-stats{display:flex;align-items:center;justify-content:center;gap:48px;margin-top:64px;flex-wrap:wrap;}
        .stat-item{text-align:center;}
        .stat-num{font-size:32px;font-weight:800;color:var(--text-primary);letter-spacing:-1px;}
        .stat-label{font-size:13px;color:var(--text-muted);font-weight:500;margin-top:4px;}

        /* ── Services ────────────────────────────────── */
        .section{padding:96px 24px;}
        .section-label{display:inline-block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:var(--blue-400);margin-bottom:16px;}
        .section-title{font-size:clamp(32px,4vw,48px);font-weight:800;letter-spacing:-1.5px;color:var(--text-primary);margin-bottom:16px;line-height:1.1;}
        .section-desc{font-size:16px;color:var(--text-secondary);max-width:560px;line-height:1.7;}
        .services-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-top:48px;}
        .service-card{background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px;transition:var(--tr);cursor:pointer;}
        .service-card:hover{border-color:rgba(37,99,235,.35);transform:translateY(-4px);box-shadow:0 12px 40px rgba(0,0,0,0.4);}
        .service-icon{width:52px;height:52px;border-radius:var(--radius-md);background:rgba(37,99,235,.12);display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--blue-400);margin-bottom:20px;}
        .service-icon.green{background:rgba(16,185,129,.12);color:var(--success);}
        .service-icon.amber{background:rgba(245,158,11,.12);color:var(--warning);}
        .service-icon.cyan{background:rgba(6,182,212,.12);color:var(--info);}
        .service-icon.red{background:rgba(239,68,68,.12);color:var(--danger);}
        .service-card h3{font-size:17px;font-weight:700;color:var(--text-primary);margin-bottom:10px;letter-spacing:-.3px;}
        .service-card p{font-size:13.5px;color:var(--text-secondary);line-height:1.6;}

        /* ── Why Us ──────────────────────────────────── */
        .why-section{background:var(--bg-surface);border-top:1px solid var(--border);border-bottom:1px solid var(--border);}
        .why-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:32px;margin-top:48px;}
        .why-item{display:flex;flex-direction:column;gap:12px;}
        .why-num{font-size:40px;font-weight:900;color:var(--blue-600);letter-spacing:-2px;line-height:1;}
        .why-item h4{font-size:16px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;}
        .why-item p{font-size:13.5px;color:var(--text-secondary);line-height:1.6;}

        /* ── CTA Banner ──────────────────────────────── */
        .cta-section{background:linear-gradient(135deg,var(--blue-700) 0%,var(--blue-600) 50%,rgba(37,99,235,.8) 100%);border-radius:var(--radius-xl);padding:72px 48px;text-align:center;margin:0 24px;position:relative;overflow:hidden;}
        .cta-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 50% 0%,rgba(255,255,255,.08) 0%,transparent 70%);pointer-events:none;}
        .cta-section h2{font-size:clamp(28px,4vw,44px);font-weight:800;color:white;letter-spacing:-1px;margin-bottom:16px;}
        .cta-section p{font-size:17px;color:rgba(255,255,255,.75);margin-bottom:36px;max-width:480px;margin-left:auto;margin-right:auto;}

        /* ── Footer ──────────────────────────────────── */
        .site-footer{border-top:1px solid var(--border);padding:40px 40px 32px;margin-top:96px;}
        .footer-inner{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;}
        .footer-links{display:flex;gap:24px;flex-wrap:wrap;}
        .footer-links a{color:var(--text-muted);text-decoration:none;font-size:13px;transition:var(--tr);}
        .footer-links a:hover{color:var(--text-secondary);}
        .footer-copy{font-size:13px;color:var(--text-muted);}

        /* ── Divider ─────────────────────────────────── */
        .divider-line{height:1px;background:var(--border);}

        @media(max-width:768px){
            .site-nav{padding:0 16px;}
            .nav-menu{gap:2px;}
            .nav-btn{padding:6px 12px;font-size:12px;}
            .hero{padding:100px 16px 60px;}
            .section{padding:64px 16px;}
            .cta-section{margin:0 16px;padding:48px 24px;}
            .hero-stats{gap:28px;}
            .stat-num{font-size:26px;}
            .site-footer{padding:32px 16px;}
            .footer-inner{flex-direction:column;text-align:center;}
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="site-nav">
        <a class="nav-brand" href="/">
            <div class="brand-logo"><i class="bi bi-snow"></i></div>
            <div class="brand-n">JASA<span>KU</span></div>
        </a>
        <div class="nav-menu">
            @if(Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="nav-btn">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="nav-btn">Masuk</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="nav-btn cta">Daftar Gratis</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div style="position:relative;z-index:1;max-width:800px;">
            <div class="hero-badge"><i class="bi bi-tools"></i> Layanan AC & Gadget Profesional</div>
            <h1>Rumah Nyaman,<br><span>Gadget Lancar</span></h1>
            <p class="hero-desc">Servis AC dan gadget oleh teknisi berpengalaman. Dari perawatan AC hingga perbaikan smartphone dan laptop, dengan harga transparan dan garansi layanan.</p>
            <div class="hero-cta">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-hero-primary">
                        <i class="bi bi-speedometer2"></i> Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-hero-primary">
                        <i class="bi bi-calendar-check"></i> Pesan Sekarang
                    </a>
                    <a href="{{ route('register') }}" class="btn-hero-ghost">
                        <i class="bi bi-person-plus"></i> Daftar Gratis
                    </a>
                @endauth
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-num">5.000+</div>
                    <div class="stat-label">Pelanggan Puas</div>
                </div>
                <div class="stat-item" style="border-left:1px solid var(--border);padding-left:48px;">
                    <div class="stat-num">200+</div>
                    <div class="stat-label">Teknisi Aktif</div>
                </div>
                <div class="stat-item" style="border-left:1px solid var(--border);padding-left:48px;">
                    <div class="stat-num">98%</div>
                    <div class="stat-label">Tingkat Kepuasan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="section">
        <div style="max-width:1100px;margin:0 auto;">
            <div style="text-align:center;">
                <span class="section-label">Layanan Kami</span>
                <h2 class="section-title">Solusi AC & Gadget<br>Anda di Satu Tempat</h2>
                <p class="section-desc" style="margin:0 auto;">Dari perawatan AC hingga perbaikan smartphone dan laptop, kami siap membantu dengan teknisi profesional.</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon"><i class="bi bi-tools"></i></div>
                    <h3>Servis & Perawatan</h3>
                    <p>Perawatan rutin AC untuk menjaga performa optimal dan memperpanjang usia unit AC Anda.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon green"><i class="bi bi-wrench-adjustable"></i></div>
                    <h3>Perbaikan Kerusakan</h3>
                    <p>Diagnosa cepat dan penanganan berbagai jenis kerusakan AC oleh teknisi berpengalaman.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon cyan"><i class="bi bi-snow2"></i></div>
                    <h3>Isi Freon</h3>
                    <p>Pengisian freon dengan freon berkualitas tinggi untuk mengembalikan dinginnya AC Anda.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon amber"><i class="bi bi-house-gear"></i></div>
                    <h3>Instalasi AC</h3>
                    <p>Pemasangan AC baru dengan instalasi yang rapi, aman, dan sesuai standar teknis.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon red"><i class="bi bi-droplet"></i></div>
                    <h3>Cuci AC</h3>
                    <p>Pembersihan menyeluruh filter dan unit AC untuk udara yang lebih bersih dan segar.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon cyan"><i class="bi bi-phone"></i></div>
                    <h3>Servis Gadget</h3>
                    <p>Perbaikan layar, baterai, dan kerusakan lain pada smartphone, tablet, dan laptop oleh teknisi berpengalaman.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="bi bi-clock-history"></i></div>
                    <h3>Darurat 24/7</h3>
                    <p>Layanan darurat tersedia 24 jam sehari, 7 hari seminggu untuk kebutuhan mendesak Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="divider-line"></div>

    <!-- Why Us -->
    <section class="section why-section">
        <div style="max-width:1100px;margin:0 auto;">
            <span class="section-label">Mengapa Kami</span>
            <h2 class="section-title" style="max-width:500px;">Kepercayaan Anda,<br>Prioritas Utama Kami</h2>
            <div class="why-grid">
                <div class="why-item">
                    <div class="why-num">01</div>
                    <h4>Teknisi Tersertifikasi</h4>
                    <p>Semua teknisi kami telah melalui pelatihan ketat dan memiliki sertifikasi resmi dari lembaga terpercaya.</p>
                </div>
                <div class="why-item">
                    <div class="why-num">02</div>
                    <h4>Harga Transparan</h4>
                    <p>Estimasi harga diberikan sebelum pekerjaan dimulai. Tidak ada biaya tersembunyi atau kejutan tagihan.</p>
                </div>
                <div class="why-item">
                    <div class="why-num">03</div>
                    <h4>Respons Cepat</h4>
                    <p>Teknisi kami siap hadir dalam waktu 1-2 jam setelah pemesanan dikonfirmasi di area layanan kami.</p>
                </div>
                <div class="why-item">
                    <div class="why-num">04</div>
                    <h4>Bergaransi</h4>
                    <p>Setiap pekerjaan kami disertai garansi layanan sehingga Anda tidak perlu khawatir jika ada masalah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Banner -->
    <div style="padding:96px 0;">
        <div style="max-width:1100px;margin:0 auto;padding:0 24px;">
            <div class="cta-section">
                <h2>AC Nyaman, Gadget Kembali Lancar</h2>
                <p>Pesan layanan AC atau gadget sekarang. Teknisi kami siap membantu dengan layanan profesional dan harga transparan.</p>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-hero-primary" style="display:inline-flex;">
                        <i class="bi bi-speedometer2"></i> Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-hero-primary" style="display:inline-flex;">
                        <i class="bi bi-calendar-check"></i> Pesan Sekarang
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div style="max-width:1100px;margin:0 auto;">
            <div class="footer-inner">
                <a class="nav-brand" href="/">
                    <div class="brand-logo"><i class="bi bi-snow"></i></div>
                    <div class="brand-n">JASA<span>KU</span></div>
                </a>
                <div class="footer-links">
                    <a href="#">Tentang Kami</a>
                    <a href="#">Layanan</a>
                    <a href="#">Kebijakan Privasi</a>
                    <a href="#">Syarat & Ketentuan</a>
                    <a href="#">Hubungi Kami</a>
                </div>
                <div class="footer-copy">&copy; {{ date('Y') }} JASAKU. Semua hak dilindungi.</div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

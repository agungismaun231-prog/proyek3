<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - JASAKU</title>
    <script>
        (function(){
            const saved=localStorage.getItem('jasaku_theme')||'dark';
            document.documentElement.setAttribute('data-bs-theme',saved);
            if(saved==='light'){
                document.documentElement.classList.add('theme-light');
            }
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>

        /* ── Design Tokens ─────────────────────────────── */
        :root{
            --blue-50:#eff6ff;--blue-100:#dbeafe;--blue-300:#93c5fd;
            --blue-400:#60a5fa;--blue-500:#3b82f6;--blue-600:#2563eb;
            --blue-700:#1d4ed8;--success:#10b981;--warning:#f59e0b;
            --danger:#ef4444;--info:#06b6d4;
            /* Dark theme */
            --bg-base:#0b1120;--bg-surface:#111827;--bg-elevated:#1a2234;
            --bg-card:rgba(26,34,52,0.9);--border:rgba(255,255,255,0.08);
            --text-primary:#f1f5f9;--text-secondary:#94a3b8;--text-muted:#64748b;
            --sidebar-w:260px;--navbar-h:60px;
            --radius-sm:6px;--radius-md:10px;--radius-lg:14px;
            --shadow-sm:0 1px 4px rgba(0,0,0,0.3);
            --shadow-md:0 4px 20px rgba(0,0,0,0.4);
            --shadow-lg:0 8px 40px rgba(0,0,0,0.5);
            --tr:all 0.2s cubic-bezier(0.4,0,0.2,1);
        }
        html.theme-light, body.theme-light{
            --bg-base:#f1f5f9;--bg-surface:#ffffff;--bg-elevated:#f8fafc;
            --bg-card:#ffffff;--border:#e2e8f0;
            --text-primary:#0f172a;--text-secondary:#475569;--text-muted:#94a3b8;
            --shadow-sm:0 1px 4px rgba(0,0,0,0.08);
            --shadow-md:0 4px 20px rgba(0,0,0,0.1);
            --shadow-lg:0 8px 40px rgba(0,0,0,0.12);
        }
        html,body{height:100%}
        body{font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;font-size:14px;
            background:var(--bg-base);color:var(--text-primary);line-height:1.6;
            -webkit-font-smoothing:antialiased;}

        /* ── Sidebar ──────────────────────────────────── */
        .sidebar{position:fixed;left:0;top:0;width:var(--sidebar-w);height:100vh;
            background:var(--bg-surface);border-right:1px solid var(--border);
            display:flex;flex-direction:column;z-index:1050;overflow:hidden;transition:var(--tr);}
        .sidebar-brand{display:flex;align-items:center;gap:10px;padding:0 16px;
            height:var(--navbar-h);border-bottom:1px solid var(--border);flex-shrink:0;}
        .brand-logo{width:34px;height:34px;background:linear-gradient(135deg,var(--blue-600),var(--blue-400));
            border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;
            flex-shrink:0;box-shadow:0 2px 10px rgba(37,99,235,0.35);}
        .brand-logo i{color:#fff;font-size:18px;}
        .brand-name{font-size:16px;font-weight:800;color:var(--text-primary);letter-spacing:-0.5px;}
        .brand-name span{color:var(--blue-400);}
        .theme-btn{margin-left:auto;width:30px;height:30px;border:1px solid var(--border);
            border-radius:var(--radius-sm);background:transparent;color:var(--text-secondary);
            cursor:pointer;display:flex;align-items:center;justify-content:center;
            transition:var(--tr);font-size:13px;flex-shrink:0;}
        .theme-btn:hover{background:var(--bg-elevated);color:var(--text-primary);}
        .sidebar-nav{flex:1;overflow-y:auto;padding:10px 0 24px;}
        .sidebar-nav::-webkit-scrollbar{width:3px;}
        .sidebar-nav::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px;}
        .nav-label{padding:14px 20px 5px;font-size:10px;font-weight:700;
            color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;}
        .s-link{display:flex;align-items:center;gap:10px;padding:9px 16px 9px 18px;
            margin:1px 8px;border-radius:var(--radius-md);color:var(--text-secondary);
            text-decoration:none;font-size:13.5px;font-weight:500;transition:var(--tr);
            border:1px solid transparent;position:relative;}
        .s-link i{font-size:15px;width:18px;text-align:center;flex-shrink:0;}
        .s-link:hover{background:var(--bg-elevated);color:var(--text-primary);border-color:var(--border);}
        .s-link.active{background:linear-gradient(135deg,rgba(37,99,235,.15),rgba(96,165,250,.07));
            color:var(--blue-400);border-color:rgba(37,99,235,.2);font-weight:600;}
        .s-link.active i{color:var(--blue-400);}
        .s-link.active::before{content:'';position:absolute;left:-8px;top:50%;
            transform:translateY(-50%);width:3px;height:18px;
            background:var(--blue-500);border-radius:0 3px 3px 0;}
        .s-link .badge{margin-left:auto;font-size:10px;padding:2px 7px;border-radius:20px;}

        /* ── Topbar ───────────────────────────────────── */
        .topbar{position:fixed;top:0;left:var(--sidebar-w);right:0;height:var(--navbar-h);
            background:var(--bg-surface);border-bottom:1px solid var(--border);
            display:flex;align-items:center;padding:0 24px;z-index:1040;gap:16px;}
        .topbar-title{flex:1;font-size:15px;font-weight:600;color:var(--text-primary);}
        .topbar-actions{display:flex;align-items:center;gap:8px;}
        .notif-btn{position:relative;width:36px;height:36px;border:1px solid var(--border);
            border-radius:var(--radius-md);background:transparent;color:var(--text-secondary);
            cursor:pointer;display:flex;align-items:center;justify-content:center;
            transition:var(--tr);font-size:16px;text-decoration:none;}
        .notif-btn:hover{background:var(--bg-elevated);color:var(--text-primary);}
        .notif-dot{position:absolute;top:6px;right:6px;width:7px;height:7px;
            background:var(--danger);border:2px solid var(--bg-surface);border-radius:50%;}
        .user-dd .dropdown-toggle{display:flex;align-items:center;gap:8px;padding:5px 10px;
            border:1px solid var(--border);border-radius:var(--radius-md);background:transparent;
            color:var(--text-primary);cursor:pointer;transition:var(--tr);
            font-size:13px;font-weight:500;text-decoration:none;}
        .user-dd .dropdown-toggle:hover{background:var(--bg-elevated);}
        .user-dd .dropdown-toggle::after{display:none;}
        .u-avatar{width:28px;height:28px;background:linear-gradient(135deg,var(--blue-600),var(--blue-400));
            border-radius:50%;display:flex;align-items:center;justify-content:center;
            font-size:11px;font-weight:700;color:white;flex-shrink:0;}

        /* ── Page Wrap ────────────────────────────────── */
        .page-wrap{margin-left:var(--sidebar-w);margin-top:var(--navbar-h);
            min-height:calc(100vh - var(--navbar-h));padding:28px 28px 48px;}
        .page-title{display:flex;align-items:center;gap:12px;margin-bottom:28px;}
        .page-title-icon{width:40px;height:40px;
            background:linear-gradient(135deg,rgba(37,99,235,.15),rgba(96,165,250,.08));
            border:1px solid rgba(37,99,235,.2);border-radius:var(--radius-md);
            display:flex;align-items:center;justify-content:center;
            color:var(--blue-400);font-size:18px;flex-shrink:0;}
        .page-title h1{font-size:22px;font-weight:700;color:var(--text-primary);
            letter-spacing:-.5px;margin:0;}
        .page-title > i{font-size:26px;color:var(--blue-400);}

        /* ── Cards & Card Footer ──────────────────────── */
        .card{background:var(--bg-card);border:1px solid var(--border);
            border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);margin-bottom:20px;
            transition:var(--tr);overflow:hidden;}
        .card:hover{box-shadow:var(--shadow-md);}
        .card-header{background:transparent;border-bottom:1px solid var(--border);
            padding:16px 20px;font-weight:600;font-size:14px;color:var(--text-primary);
            display:flex;align-items:center;gap:8px;}
        .card-header i{color:var(--blue-400);}
        .card-header.bg-light{background:var(--bg-elevated)!important;border-bottom:1px solid var(--border)!important;color:var(--text-primary)!important;}
        .card-body{padding:20px;}
        .card-footer{background:transparent!important;border-top:1px solid var(--border)!important;
            padding:14px 20px;color:var(--text-secondary);}

        /* ── Stats Card ───────────────────────────────── */
        .stats-card{background:var(--bg-card);border:1px solid var(--border);
            border-radius:var(--radius-lg);padding:20px;margin-bottom:20px;
            box-shadow:var(--shadow-sm);transition:var(--tr);position:relative;overflow:hidden;}
        .stats-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md);}
        .stats-card h5,.stats-card h6{font-size:11px;font-weight:700;
            text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);margin-bottom:10px;}
        .stats-card .number{font-size:26px;font-weight:800;letter-spacing:-1px;
            color:var(--text-primary);}
        .stats-card.success .number{color:var(--success);}

        /* ── Tables (Dark & Light Theme Overrides) ─────── */
        .table{
            --bs-table-bg:transparent!important;
            --bs-table-accent-bg:transparent!important;
            --bs-table-striped-bg:rgba(255,255,255,0.02)!important;
            --bs-table-striped-color:var(--text-primary)!important;
            --bs-table-active-bg:var(--bg-elevated)!important;
            --bs-table-active-color:var(--text-primary)!important;
            --bs-table-hover-bg:var(--bg-elevated)!important;
            --bs-table-hover-color:var(--text-primary)!important;
            --bs-table-color:var(--text-primary)!important;
            --bs-table-border-color:var(--border)!important;
            color:var(--text-primary)!important;
            margin-bottom:0;
            border-color:var(--border)!important;
        }
        .table > :not(caption) > * > *{
            background-color:transparent!important;
            color:var(--text-secondary);
            border-bottom:1px solid var(--border)!important;
            padding:13px 16px;
            vertical-align:middle;
            font-size:13.5px;
        }
        .table thead th{
            background-color:var(--bg-elevated)!important;
            color:var(--text-muted)!important;
            font-size:11px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.8px;
            border-bottom:1px solid var(--border)!important;
            border-top:none!important;
            white-space:nowrap;
        }
        .table tbody tr{
            background-color:transparent!important;
            transition:var(--tr);
        }
        .table tbody tr:last-child td{
            border-bottom:none!important;
        }
        .table tbody tr:hover > *{
            background-color:var(--bg-elevated)!important;
            color:var(--text-primary)!important;
        }
        .table tbody tr strong{
            color:var(--text-primary)!important;
        }
        .table-light,
        .table thead.table-light,
        .table thead.table-light th,
        .table-light th,
        .table-light td{
            --bs-table-bg:var(--bg-elevated)!important;
            --bs-table-color:var(--text-muted)!important;
            background-color:var(--bg-elevated)!important;
            color:var(--text-muted)!important;
            border-color:var(--border)!important;
        }
        .table-responsive{border-radius:var(--radius-md);overflow:hidden;}

        /* ── Modals ───────────────────────────────────── */
        .modal-content{background-color:var(--bg-surface)!important;color:var(--text-primary)!important;
            border:1px solid var(--border)!important;border-radius:var(--radius-lg)!important;
            box-shadow:var(--shadow-lg)!important;}
        .modal-header{border-bottom:1px solid var(--border)!important;color:var(--text-primary)!important;padding:16px 20px;}
        .modal-footer{border-top:1px solid var(--border)!important;padding:16px 20px;}
        .modal-title{color:var(--text-primary)!important;font-weight:700;font-size:16px;}

        /* ── Pagination ───────────────────────────────── */
        .pagination{margin-bottom:0;gap:4px;}
        .page-item .page-link{background-color:var(--bg-elevated)!important;border:1px solid var(--border)!important;
            color:var(--text-secondary)!important;border-radius:var(--radius-sm)!important;
            padding:6px 12px;font-size:13px;transition:var(--tr);}
        .page-item .page-link:hover{background-color:var(--bg-surface)!important;color:var(--text-primary)!important;
            border-color:var(--blue-500)!important;}
        .page-item.active .page-link{background:linear-gradient(135deg,var(--blue-600),var(--blue-500))!important;
            border-color:var(--blue-500)!important;color:#ffffff!important;}
        .page-item.disabled .page-link{background-color:transparent!important;border-color:var(--border)!important;
            color:var(--text-muted)!important;opacity:0.5;}

        /* ── Dropdowns ────────────────────────────────── */
        .dropdown-menu{background:var(--bg-elevated)!important;border:1px solid var(--border)!important;
            border-radius:var(--radius-lg)!important;box-shadow:var(--shadow-lg)!important;
            padding:6px!important;color:var(--text-primary)!important;min-width:200px;}
        .dropdown-item{color:var(--text-secondary)!important;border-radius:var(--radius-sm)!important;
            padding:8px 12px!important;font-size:13px;display:flex;align-items:center;gap:8px;transition:var(--tr);}
        .dropdown-item:hover,.dropdown-item:focus{background:var(--bg-surface)!important;color:var(--text-primary)!important;}
        .dropdown-item.logout-item{color:var(--danger)!important;}
        .dropdown-item.logout-item:hover{background:rgba(239,68,68,.08)!important;}
        .dropdown-divider{border-color:var(--border)!important;margin:4px 0!important;}
        .dropdown-header{color:var(--text-muted)!important;font-size:11px;padding:4px 12px 8px!important;}

        /* ── List Groups & Containers ─────────────────── */
        .list-group-item{background-color:var(--bg-surface)!important;color:var(--text-primary)!important;
            border-color:var(--border)!important;}
        .input-group-text{background-color:var(--bg-elevated)!important;color:var(--text-secondary)!important;
            border-color:var(--border)!important;}
        .bg-light{background-color:var(--bg-elevated)!important;color:var(--text-primary)!important;
            border:1px solid var(--border)!important;}
        .badge.bg-light{background-color:var(--bg-elevated)!important;color:var(--text-secondary)!important;
            border:1px solid var(--border)!important;}
        .card.bg-light{background:var(--bg-card)!important;border-color:var(--border)!important;}
        .border-light{border-color:var(--border)!important;}
        /* Checkout & Payment custom views support in dark theme */
        .checkout-header, .checkout-section, .summary-card, .status-section, .payment-list, .empty-state{
            background:var(--bg-card)!important;border-color:var(--border)!important;color:var(--text-primary)!important;
        }
        .checkout-title, .section-title{color:var(--text-primary)!important;}
        .payment-option, .bank-item, .info-item{
            background:var(--bg-elevated)!important;border-color:var(--border)!important;color:var(--text-primary)!important;
        }
        .payment-option:hover, .bank-item:hover{background:var(--bg-surface)!important;}
        .summary-item{border-color:var(--border)!important;}

        /* ── Status Badges ────────────────────────────── */
        .badge{font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;letter-spacing:.2px;}
        .badge.pending{background:rgba(245,158,11,.15)!important;color:var(--warning)!important;border:1px solid rgba(245,158,11,.3);}
        .badge.confirmed{background:rgba(6,182,212,.15)!important;color:var(--info)!important;border:1px solid rgba(6,182,212,.3);}
        .badge.in_progress{background:rgba(59,130,246,.15)!important;color:var(--blue-400)!important;border:1px solid rgba(59,130,246,.3);}
        .badge.completed{background:rgba(16,185,129,.15)!important;color:var(--success)!important;border:1px solid rgba(16,185,129,.3);}
        .badge.cancelled{background:rgba(239,68,68,.15)!important;color:var(--danger)!important;border:1px solid rgba(239,68,68,.3);}
        .bg-success{background:rgba(16,185,129,.15)!important;color:var(--success)!important;border:1px solid rgba(16,185,129,.3);}
        .bg-danger{background:rgba(239,68,68,.15)!important;color:var(--danger)!important;border:1px solid rgba(239,68,68,.3);}
        .bg-warning{background:rgba(245,158,11,.15)!important;color:var(--warning)!important;border:1px solid rgba(245,158,11,.3);}
        .bg-info{background:rgba(6,182,212,.15)!important;color:var(--info)!important;border:1px solid rgba(6,182,212,.3);}
        .bg-primary{background:rgba(37,99,235,.15)!important;color:var(--blue-400)!important;border:1px solid rgba(37,99,235,.3);}
        .bg-secondary{background:var(--bg-elevated)!important;color:var(--text-secondary)!important;border:1px solid var(--border)!important;}
        .text-dark{color:inherit!important;}

        /* ── Buttons ──────────────────────────────────── */
        .btn{font-family:'Inter',sans-serif;font-weight:500;border-radius:var(--radius-sm);
            transition:var(--tr);font-size:13px;}
        .btn-primary{background:linear-gradient(135deg,var(--blue-600),var(--blue-500));
            border:none;color:white;box-shadow:0 2px 8px rgba(37,99,235,.3);}
        .btn-primary:hover{background:linear-gradient(135deg,var(--blue-700),var(--blue-600));
            transform:translateY(-1px);box-shadow:0 4px 14px rgba(37,99,235,.4);color:white;}
        .btn-outline-primary{border:1px solid rgba(37,99,235,.4);color:var(--blue-400);background:transparent;}
        .btn-outline-primary:hover{background:rgba(37,99,235,.1);border-color:var(--blue-500);color:var(--blue-400);}
        .btn-outline-warning{border:1px solid rgba(245,158,11,.4);color:var(--warning);background:transparent;}
        .btn-outline-warning:hover{background:rgba(245,158,11,.15);border-color:var(--warning);color:var(--warning);}
        .btn-outline-info{border:1px solid rgba(6,182,212,.4);color:var(--info);background:transparent;}
        .btn-outline-info:hover{background:rgba(6,182,212,.15);border-color:var(--info);color:var(--info);}
        .btn-outline-secondary{border:1px solid var(--border);color:var(--text-secondary);background:transparent;}
        .btn-outline-secondary:hover{background:var(--bg-elevated);color:var(--text-primary);border-color:var(--text-muted);}
        .btn-secondary{background:var(--bg-elevated);border:1px solid var(--border);color:var(--text-primary);}
        .btn-secondary:hover{background:var(--bg-surface);color:var(--text-primary);}
        .btn-danger{background:rgba(239,68,68,.9);border:none;color:white;}
        .btn-danger:hover{background:var(--danger);color:white;}
        .btn-outline-danger{border:1px solid rgba(239,68,68,.4);color:var(--danger);background:transparent;}
        .btn-outline-danger:hover{background:rgba(239,68,68,.1);color:var(--danger);}
        .btn-success{background:rgba(16,185,129,.9);border:none;color:white;}
        .btn-sm{padding:5px 12px;font-size:12px;}
        .btn-light{background:var(--bg-elevated)!important;border:1px solid var(--border)!important;color:var(--text-primary)!important;}
        .btn-light:hover{background:var(--bg-surface)!important;color:var(--text-primary)!important;border-color:var(--text-muted)!important;}

        /* ── Nav Tabs ─────────────────────────────────── */
        .nav-tabs{border-bottom-color:var(--border)!important;}
        .nav-tabs .nav-link{color:var(--text-secondary)!important;border:1px solid transparent!important;
            border-radius:var(--radius-sm) var(--radius-sm) 0 0!important;padding:8px 16px;transition:var(--tr);}
        .nav-tabs .nav-link:hover{color:var(--text-primary)!important;border-color:var(--border) var(--border) transparent!important;
            background:var(--bg-elevated)!important;}
        .nav-tabs .nav-link.active{color:var(--blue-400)!important;background:var(--bg-surface)!important;
            border-color:var(--border) var(--border) transparent!important;font-weight:600;}

        /* ── Forms ────────────────────────────────────── */
        .form-label{font-size:12px;font-weight:600;color:var(--text-secondary);
            margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;}
        .form-control,.form-select{background:var(--bg-elevated);border:1px solid var(--border);
            color:var(--text-primary);border-radius:var(--radius-sm);padding:9px 14px;
            font-size:13.5px;font-family:'Inter',sans-serif;transition:var(--tr);}
        .form-control::placeholder{color:var(--text-muted);}
        .form-control:focus,.form-select:focus{outline:none;border-color:var(--blue-500);
            box-shadow:0 0 0 3px rgba(37,99,235,.15);background:var(--bg-elevated);color:var(--text-primary);}
        .form-check-input{background-color:var(--bg-elevated);border-color:var(--border);}
        .form-check-input:checked{background-color:var(--blue-600);border-color:var(--blue-600);}
        select option{background:var(--bg-surface);color:var(--text-primary);}
        body.theme-light select option{background:#ffffff;color:#0f172a;}
        body.theme-light .form-control,
        body.theme-light .form-select{background:#f8fafc;color:#0f172a;}
        body.theme-light .form-control::placeholder{color:#94a3b8;}

        /* ── Alerts ───────────────────────────────────── */
        .alert{border-radius:var(--radius-md);border:1px solid;font-size:13.5px;padding:12px 16px;}
        .alert-success{background:rgba(16,185,129,.1);border-color:rgba(16,185,129,.25);color:var(--success);}
        .alert-danger{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.25);color:#fca5a5;}
        .alert-warning{background:rgba(245,158,11,.1);border-color:rgba(245,158,11,.25);color:var(--warning);}
        body.theme-light .alert-danger{color:#991b1b;}
        body.theme-light .alert-success{color:#065f46;}
        .btn-close{filter:invert(1) opacity(.7);}
        body.theme-light .btn-close{filter:none;}

        /* ── Light theme overrides ────────────────────── */
        body.theme-light .table{
            --bs-table-bg:transparent!important;
            --bs-table-color:#0f172a!important;
            --bs-table-hover-bg:#f8fafc!important;
            --bs-table-hover-color:#0f172a!important;
            --bs-table-striped-bg:rgba(0,0,0,0.02)!important;
            --bs-table-striped-color:#0f172a!important;
            --bs-table-border-color:#e2e8f0!important;
        }
        body.theme-light .table > :not(caption) > * > *{color:#475569;border-bottom-color:#e2e8f0!important;}
        body.theme-light .table thead th{background-color:#f8fafc!important;color:#64748b!important;border-bottom-color:#e2e8f0!important;}
        body.theme-light .table tbody tr:hover > *{background-color:#f1f5f9!important;color:#0f172a!important;}
        body.theme-light .table tbody tr strong{color:#0f172a!important;}
        body.theme-light .table-light,
        body.theme-light .table thead.table-light th,
        body.theme-light .table-light th,
        body.theme-light .table-light td{background-color:#f8fafc!important;color:#64748b!important;border-color:#e2e8f0!important;}
        body.theme-light .card-footer{border-top-color:#e2e8f0!important;}
        body.theme-light .modal-content{background-color:#ffffff!important;color:#0f172a!important;border-color:#e2e8f0!important;}
        body.theme-light .page-item .page-link{background-color:#ffffff!important;border-color:#e2e8f0!important;color:#475569!important;}
        body.theme-light .page-item .page-link:hover{background-color:#f1f5f9!important;color:#0f172a!important;}
        body.theme-light .page-item.active .page-link{color:#ffffff!important;}
        body.theme-light .dropdown-menu{background:#ffffff!important;border-color:#e2e8f0!important;}
        body.theme-light .dropdown-item{color:#475569!important;}
        body.theme-light .dropdown-item:hover{background:#f1f5f9!important;color:#0f172a!important;}
        body.theme-light .list-group-item{background-color:#ffffff!important;color:#0f172a!important;border-color:#e2e8f0!important;}
        body.theme-light .input-group-text{background-color:#f8fafc!important;color:#475569!important;border-color:#e2e8f0!important;}
        body.theme-light .bg-light{background-color:#f8fafc!important;color:#0f172a!important;border-color:#e2e8f0!important;}
        body.theme-light .badge.bg-light{background-color:#f1f5f9!important;color:#475569!important;border-color:#e2e8f0!important;}
        body.theme-light .card.bg-light{background:#ffffff!important;border-color:#e2e8f0!important;}
        body.theme-light .border-light{border-color:#e2e8f0!important;}
        body.theme-light .btn-light{background:#f1f5f9!important;border-color:#e2e8f0!important;color:#0f172a!important;}
        body.theme-light .btn-light:hover{background:#e2e8f0!important;color:#0f172a!important;}
        body.theme-light .checkout-header, body.theme-light .checkout-section, body.theme-light .summary-card,
        body.theme-light .status-section, body.theme-light .payment-list, body.theme-light .empty-state{
            background:#ffffff!important;border-color:#e2e8f0!important;color:#0f172a!important;
        }
        body.theme-light .checkout-title, body.theme-light .section-title{color:#0f172a!important;}
        body.theme-light .payment-option, body.theme-light .bank-item{
            background:#f8fafc!important;border-color:#e2e8f0!important;color:#0f172a!important;
        }
        body.theme-light .payment-option:hover, body.theme-light .bank-item:hover{background:#f1f5f9!important;}


        /* ── Mobile ───────────────────────────────────── */
        .menu-toggle{display:none;width:36px;height:36px;border:1px solid var(--border);
            border-radius:var(--radius-md);background:transparent;color:var(--text-secondary);
            cursor:pointer;align-items:center;justify-content:center;font-size:18px;transition:var(--tr);}
        .menu-toggle:hover{background:var(--bg-elevated);color:var(--text-primary);}
        .s-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);
            z-index:1045;backdrop-filter:blur(2px);}
        @media(max-width:991px){
            .sidebar{transform:translateX(-100%);z-index:1060;}
            .sidebar.open{transform:translateX(0);}
            .s-overlay.open{display:block;}
            .topbar{left:0;}
            .page-wrap{margin-left:0;}
            .menu-toggle{display:flex;}
        }
        @media(max-width:767px){
            .page-wrap{padding:20px 16px 40px;}
            .page-title h1{font-size:18px;}
            .u-name{display:none;}
        }
    </style>
</head>
<body>
    <div class="s-overlay" id="sOverlay"></div>

    <!-- ── Sidebar ─────────────────────────────────────────── -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo"><i class="bi bi-snow"></i></div>
            <div class="brand-name">JASA<span>KU</span></div>
            <button class="theme-btn" id="themeToggle" title="Ganti Tema">
                <i class="bi bi-moon-stars"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('dashboard') }}"
                   class="s-link @if(request()->routeIs('dashboard')) active @endif">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <div class="nav-label">Data Master</div>
                <a href="{{ route('customers.index') }}"
                   class="s-link @if(request()->routeIs('customers.*')) active @endif">
                    <i class="bi bi-people"></i> Pelanggan
                </a>
                <a href="{{ route('technicians.index') }}"
                   class="s-link @if(request()->routeIs('technicians.*')) active @endif">
                    <i class="bi bi-tools"></i> Teknisi
                </a>
                <a href="{{ route('services.index') }}"
                   class="s-link @if(request()->routeIs('services.*')) active @endif">
                    <i class="bi bi-wrench-adjustable"></i> Layanan
                </a>
                <div class="nav-label">Transaksi & Keuangan</div>
                <a href="{{ route('bookings.index') }}"
                   class="s-link @if(request()->routeIs('bookings.*')) active @endif">
                    <i class="bi bi-calendar-check"></i> Pemesanan
                </a>
                <a href="{{ route('invoices.index') }}"
                   class="s-link @if(request()->routeIs('invoices.*')) active @endif">
                    <i class="bi bi-receipt"></i> Invoice
                </a>
                @php $pendingPaymentCount = \App\Models\Payment::where('status','pending_approval')->count(); @endphp
                <a href="{{ route('payments.pending') }}"
                   class="s-link @if(request()->routeIs('payments.pending')) active @endif">
                    <i class="bi bi-credit-card"></i> Monitoring Pembayaran
                    @if($pendingPaymentCount > 0)
                        <span class="badge bg-danger ms-auto">{{ $pendingPaymentCount }}</span>
                    @endif
                </a>
                <div class="nav-label">Laporan & Analitik</div>
                <a href="{{ route('reports.dashboard') }}"
                   class="s-link @if(request()->routeIs('reports.dashboard')) active @endif">
                    <i class="bi bi-graph-up-arrow"></i> Ringkasan Laporan
                </a>
                <a href="{{ route('reports.revenue') }}"
                   class="s-link @if(request()->routeIs('reports.revenue')) active @endif">
                    <i class="bi bi-cash-coin"></i> Pendapatan
                </a>
                <a href="{{ route('reports.bookings') }}"
                   class="s-link @if(request()->routeIs('reports.bookings')) active @endif">
                    <i class="bi bi-bar-chart-line"></i> Pemesanan
                </a>
                <a href="{{ route('reports.technicians') }}"
                   class="s-link @if(request()->routeIs('reports.technicians')) active @endif">
                    <i class="bi bi-person-check"></i> Performa Teknisi
                </a>
                <a href="{{ route('reports.customers') }}"
                   class="s-link @if(request()->routeIs('reports.customers')) active @endif">
                    <i class="bi bi-people-fill"></i> Analisis Pelanggan
                </a>
                <a href="{{ route('reports.payments') }}"
                   class="s-link @if(request()->routeIs('reports.payments')) active @endif">
                    <i class="bi bi-wallet2"></i> Laporan Pembayaran
                </a>
                <div class="nav-label">Konten</div>
                <a href="{{ route('sliders.index') }}"
                   class="s-link @if(request()->routeIs('sliders.*')) active @endif">
                    <i class="bi bi-images"></i> Slider Promosi
                </a>

            @elseif(auth()->user()->role === 'technician')
                <a href="{{ route('technician.dashboard') }}"
                   class="s-link @if(request()->routeIs('technician.dashboard')) active @endif">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <div class="nav-label">Pekerjaan Saya</div>
                <a href="{{ route('technician.bookings.index') }}"
                   class="s-link @if(request()->routeIs('technician.bookings.*')) active @endif">
                    <i class="bi bi-calendar-check"></i> Pesanan Masuk
                </a>
                <a href="{{ route('technician.tasks.index') }}"
                   class="s-link @if(request()->routeIs('technician.tasks.*')) active @endif">
                    <i class="bi bi-list-check"></i> Tugas Saya
                </a>
                <div class="nav-label">Akun Saya</div>
                <a href="{{ route('technician.profile.show') }}"
                   class="s-link @if(request()->routeIs('technician.profile.*')) active @endif">
                    <i class="bi bi-person-circle"></i> Profil
                </a>
                <a href="{{ route('technician.earnings.index') }}"
                   class="s-link @if(request()->routeIs('technician.earnings.*')) active @endif">
                    <i class="bi bi-wallet2"></i> Penghasilan
                </a>
                <a href="{{ route('technician.ratings.index') }}"
                   class="s-link @if(request()->routeIs('technician.ratings.*')) active @endif">
                    <i class="bi bi-star-half"></i> Rating & Review
                </a>

            @else
                <a href="{{ route('customer.dashboard') }}"
                   class="s-link @if(request()->routeIs('customer.dashboard')) active @endif">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <div class="nav-label">Transaksi Saya</div>
                <a href="{{ route('customer.bookings.index') }}"
                   class="s-link @if(request()->routeIs('customer.bookings.*')) active @endif">
                    <i class="bi bi-calendar-check"></i> Pemesanan Saya
                </a>
                <a href="#" class="s-link" data-bs-toggle="modal" data-bs-target="#modalInvoice">
                    <i class="bi bi-receipt"></i> Invoice Saya
                </a>
                <a href="#" class="s-link" data-bs-toggle="modal" data-bs-target="#modalLayanan">
                    <i class="bi bi-wrench-adjustable"></i> Layanan Tersedia
                </a>
            @endif
        </nav>
    </aside>

    <!-- ── Topbar ───────────────────────────────────────────── -->
    <header class="topbar">
        <button class="menu-toggle" id="menuToggle" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title">@yield('page-title', 'JASAKU')</div>
        <div class="topbar-actions">
            @if(auth()->user()->role === 'customer')
                @php
                    $customer = \App\Models\Customer::where('email', auth()->user()->email)->first();
                    $overdueCount = 0;
                    if ($customer) {
                        $overdueInvoices = \App\Models\Invoice::whereIn('booking_id', $customer->bookings()->pluck('id'))
                            ->where('status','!=','paid')->get()
                            ->filter(fn($inv) => $inv->status === 'overdue' || now()->isAfter($inv->due_date));
                        $overdueCount = $overdueInvoices->count();
                    }
                @endphp
                <a href="{{ route('customer.dashboard') }}" class="notif-btn" title="Notifikasi">
                    <i class="bi bi-bell"></i>
                    @if($overdueCount > 0)<span class="notif-dot"></span>@endif
                </a>
            @endif

            <div class="user-dd dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="u-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <span class="u-name">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->role === 'admin')
                        <span class="badge bg-danger" style="font-size:9px;padding:2px 6px;">Admin</span>
                    @elseif(auth()->user()->role === 'technician')
                        <span class="badge bg-warning" style="font-size:9px;padding:2px 6px;">Teknisi</span>
                    @else
                        <span class="badge bg-info" style="font-size:9px;padding:2px 6px;">Pelanggan</span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-header">{{ auth()->user()->email }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    @if(auth()->user()->role === 'customer')
                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    @elseif(auth()->user()->role === 'technician')
                        <li><a class="dropdown-item" href="{{ route('technician.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    @endif
                    <li><a class="dropdown-item" href="{{ route('profile.editProfile') }}"><i class="bi bi-person"></i> Edit Profil</a></li>
                    <li><a class="dropdown-item" href="{{ route('profile.editEmail') }}"><i class="bi bi-envelope"></i> Ubah Email</a></li>
                    <li><a class="dropdown-item" href="{{ route('profile.editPassword') }}"><i class="bi bi-key"></i> Ubah Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="dropdown-item logout-item"
                                    style="border:none;background:none;width:100%;cursor:pointer;text-align:left;">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- ── Content ──────────────────────────────────────────── -->
    <main class="page-wrap">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>Ada kesalahan:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function(){
        // Theme toggle
        const html=document.documentElement,body=document.body,btn=document.getElementById('themeToggle');
        const saved=localStorage.getItem('jasaku_theme')||'dark';
        if(saved==='light'){
            html.classList.add('theme-light');
            body.classList.add('theme-light');
            html.setAttribute('data-bs-theme','light');
        } else {
            html.classList.remove('theme-light');
            body.classList.remove('theme-light');
            html.setAttribute('data-bs-theme','dark');
        }
        function syncIcon(){if(!btn)return;const i=btn.querySelector('i');
            i.className=html.classList.contains('theme-light')?'bi bi-sun':'bi bi-moon-stars';}
        syncIcon();
        if(btn) btn.addEventListener('click',()=>{
            const isLight=!html.classList.contains('theme-light');
            html.classList.toggle('theme-light',isLight);
            body.classList.toggle('theme-light',isLight);
            html.setAttribute('data-bs-theme',isLight?'light':'dark');
            localStorage.setItem('jasaku_theme',isLight?'light':'dark');
            syncIcon();
        });
        // Mobile sidebar
        const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('sOverlay');
        const toggle=document.getElementById('menuToggle');
        function open(){sidebar.classList.add('open');overlay.classList.add('open');}
        function close(){sidebar.classList.remove('open');overlay.classList.remove('open');}
        if(toggle) toggle.addEventListener('click',open);
        if(overlay) overlay.addEventListener('click',close);
        window.addEventListener('resize',()=>{if(window.innerWidth>=992)close();});
    })();
    </script>
    @yield('extra-js')
</body>
</html>


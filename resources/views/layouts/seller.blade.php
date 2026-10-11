<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller — @yield('title') | ThriftStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    :root { --primary:#1a1a1a;--accent:#d4a843;--bg:#f8f8f6;--border:#e8e8e8;--muted:#777;--faint:#aaa; }
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Jost',sans-serif;background:var(--bg);color:var(--primary);display:flex;min-height:100vh}
    .seller-sidebar{width:220px;min-height:100vh;background:var(--primary);display:flex;flex-direction:column;position:fixed;top:0;left:0;z-index:100}
    .sl-brand{padding:20px;border-bottom:1px solid rgba(255,255,255,.1)}
    .sl-brand-logo{font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:white}
    .sl-brand-logo span{color:var(--accent)}
    .sl-brand-sub{font-size:.68rem;color:rgba(255,255,255,.4);letter-spacing:1.5px;text-transform:uppercase;margin-top:2px}
    .sl-nav{padding:12px 10px;flex:1}
    .sl-link{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;color:rgba(255,255,255,.6);text-decoration:none;font-size:.82rem;margin-bottom:2px;transition:all .18s}
    .sl-link:hover,.sl-link.active{background:rgba(255,255,255,.08);color:white}
    .sl-link.active{background:rgba(212,168,67,.15);color:var(--accent)}
    .sl-link i{width:18px;text-align:center}
    .sl-section-lbl{font-size:.6rem;letter-spacing:1.8px;text-transform:uppercase;color:rgba(255,255,255,.25);padding:8px 12px 4px;margin-top:6px}
    .seller-main{margin-left:220px;flex:1;min-height:100vh}
    .seller-topbar{background:white;border-bottom:1px solid var(--border);padding:14px 28px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50}
    .seller-content{padding:28px}
    .flash-ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:12px 16px;font-size:.85rem;margin-bottom:16px}
    .flash-err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:12px 16px;font-size:.85rem;margin-bottom:16px}
    .btn-gold{display:inline-flex;align-items:center;gap:7px;background:var(--accent);border:none;border-radius:6px;padding:9px 18px;font-size:.8rem;font-weight:700;color:white;cursor:pointer;font-family:'Jost',sans-serif;text-decoration:none;letter-spacing:.5px;text-transform:uppercase;transition:background .2s}
    .btn-gold:hover{background:#b8882e;color:white}
    </style>
    @stack('styles')
</head>
<body>

<aside class="seller-sidebar">
    <div class="sl-brand">
        <div class="sl-brand-logo">Thrift<span>Store</span></div>
        <div class="sl-brand-sub">Seller Dashboard</div>
    </div>
    <nav class="sl-nav">
        <a href="{{ route('seller.dashboard') }}" class="sl-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <div class="sl-section-lbl">Produk</div>
        <a href="{{ route('seller.products.index') }}" class="sl-link {{ request()->routeIs('seller.products*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Produk Saya
        </a>
        <a href="{{ route('seller.products.create') }}" class="sl-link">
            <i class="bi bi-plus-circle"></i> Upload Produk
        </a>
        <div class="sl-section-lbl">Transaksi</div>
        <a href="{{ route('seller.orders.index') }}" class="sl-link {{ request()->routeIs('seller.orders*') ? 'active' : '' }}">
            <i class="bi bi-bag-check"></i> Pesanan
        </a>
        <a href="{{ route('seller.finance') }}" class="sl-link {{ request()->routeIs('seller.finance') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i> Keuangan
        </a>
        <a href="{{ route('seller.reviews.index') }}" class="sl-link {{ request()->routeIs('seller.reviews*') ? 'active' : '' }}">
            <i class="bi bi-star-half"></i> Ulasan
        </a>
        <div class="sl-section-lbl">Toko</div>
        <a href="{{ route('seller.profile.edit') }}" class="sl-link {{ request()->routeIs('seller.profile*') ? 'active' : '' }}">
            <i class="bi bi-shop"></i> Profil Toko
        </a>
        <hr style="border-color:rgba(255,255,255,.1);margin:8px 10px">
        <a href="{{ route('home') }}" target="_blank" class="sl-link">
            <i class="bi bi-box-arrow-up-right"></i> Lihat Toko
        </a>
    </nav>
    <div style="padding:12px;border-top:1px solid rgba(255,255,255,.1)">
        <form action="{{ route('logout') }}" method="POST">@csrf
            <button type="submit" style="width:100%;background:none;border:none;display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:8px;color:rgba(255,255,255,.4);font-size:.82rem;cursor:pointer;font-family:'Jost',sans-serif;transition:all .2s" onmouseover="this.style.background='rgba(239,68,68,.1)';this.style.color='#f87171'" onmouseout="this.style.background='none';this.style.color='rgba(255,255,255,.4)'">
                <i class="bi bi-box-arrow-right" style="width:18px;text-align:center"></i> Logout
            </button>
        </form>
    </div>
</aside>

<div class="seller-main">
    <div class="seller-topbar">
        <div>
            <div style="font-size:.92rem;font-weight:600">@yield('title','Dashboard')</div>
            <div style="font-size:.72rem;color:var(--muted)">{{ Auth::user()->store?->name ?? 'ThriftStore' }}</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <div style="font-size:.82rem;color:var(--muted)">Halo, <strong>{{ Auth::user()->name }}</strong></div>
        </div>
    </div>
    <div class="seller-content">
        @if(session('success'))
        <div class="flash-ok"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="flash-err"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
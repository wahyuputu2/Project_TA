<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ThriftStore') — Premium Thrift Fashion</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/thriftstore.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

@php
    $cartCount = app(\App\Services\CartService::class)->count();
@endphp
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <!-- Burger menu (Mobile) -->
        <button class="navbar-toggler border-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navMenu">
            <i class="bi bi-list fs-3" style="color: var(--primary);"></i>
        </button>

        <!-- Centered Logo (Mobile) -->
        <a class="navbar-brand d-lg-none mx-auto" href="{{ route('home') }}">
            Frog Market
        </a>

        <!-- Right icons (Mobile) -->
        <div class="d-flex d-lg-none align-items-center gap-2">
            <a href="{{ route('cart.index') }}" class="cart-btn py-1 px-2">
                <i class="bi bi-bag"></i>
                @if($cartCount > 0)
                    <span class="cart-count">{{ $cartCount }}</span>
                @endif
            </a>
        </div>

        <div class="collapse navbar-collapse" id="navMenu">
            <!-- Left: Links -->
            <ul class="navbar-nav me-auto gap-1 d-none d-lg-flex" style="width: 38%; justify-content: flex-start;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('catalog.index') }}">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('stores.index') }}">Toko</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pages.faq') }}">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pages.policy') }}">Policy</a>
                </li>
            </ul>

            <!-- Centered Logo (Desktop) -->
            <a class="navbar-brand d-none d-lg-block mx-auto text-center" href="{{ route('home') }}" style="width: 24%;">
                Frog Market
            </a>

            <!-- Right: Icons & Actions (Desktop) -->
            <div class="d-flex align-items-center gap-3 ms-auto" style="width: 38%; justify-content: flex-end;">
                <!-- Search Trigger -->
                <button class="navbar-icon-btn d-none d-lg-inline-block" onclick="focusSearch()">
                    <i class="bi bi-search"></i>
                </button>

                @auth
                    <!-- User dropdown -->
                    <div class="dropdown">
                        <button class="navbar-icon-btn dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark small" style="color: #111 !important;">{{ Auth::user()->name }}</div>
                                <div class="text-muted small" style="font-size:0.7rem;">{{ Auth::user()->email }}</div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <i class="bi bi-bag-check me-2"></i>Pesanan Saya
                                </a>
                            </li>
                            @if(Auth::user()->role === 'customer' && !Auth::user()->hasStore())
                                <li>
                                    <a class="dropdown-item" href="{{ route('seller.register') }}">
                                        <i class="bi bi-shop me-2"></i>Buka Toko
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->isSeller() || Auth::user()->hasStore())
                                <li>
                                    <a class="dropdown-item" href="{{ route('seller.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard Toko
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->role === 'admin')
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Admin Panel
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="navbar-icon-btn" title="Masuk">
                        <i class="bi bi-person"></i>
                    </a>
                    <a href="{{ route('register') }}" class="navbar-icon-btn" title="Daftar"
                        style="position:relative">
                        <i class="bi bi-person-plus"></i>
                     </a>
                @endauth

                <!-- Wishlist (Dummy) -->
                <a href="#" class="navbar-icon-btn d-none d-lg-inline-block">
                    <i class="bi bi-heart"></i>
                </a>

                <!-- Cart Count -->
                <a href="{{ route('cart.index') }}" class="cart-btn">
                    <i class="bi bi-bag"></i>
                    @if($cartCount > 0)
                        <span class="cart-count">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>

            <!-- Mobile Menu Links -->
            <ul class="navbar-nav d-lg-none w-100 mt-3 border-top pt-2">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('catalog.index') }}">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('stores.index') }}">Toko</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pages.faq') }}">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pages.policy') }}">Policy</a>
                </li>
                @auth
                    <li class="nav-item border-top mt-2 pt-2">
                        <a class="nav-link" href="{{ route('orders.index') }}">Pesanan Saya</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="nav-link border-0 bg-transparent text-danger text-start w-100">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item border-top mt-2 pt-2">
                        <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Daftar</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

@if(session('success') || session('error'))
<div class="container mt-3">

    @if(session('success'))
    <div class="flash-success">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="flash-error">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ session('error') }}
    </div>
    @endif

</div>
@endif

<main>
    @yield('content')
</main>

{{-- FOOTER --}}
<footer class="py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="footer-brand">Frog Market</div>
                <p style="font-size:.82rem;color:rgba(255,255,255,.45);line-height:1.8;max-width:240px;margin-top:8px">
                    Baju bekas premium pilihan. Setiap item unik, jujur, dan berkelanjutan.
                </p>
                <a href="https://wa.me/{{ config('services.whatsapp.number') }}"
                   target="_blank"
                   style="display:inline-flex;align-items:center;gap:7px;background:#25d366;color:white;padding:9px 18px;font-size:.78rem;font-weight:600;text-decoration:none;margin-top:14px;transition:background .15s"
                   onmouseover="this.style.background='#1da851'"
                   onmouseout="this.style.background='#25d366'">
                    <i class="bi bi-whatsapp"></i> Chat Admin
                </a>
            </div>
            <div class="col-lg-2 col-6">
                <div class="footer-col-title">Belanja</div>
                <ul class="list-unstyled" style="font-size:.82rem">
                    <li class="mb-2"><a href="{{ route('catalog.index') }}">Semua Produk</a></li>
                    <li class="mb-2"><a href="{{ route('catalog.index') }}?gender=pria">Pria</a></li>
                    <li class="mb-2"><a href="{{ route('catalog.index') }}?gender=wanita">Wanita</a></li>
                    <li class="mb-2"><a href="{{ route('catalog.index') }}?condition=like_new">Like New</a></li>
                    <li class="mb-2"><a href="{{ route('stores.index') }}">Semua Toko</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <div class="footer-col-title">Info</div>
                <ul class="list-unstyled" style="font-size:.82rem">
                    <li class="mb-2"><a href="{{ route('pages.faq') }}">FAQ</a></li>
                    <li class="mb-2"><a href="{{ route('pages.policy') }}">Kebijakan Toko</a></li>
                    @auth<li class="mb-2"><a href="{{ route('orders.index') }}">Pesanan Saya</a></li>@endauth
                    @guest<li class="mb-2"><a href="{{ route('seller.register') }}">Buka Toko</a></li>@endguest
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <div class="footer-col-title">Ikuti Kami</div>
                <ul class="list-unstyled" style="font-size:.82rem">
                    <li class="mb-2"><a href="#"><i class="bi bi-instagram me-2"></i>Instagram</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-tiktok me-2"></i>TikTok</a></li>
                    <li class="mb-2"><a href="https://wa.me/{{ config('services.whatsapp.number') }}" target="_blank">
                        <i class="bi bi-whatsapp me-2"></i>WhatsApp
                    </a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <div class="footer-col-title">Kenapa Thrift?</div>
                <div class="footer-feature"><i class="bi bi-leaf-fill" style="color:#4ade80"></i> Ramah lingkungan</div>
                <div class="footer-feature"><i class="bi bi-patch-check-fill" style="color:#7dd3fc"></i> Dikurasi manual</div>
                <div class="footer-feature"><i class="bi bi-shield-lock-fill" style="color:#7dd3fc"></i> Pembayaran aman</div>
                <div class="footer-feature"><i class="bi bi-truck" style="color:#7dd3fc"></i> Ongkir real-time</div>
            </div>
        </div>
        <hr class="my-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span style="font-size:.75rem;color:rgba(255,255,255,.25)">
                © {{ date('Y') }} FrogMarket. All rights reserved.
            </span>
            <div class="d-flex gap-3" style="font-size:.75rem;color:rgba(255,255,255,.25)">
                <span><i class="bi bi-shield-check me-1"></i>SSL Secured</span>
                <span><i class="bi bi-truck me-1"></i>Fast Shipping</span>
                <span><i class="bi bi-patch-check me-1"></i>Verified Items</span>
            </div>
        </div>
    </div>
</footer>

<div class="wa-float">
    <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Halo+admin,+saya+ingin+tanya"
       target="_blank">

        <i class="bi bi-whatsapp fs-5"></i>

        <span class="d-none d-md-inline">
            Chat Admin
        </span>
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function focusSearch() {
    if (window.location.pathname.includes('/produk') || window.location.pathname === '/') {
        if (typeof openDrawer === 'function') {
            openDrawer();
            setTimeout(() => {
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) searchInput.focus();
            }, 350);
        }
    } else {
        window.location.href = "{{ route('catalog.index') }}?open_search=1";
    }
}
</script>

@stack('scripts')

</body>
</html>
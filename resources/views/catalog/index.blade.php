@extends('layouts.app')
@section('title', 'Katalog Produk')
@section('content')

{{-- HERO --}}
@if(!request()->hasAny(['search','category','brand','size','condition','gender','min_price','max_price']))
<section class="hero-banner">
    <div class="container position-relative" style="z-index:2">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-eyebrow">
                    URBAN EDGE
                </div>
                <h1 class="hero-title">
                    Jackets for the<br>Modern Man
                </h1>
                <p class="hero-sub">
                    Koleksi pakaian bekas pilihan premium dikurasi manual. Setiap item unik, jujur, dan ramah lingkungan. Temukan gaya sejatimu sekarang.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('catalog.index') }}?category=jaket" class="btn-gold">
                        Discovery Now
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Slide Arrows (Aesthetic) -->
    <button class="hero-slide-btn prev" onclick="window.location.href='#prev'"><i class="bi bi-chevron-left"></i></button>
    <button class="hero-slide-btn next" onclick="window.location.href='#next'"><i class="bi bi-chevron-right"></i></button>
</section>
@endif

{{-- NEW ARRIVALS HEADER --}}
<div class="container">
    <div class="new-arrivals-title">New Arrivals</div>
    <div class="new-arrivals-sub">Temukan rilisan produk teranyar minggu ini</div>
</div>

{{-- CATEGORY FILTER CHIPS --}}
<div class="cat-strip">
    <div class="container">
        <div class="cat-chips">
            <a href="{{ route('catalog.index', request()->except(['gender', 'category'])) }}"
               class="cat-chip {{ !request('gender') && !request('category') ? 'active' : '' }}">
                ALL
            </a>
            <a href="{{ route('catalog.index', array_merge(request()->all(), ['gender' => 'wanita'])) }}"
               class="cat-chip {{ request('gender') === 'wanita' ? 'active' : '' }}">
                WOMEN
            </a>
            <a href="{{ route('catalog.index', array_merge(request()->all(), ['gender' => 'pria'])) }}"
               class="cat-chip {{ request('gender') === 'pria' ? 'active' : '' }}">
                MEN
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('catalog.index', array_merge(request()->all(), ['category' => $cat->slug])) }}"
               class="cat-chip {{ request('category') === $cat->slug ? 'active' : '' }}">
                {{ strtoupper($cat->name) }} <span class="cnt">{{ $cat->products_count }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- TOOLBAR & PRODUCTS --}}
<div class="container py-4">
    <div class="catalog-toolbar">
        <div class="toolbar-left">
            <span class="result-count">
                Showing <strong>{{ $products->total() }}</strong> products
                @if(!empty($filters['search'])) for "<em>{{ $filters['search'] }}</em>" @endif
            </span>

            {{-- Active filter tags --}}
            @php
                $activeFilters = array_filter([
                    'Kategori'  => $filters['category'] ?? null,
                    'Brand'     => $filters['brand'] ?? null,
                    'Size'      => $filters['size'] ?? null,
                    'Kondisi'   => $filters['condition'] ?? null,
                    'Gender'    => $filters['gender'] ?? null,
                    'Min Harga' => isset($filters['min_price']) ? 'Rp '.number_format($filters['min_price'],0,',','.') : null,
                    'Max Harga' => isset($filters['max_price']) ? 'Rp '.number_format($filters['max_price'],0,',','.') : null,
                ]);
            @endphp
            @foreach($activeFilters as $key => $val)
            <span class="active-filter-tag">
                {{ $key }}: {{ $val }}
                <a href="{{ route('catalog.index', request()->except(strtolower(str_replace(' ','_',$key)))) }}" class="remove-tag">×</a>
            </span>
            @endforeach
        </div>

        <div class="toolbar-right">
            <form method="GET" action="{{ route('catalog.index') }}" id="sortForm" class="d-inline-block">
                @foreach(request()->except('sort') as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()">
                    <option value="newest"     @selected(($filters['sort']??'newest')==='newest')>Terbaru</option>
                    <option value="price_asc"  @selected(($filters['sort']??'')==='price_asc')>Harga Terendah</option>
                    <option value="price_desc" @selected(($filters['sort']??'')==='price_desc')>Harga Tertinggi</option>
                </select>
            </form>

            <button class="btn-open-filter" id="openFilterBtn">
                <i class="bi bi-sliders"></i> Filter
                @php $filterCount = count(array_filter($filters ?? [], fn($v) => !empty($v) && $v !== 'newest')); @endphp
                @if($filterCount > 0)
                <span class="filter-count-badge ms-1">{{ $filterCount }}</span>
                @endif
            </button>
        </div>
    </div>

    {{-- PRODUCT GRID --}}
    @if($products->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="bi bi-search"></i></div>
        <h5 class="fw-bold mb-2">Produk tidak ditemukan</h5>
        <p class="text-muted small mb-4">Coba ubah filter atau kata kunci pencarian Anda.</p>
        <a href="{{ route('catalog.index') }}" class="btn-gold" style="border: 1px solid #111;">Reset Filter</a>
    </div>
    @else
    <div class="product-grid">
        @foreach($products as $product)
        <div class="product-card">
            <div class="pcard-img-wrap">
                @if($product->mainImage())
                    <img src="{{ Storage::url($product->mainImage()->image_path) }}"
                         alt="{{ $product->name }}" loading="lazy">
                @else
                    <div class="pcard-no-img">
                        <i class="bi bi-image"></i>
                        <span style="font-size:.65rem;margin-top:8px">No Image</span>
                    </div>
                @endif

                @if($product->isSold())
                    <div class="pcard-sold-overlay"><span class="pcard-sold-tag">TERJUAL</span></div>
                @elseif($product->isLocked())
                    <div class="pcard-locked-overlay"><span class="pcard-locked-tag">DIPESAN</span></div>
                @else
                    <div class="pcard-overlay">
                        <a href="{{ route('catalog.show', $product->slug) }}" class="btn-card-detail">Lihat Detail</a>
                    </div>
                @endif

                <span class="pcard-cond-badge">{{ $product->conditionLabel() }}</span>
                <span class="pcard-gender-badge">{{ ucfirst($product->gender) }}</span>
            </div>
            <div class="pcard-body">
                <div class="pcard-brand">{{ $product->brand?->name ?? 'No Brand' }}</div>
                <div class="pcard-name" title="{{ $product->name }}">{{ $product->name }}</div>
                
                <!-- Rating Stars -->
                <div class="d-flex align-items-center gap-1 mb-2">
                    @for($s=1;$s<=5;$s++)
                        <i class="bi bi-star-fill text-warning" style="font-size: 0.75rem;"></i>
                    @endfor
                </div>

                <div class="pcard-foot">
                    <div class="pcard-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    <a href="{{ route('catalog.show', $product->slug) }}" class="pcard-btn">Detail →</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $products->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- BOTTOM PROMOTION BANNERS GRID --}}
@if(!request()->hasAny(['search','category','brand','size','condition','gender','min_price','max_price']))
<div class="container">
    <section class="promo-section">
        <div class="promo-grid">
            <!-- Left tall banner -->
            <div class="promo-card tall-left" style="background-image: url('{{ asset('images/banner.jpg') }}');">
                <div class="promo-eyebrow">ETHEREAL ELEGANCE</div>
                <div class="promo-title">Where Dreams Meet Couture</div>
                <a href="{{ route('catalog.index') }}?gender=wanita" class="btn-promo-shop">Shop Now</a>
            </div>

            <!-- Middle top landscape banner -->
            <div class="promo-card landscape-mid-top" style="background-image: url('{{ asset('images/banner_women.jpg') }}');">
                <div class="promo-eyebrow">RADIANT REVERIE</div>
                <div class="promo-title">Enchanting Styles for Every Woman</div>
                <a href="{{ route('catalog.index') }}?gender=wanita" class="btn-promo-shop">Shop Now</a>
            </div>

            <!-- Middle bottom landscape banner -->
            <div class="promo-card landscape-mid-bottom" style="background-image: url('{{ asset('images/banner_man.jpg') }}');">
                <div class="promo-eyebrow">URBAN STRIDES</div>
                <div class="promo-title">Chic Footwear for City Living</div>
                <a href="{{ route('catalog.index') }}" class="btn-promo-shop">Shop Now</a>
            </div>

            <!-- Right tall banner -->
            <div class="promo-card tall-right">
                <div class="promo-eyebrow" style="color: rgba(255,255,255,0.7);">URBAN STRIDES</div>
                <div class="promo-title" style="font-family: 'Outfit', sans-serif;">Trendsetting Bags for Her</div>
                <div class="promo-percent">50%</div>
                <a href="{{ route('catalog.index') }}" class="btn-promo-shop">Shop Now</a>
            </div>
        </div>
    </section>
</div>
@endif

{{-- FILTER DRAWER --}}
<div class="filter-backdrop" id="filterBackdrop"></div>

<div class="filter-drawer" id="filterDrawer">
    <div class="filter-drawer-header">
        <div class="filter-drawer-title">
            <i class="bi bi-sliders2 me-2"></i>Filter & Sortir
        </div>
        <button class="filter-drawer-close" id="closeFilterBtn">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="filter-drawer-body">
        <form method="GET" action="{{ route('catalog.index') }}" id="filterForm">

            {{-- Search --}}
            <div class="fd-section">
                <div class="fd-label">Pencarian</div>
                <div class="search-box">
                    <i class="bi bi-search text-muted" style="font-size:.85rem"></i>
                    <input type="text" name="search" placeholder="Nama produk / brand..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>

            {{-- Kondisi --}}
            <div class="fd-section">
                <div class="fd-label">Kondisi Barang</div>
                <div class="fd-radio-grid">
                    @foreach(['' => 'Semua', 'like_new' => 'Like New', '9/10' => '9/10', '8/10' => '8/10', '7/10' => '7/10', '6/10' => '6/10'] as $val => $lbl)
                    <label class="fd-radio-pill">
                        <input type="radio" name="condition" value="{{ $val }}" @checked(($filters['condition']??'')===$val)>
                        <span>{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Gender --}}
            <div class="fd-section">
                <div class="fd-label">Gender</div>
                <div class="fd-radio-grid">
                    @foreach(['' => 'Semua', 'pria' => 'Pria', 'wanita' => 'Wanita', 'unisex' => 'Unisex'] as $val => $lbl)
                    <label class="fd-radio-pill">
                        <input type="radio" name="gender" value="{{ $val }}" @checked(($filters['gender']??'')===$val)>
                        <span>{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Brand --}}
            <div class="fd-section">
                <div class="fd-label">Brand</div>
                <div class="fd-brand-list">
                    @foreach($brands as $brand)
                    <label class="fd-brand-item">
                        <input type="radio" name="brand" value="{{ $brand->slug }}" @checked(($filters['brand']??'')===$brand->slug)>
                        <span class="fd-brand-name">{{ $brand->name }}</span>
                        <span class="fd-brand-count">{{ $brand->products_count }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Ukuran --}}
            <div class="fd-section">
                <div class="fd-label">Ukuran</div>
                <div class="fd-size-grid">
                    @foreach($sizes as $sz)
                    <label class="fd-size-wrap">
                        <input type="radio" name="size" value="{{ $sz }}" @checked(($filters['size']??'')===$sz)>
                        <span class="fd-size-btn">{{ $sz }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Harga --}}
            <div class="fd-section">
                <div class="fd-label">Rentang Harga</div>
                <div class="fd-price-range">
                    <div class="fd-price-input-wrap">
                        <span class="fd-price-prefix">Rp</span>
                        <input type="number" name="min_price" class="fd-price-input"
                               placeholder="0" value="{{ $filters['min_price'] ?? '' }}">
                    </div>
                    <span class="fd-price-sep">—</span>
                    <div class="fd-price-input-wrap">
                        <span class="fd-price-prefix">Rp</span>
                        <input type="number" name="max_price" class="fd-price-input"
                               placeholder="∞" value="{{ $filters['max_price'] ?? '' }}">
                    </div>
                </div>
            </div>

        </form>
    </div>

    <div class="filter-drawer-footer">
        <a href="{{ route('catalog.index') }}" class="fd-btn-reset">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
        </a>
        <button type="submit" form="filterForm" class="fd-btn-apply">
            Terapkan Filter
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const openBtn    = document.getElementById('openFilterBtn');
const closeBtn   = document.getElementById('closeFilterBtn');
const drawer     = document.getElementById('filterDrawer');
const backdrop   = document.getElementById('filterBackdrop');

function openDrawer() {
    if (drawer) drawer.classList.add('open');
    if (backdrop) backdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeDrawer() {
    if (drawer) drawer.classList.remove('open');
    if (backdrop) backdrop.classList.remove('open');
    document.body.style.overflow = '';
}

if (openBtn) openBtn.addEventListener('click', openDrawer);
if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
if (backdrop) backdrop.addEventListener('click', closeDrawer);

// Tutup dengan ESC
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDrawer();
});

// Auto-open search if open_search query is present
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('open_search')) {
    openDrawer();
    setTimeout(() => {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) searchInput.focus();
    }, 350);
}
</script>
@endpush
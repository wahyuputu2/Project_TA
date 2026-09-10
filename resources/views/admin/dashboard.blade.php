@extends('layouts.admin')
@section('title','Dashboard')
@section('content')

{{-- PAGE HEADER --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:16px">
    <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
            <div style="width:4px;height:28px;background:linear-gradient(180deg,#3b6fe8,#6366f1)"></div>
            <h1 style="font-family:'Outfit',sans-serif;font-size:1.55rem;font-weight:700;color:#e8edf5;margin:0;letter-spacing:-.3px">
                Selamat datang, {{ Auth::user()->name }} 👋
            </h1>
        </div>
        <p style="font-size:.82rem;color:rgba(232,237,245,.4);margin:0;padding-left:14px">
            <i class="bi bi-clock me-1" style="opacity:.6"></i>
            {{ now()->format('l, d F Y') }} &nbsp;·&nbsp; Overview platform <strong style="color:rgba(232,237,245,.6)">Frog Market</strong>
        </p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        @if($pendingStores > 0)
        <a href="{{ route('admin.stores.index', ['status'=>'pending']) }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-shop-window"></i> Verifikasi Toko
            <span style="background:rgba(255,255,255,.2);padding:1px 7px;font-size:.72rem;font-weight:800">{{ $pendingStores }}</span>
        </a>
        @endif
        @if($pendingOrders > 0)
        <a href="{{ route('admin.orders.index', ['status'=>'paid']) }}" class="adm-btn adm-btn-secondary">
            <i class="bi bi-bag-check"></i> Proses Pesanan
            <span style="background:rgba(59,111,232,.25);color:#93b4f5;padding:1px 7px;font-size:.72rem;font-weight:800">{{ $pendingOrders }}</span>
        </a>
        @endif
    </div>
</div>

{{-- STAT CARDS ROW 1 --}}
<div class="row g-3 mb-3">
    <div class="col-xl-3 col-md-6 adm-animate">
        <div class="adm-stat" style="--stat-color:#3b6fe8">
            <i class="bi bi-tags-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Total Produk</div>
            <div class="adm-stat-value">{{ number_format($totalProducts, 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <span style="color:#34d399">{{ number_format($availableProducts, 0, ',', '.') }}</span> tersedia ·
                <span style="color:#f87171">{{ number_format($soldProducts, 0, ',', '.') }}</span> terjual
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 adm-animate">
        <div class="adm-stat" style="--stat-color:#6366f1">
            <i class="bi bi-bag-check-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Total Pesanan</div>
            <div class="adm-stat-value">{{ number_format($totalOrders, 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <span style="color:#fbbf24">{{ number_format($pendingOrders, 0, ',', '.') }}</span> perlu diproses
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 adm-animate">
        <div class="adm-stat" style="--stat-color:#10b981">
            <i class="bi bi-currency-dollar adm-stat-icon"></i>
            <div class="adm-stat-label">Total Pendapatan</div>
            <div class="adm-stat-value" style="font-size:1.4rem">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="adm-stat-sub">Dari pesanan selesai</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 adm-animate">
        <div class="adm-stat" style="--stat-color:#8b5cf6">
            <i class="bi bi-people-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Total User</div>
            <div class="adm-stat-value">{{ number_format($totalUsers, 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <span style="color:#34d399">{{ number_format($totalSellers, 0, ',', '.') }}</span> seller ·
                <span style="color:#93b4f5">{{ number_format($totalCustomers, 0, ',', '.') }}</span> pembeli
            </div>
        </div>
    </div>
</div>

{{-- STAT CARDS ROW 2 --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6 adm-animate">
        <div class="adm-stat" style="--stat-color:#f43f5e">
            <i class="bi bi-shop-window adm-stat-icon"></i>
            <div class="adm-stat-label">Total Toko</div>
            <div class="adm-stat-value">{{ number_format($totalStores, 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <span style="color:#fbbf24">{{ number_format($pendingStores, 0, ',', '.') }}</span> pending verifikasi
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 adm-animate">
        <div class="adm-stat" style="--stat-color:#06b6d4">
            <i class="bi bi-star-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Ulasan Masuk</div>
            <div class="adm-stat-value">{{ number_format($totalReviews, 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <span style="color:#fbbf24">{{ number_format($pendingReviews, 0, ',', '.') }}</span> menunggu moderasi
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 adm-animate">
        <div class="adm-stat" style="--stat-color:#84cc16">
            <i class="bi bi-cash-coin adm-stat-icon"></i>
            <div class="adm-stat-label">Pending Withdraw</div>
            <div class="adm-stat-value" style="font-size:1.4rem">Rp {{ number_format($pendingWithdraw, 0, ',', '.') }}</div>
            <div class="adm-stat-sub">{{ number_format($pendingWithdrawCount, 0, ',', '.') }} permintaan aktif</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 adm-animate">
        <div class="adm-stat" style="--stat-color:#f97316">
            <i class="bi bi-box-seam-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Butuh Diproses</div>
            <div class="adm-stat-value" style="color:#fb923c">{{ number_format($needProcess, 0, ',', '.') }}</div>
            <div class="adm-stat-sub">Pesanan paid belum dikemas</div>
        </div>
    </div>
</div>

{{-- ACTION NEEDED BANNER --}}
@if($pendingStores > 0 || $pendingOrders > 0 || $pendingReviews > 0)
<div style="background:rgba(59,111,232,.06);border:1px solid rgba(59,111,232,.15);border-left:3px solid #3b6fe8;padding:16px 20px;margin-bottom:28px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
        <i class="bi bi-bell-fill" style="color:#fbbf24;font-size:1rem"></i>
        <span style="font-size:.83rem;font-weight:600;color:#e8edf5">Perlu Tindakan Segera</span>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap">
        @if($pendingStores > 0)
        <a href="{{ route('admin.stores.index', ['status'=>'pending']) }}"
           class="dash-action-card dash-action-amber">
            <div class="dash-action-icon" style="background:rgba(245,158,11,.12);color:#fbbf24">
                <i class="bi bi-shop-window"></i>
            </div>
            <div>
                <div class="dash-action-title">{{ $pendingStores }} Toko Pending</div>
                <div class="dash-action-sub">Klik untuk verifikasi →</div>
            </div>
        </a>
        @endif
        @if($pendingOrders > 0)
        <a href="{{ route('admin.orders.index', ['status'=>'paid']) }}"
           class="dash-action-card dash-action-blue">
            <div class="dash-action-icon" style="background:rgba(59,111,232,.12);color:#93b4f5">
                <i class="bi bi-bag-check-fill"></i>
            </div>
            <div>
                <div class="dash-action-title">{{ $pendingOrders }} Pesanan Dibayar</div>
                <div class="dash-action-sub">Proses pengiriman →</div>
            </div>
        </a>
        @endif
        @if($pendingReviews > 0)
        <a href="{{ route('admin.reviews.index', ['status'=>'pending']) }}"
           class="dash-action-card dash-action-purple">
            <div class="dash-action-icon" style="background:rgba(139,92,246,.12);color:#a78bfa">
                <i class="bi bi-star-fill"></i>
            </div>
            <div>
                <div class="dash-action-title">{{ $pendingReviews }} Ulasan Pending</div>
                <div class="dash-action-sub">Moderasi sekarang →</div>
            </div>
        </a>
        @endif
    </div>
</div>
@endif

{{-- MAIN GRID: Orders Table + Right Sidebar --}}
<div class="row g-4">

    {{-- RECENT ORDERS --}}
    <div class="col-xl-8">
        <div class="adm-card h-100">
            <div class="adm-card-header">
                <i class="bi bi-bag-check-fill"></i>
                Pesanan Terbaru
                <a href="{{ route('admin.orders.index') }}" class="adm-btn adm-btn-secondary adm-btn-sm ms-auto">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div style="overflow-x:auto">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Pembeli</th>
                            <th>Toko</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show',$order) }}"
                                   style="color:#93b4f5;font-weight:600;text-decoration:none;font-size:.82rem;font-family:'Outfit',monospace;letter-spacing:.3px">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px">
                                    <div style="width:28px;height:28px;background:linear-gradient(135deg,#3b6fe8,#6366f1);color:white;font-size:.65rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:50% !important">
                                        {{ strtoupper(substr($order->user->name,0,1)) }}
                                    </div>
                                    <span style="color:#e8edf5;font-size:.84rem">{{ $order->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($order->store)
                                <span class="adm-badge adm-badge-gold" style="font-size:.63rem">{{ $order->store->name }}</span>
                                @else
                                <span style="color:rgba(232,237,245,.2)">—</span>
                                @endif
                            </td>
                            <td style="font-weight:600;color:#e8edf5">Rp {{ number_format($order->total,0,',','.') }}</td>
                            <td>
                                @php
                                    $badge = match($order->status) {
                                        'pending_payment' => 'adm-badge-yellow',
                                        'paid'            => 'adm-badge-blue',
                                        'processing'      => 'adm-badge-gold',
                                        'shipped'         => 'adm-badge-purple',
                                        'delivered'       => 'adm-badge-green',
                                        'cancelled'       => 'adm-badge-red',
                                        default           => 'adm-badge-gray',
                                    };
                                @endphp
                                <span class="adm-badge {{ $badge }}">{{ $order->statusLabel() }}</span>
                            </td>
                            <td style="font-size:.75rem;color:rgba(232,237,245,.35)">
                                {{ $order->created_at->format('d M Y') }}<br>
                                <span style="font-size:.68rem">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px;color:rgba(232,237,245,.2)">
                                <i class="bi bi-inbox" style="font-size:2.2rem;display:block;margin-bottom:10px;opacity:.4"></i>
                                Belum ada pesanan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="col-xl-4">

        {{-- RECENT STORES --}}
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <i class="bi bi-shop-window"></i>
                Toko Terbaru
                <a href="{{ route('admin.stores.index') }}" class="adm-btn adm-btn-secondary adm-btn-sm ms-auto">Semua</a>
            </div>
            <div>
                @forelse($recentStores as $store)
                <div class="dash-list-row">
                    <div class="adm-store-avatar" style="width:34px;height:34px;background:linear-gradient(135deg,rgba(59,111,232,.3),rgba(99,102,241,.3));border:1px solid rgba(59,111,232,.25);color:#93b4f5;font-size:.78rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        {{ strtoupper(substr($store->name,0,1)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.84rem;font-weight:500;color:#e8edf5;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $store->name }}</div>
                        <div style="font-size:.71rem;color:rgba(232,237,245,.3)">{{ $store->user->name }}</div>
                    </div>
                    @php
                        $sb = match($store->status) {
                            'active'    => 'adm-badge-green',
                            'pending'   => 'adm-badge-yellow',
                            'suspended' => 'adm-badge-red',
                            default     => 'adm-badge-gray',
                        };
                    @endphp
                    <span class="adm-badge {{ $sb }}" style="font-size:.6rem">{{ $store->statusLabel() }}</span>
                </div>
                @empty
                <div style="text-align:center;padding:28px;color:rgba(232,237,245,.2);font-size:.82rem">
                    <i class="bi bi-shop" style="font-size:1.5rem;display:block;margin-bottom:8px;opacity:.3"></i>
                    Belum ada toko
                </div>
                @endforelse
            </div>
        </div>

        {{-- TOP PRODUCTS --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <i class="bi bi-fire" style="color:#f97316"></i> Produk Terlaris
            </div>
            <div>
                @forelse($topProducts as $i => $product)
                @php
                    // Ambil sekali saja, jangan panggil $product->mainImage() dua kali
                    // (sebelumnya dipanggil di @if lalu dipanggil lagi untuk ->image_path,
                    // artinya 2x query per produk di dalam loop ini).
                    $mainImage = $product->mainImage();
                @endphp
                <div class="dash-list-row">
                    <span style="font-size:.7rem;font-weight:800;color:{{ $i === 0 ? '#fbbf24' : ($i === 1 ? '#94a3b8' : ($i === 2 ? '#c97c50' : 'rgba(232,237,245,.2)')) }};width:18px;text-align:center;flex-shrink:0">
                        #{{ $i+1 }}
                    </span>
                    @if($mainImage)
                    <img src="{{ Storage::url($mainImage->image_path) }}"
                         alt="{{ $product->name }}"
                         style="width:36px;height:46px;object-fit:cover;flex-shrink:0;border:1px solid rgba(255,255,255,.07)">
                    @else
                    <div style="width:36px;height:46px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:rgba(232,237,245,.2);font-size:.9rem">
                        <i class="bi bi-image"></i>
                    </div>
                    @endif
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.82rem;font-weight:500;color:#e8edf5;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $product->name }}</div>
                        <div style="font-size:.68rem;color:rgba(232,237,245,.3)">{{ number_format($product->sold_count, 0, ',', '.') }} terjual</div>
                    </div>
                    <div style="font-size:.8rem;font-weight:600;color:#93b4f5;white-space:nowrap">
                        Rp {{ number_format($product->price,0,',','.') }}
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:28px;color:rgba(232,237,245,.2);font-size:.82rem">
                    <i class="bi bi-bar-chart" style="font-size:1.5rem;display:block;margin-bottom:8px;opacity:.3"></i>
                    Belum ada data
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.dash-action-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: 1px solid rgba(255,255,255,.07);
    text-decoration: none;
    transition: all .2s;
    flex: 1;
    min-width: 200px;
    background: rgba(255,255,255,.02);
}
.dash-action-card:hover { background: rgba(255,255,255,.05); transform: translateY(-1px); }
.dash-action-amber:hover { border-color: rgba(245,158,11,.3); }
.dash-action-blue:hover  { border-color: rgba(59,111,232,.3); }
.dash-action-purple:hover{ border-color: rgba(139,92,246,.3); }
.dash-action-icon {
    width: 38px; height: 38px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.dash-action-title { font-size: .83rem; font-weight: 600; color: #e8edf5; margin-bottom: 2px; }
.dash-action-sub   { font-size: .72rem; color: rgba(232,237,245,.35); }

/* List row hover — diganti dari inline onmouseover/onmouseout ke CSS murni */
.dash-list-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 18px;
    border-bottom: 1px solid rgba(255,255,255,.04);
    transition: background .15s;
}
.dash-list-row:hover { background: rgba(59,111,232,.04); }
</style>
@endpush

@push('scripts')
<script>
// Counter animation for stat values
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.adm-stat-value').forEach(el => {
        const text = el.textContent.trim();
        const hasRp = text.startsWith('Rp');
        const num = parseFloat(text.replace(/[^0-9]/g,''));
        if (isNaN(num) || num === 0) return;
        let start = 0;
        const duration = 900;
        const step = timestamp => {
            if (!start) start = timestamp;
            const progress = Math.min((timestamp - start) / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(ease * num);
            if (hasRp) {
                el.textContent = 'Rp ' + current.toLocaleString('id-ID');
            } else {
                el.textContent = current.toLocaleString('id-ID');
            }
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = text; // restore original with formatting
        };
        requestAnimationFrame(step);
    });
});
</script>
@endpush
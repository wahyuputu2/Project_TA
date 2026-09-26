@extends('layouts.admin')
@section('title','Dashboard')
@section('content')

{{-- PAGE HEADER --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:16px">
    <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
            <div style="width:4px;height:28px;background:var(--gold);border-radius:2px"></div>
            <h1 style="font-family:'Merriweather',serif;font-size:1.55rem;font-weight:700;color:var(--text);margin:0">
                Selamat datang, {{ Auth::user()->name }} 👋
            </h1>
        </div>
        <p style="font-size:.82rem;color:var(--muted);margin:0;padding-left:14px">
            <i class="bi bi-clock me-1" style="opacity:.6"></i>
            {{ now()->format('l, d F Y') }} &nbsp;·&nbsp; Overview platform <strong style="color:var(--text)">Frog Market</strong>
        </p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        @if($pendingStores > 0)
        <a href="{{ route('admin.stores.index', ['status'=>'pending']) }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-shop-window"></i> Verifikasi Toko
            <span style="background:rgba(0,0,0,.2);padding:1px 7px;font-size:.72rem;font-weight:800;border-radius:4px">{{ $pendingStores }}</span>
        </a>
        @endif
        @if($pendingOrders > 0)
        <a href="{{ route('admin.orders.index', ['status'=>'paid']) }}" class="adm-btn adm-btn-secondary">
            <i class="bi bi-bag-check"></i> Proses Pesanan
            <span style="background:var(--gold-dim);color:var(--gold);padding:1px 7px;font-size:.72rem;font-weight:800;border-radius:4px">{{ $pendingOrders }}</span>
        </a>
        @endif
    </div>
</div>

{{-- STAT CARDS ROW 1 --}}
<div class="row g-3 mb-3">
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat" style="--stat-color: var(--gold)">
            <i class="bi bi-tags-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Total Produk</div>
            <div class="adm-stat-value">{{ number_format($totalProducts) }}</div>
            <div class="adm-stat-sub">
                <span style="color:#34d399">{{ $availableProducts }}</span> tersedia ·
                <span style="color:#f87171">{{ $soldProducts }}</span> terjual
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat" style="--stat-color: var(--gold)">
            <i class="bi bi-bag-check-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Total Pesanan</div>
            <div class="adm-stat-value">{{ number_format($totalOrders) }}</div>
            <div class="adm-stat-sub">
                <span style="color:var(--gold)">{{ $pendingOrders }}</span> perlu diproses
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat" style="--stat-color: var(--gold)">
            <i class="bi bi-currency-dollar adm-stat-icon"></i>
            <div class="adm-stat-label">Total Pendapatan</div>
            <div class="adm-stat-value" style="font-size:1.3rem">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
            <div class="adm-stat-sub">Dari pesanan selesai</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat" style="--stat-color: var(--gold)">
            <i class="bi bi-people-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Total User</div>
            <div class="adm-stat-value">{{ number_format($totalUsers) }}</div>
            <div class="adm-stat-sub">
                <span style="color:#34d399">{{ $totalSellers }}</span> seller ·
                <span style="color:#60a5fa">{{ $totalCustomers }}</span> pembeli
            </div>
        </div>
    </div>
</div>

{{-- STAT CARDS ROW 2 --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat" style="--stat-color: var(--gold)">
            <i class="bi bi-shop-window adm-stat-icon"></i>
            <div class="adm-stat-label">Total Toko</div>
            <div class="adm-stat-value">{{ $totalStores }}</div>
            <div class="adm-stat-sub">
                <span style="color:var(--gold)">{{ $pendingStores }}</span> pending verifikasi
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat" style="--stat-color: var(--gold)">
            <i class="bi bi-star-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Ulasan Masuk</div>
            <div class="adm-stat-value">{{ $totalReviews }}</div>
            <div class="adm-stat-sub">
                <span style="color:var(--gold)">{{ $pendingReviews }}</span> menunggu moderasi
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat" style="--stat-color: var(--gold)">
            <i class="bi bi-cash-coin adm-stat-icon"></i>
            <div class="adm-stat-label">Pending Withdraw</div>
            <div class="adm-stat-value" style="font-size:1.3rem">Rp {{ number_format($pendingWithdraw,0,',','.') }}</div>
            <div class="adm-stat-sub">{{ $pendingWithdrawCount }} permintaan</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="adm-stat" style="--stat-color: var(--gold)">
            <i class="bi bi-box-seam-fill adm-stat-icon"></i>
            <div class="adm-stat-label">Butuh Diproses</div>
            <div class="adm-stat-value" style="color:var(--gold)">{{ $needProcess }}</div>
            <div class="adm-stat-sub">Pesanan paid belum dikemas</div>
        </div>
    </div>
</div>

{{-- ACTION NEEDED BANNER --}}
@if($pendingStores > 0 || $pendingOrders > 0 || $pendingReviews > 0)
<div style="background:var(--gold-dim);border:1px solid var(--gold-bdr);border-left:3px solid var(--gold);padding:16px 20px;margin-bottom:28px;border-radius:var(--radius)">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
        <i class="bi bi-bell-fill" style="color:var(--gold);font-size:1rem"></i>
        <span style="font-size:.83rem;font-weight:600;color:var(--text)">Perlu Tindakan Segera</span>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap">
        @if($pendingStores > 0)
        <a href="{{ route('admin.stores.index', ['status'=>'pending']) }}" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(251,191,36,.12);color:#fbbf24">
                <i class="bi bi-shop-window"></i>
            </div>
            <div>
                <div class="dash-action-title">{{ $pendingStores }} Toko Pending</div>
                <div class="dash-action-sub">Klik untuk verifikasi →</div>
            </div>
        </a>
        @endif
        @if($pendingOrders > 0)
        <a href="{{ route('admin.orders.index', ['status'=>'paid']) }}" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(96,165,250,.12);color:#60a5fa">
                <i class="bi bi-bag-check-fill"></i>
            </div>
            <div>
                <div class="dash-action-title">{{ $pendingOrders }} Pesanan Dibayar</div>
                <div class="dash-action-sub">Proses pengiriman →</div>
            </div>
        </a>
        @endif
        @if($pendingReviews > 0)
        <a href="{{ route('admin.reviews.index', ['status'=>'pending']) }}" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(167,139,250,.12);color:#a78bfa">
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
                                <a href="{{ route('admin.orders.show',$order) }}" style="color:var(--gold);font-weight:600;text-decoration:none;font-size:.82rem;font-family:monospace">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px">
                                    <div style="width:28px;height:28px;background:var(--gold-dim);border:1px solid var(--gold-bdr);color:var(--gold);font-size:.65rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:50%">
                                        {{ strtoupper(substr($order->user->name,0,1)) }}
                                    </div>
                                    <span style="color:var(--text);font-size:.84rem">{{ $order->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($order->store)
                                <span class="adm-badge adm-badge-gold" style="font-size:.63rem">{{ $order->store->name }}</span>
                                @else
                                <span style="color:var(--faint)">—</span>
                                @endif
                            </td>
                            <td style="font-weight:600;color:var(--text)">Rp {{ number_format($order->total,0,',','.') }}</td>
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
                            <td style="font-size:.75rem;color:var(--faint)">
                                {{ $order->created_at->format('d M Y') }}<br>
                                <span style="font-size:.68rem">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px;color:var(--faint)">
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
                    <div style="width:34px;height:34px;background:var(--gold-dim);border:1px solid var(--gold-bdr);color:var(--gold);font-size:.78rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:8px">
                        {{ strtoupper(substr($store->name,0,1)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.84rem;font-weight:500;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $store->name }}</div>
                        <div style="font-size:.71rem;color:var(--faint)">{{ $store->user->name }}</div>
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
                <div style="text-align:center;padding:28px;color:var(--faint);font-size:.82rem">
                    <i class="bi bi-shop" style="font-size:1.5rem;display:block;margin-bottom:8px;opacity:.3"></i>
                    Belum ada toko
                </div>
                @endforelse
            </div>
        </div>

        {{-- TOP PRODUCTS --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <i class="bi bi-fire" style="color:var(--gold)"></i> Produk Terlaris
            </div>
            <div>
                @forelse($topProducts as $i => $product)
                @php
                    $mainImage = $product->mainImage();
                @endphp
                <div class="dash-list-row">
                    <span style="font-size:.7rem;font-weight:800;color:{{ $i === 0 ? 'var(--gold)' : ($i === 1 ? '#94a3b8' : ($i === 2 ? '#c97c50' : 'var(--faint)')) }};width:18px;text-align:center;flex-shrink:0">
                        #{{ $i+1 }}
                    </span>
                    @if($mainImage)
                    <img src="{{ Storage::url($mainImage->image_path) }}"
                         alt="{{ $product->name }}"
                         style="width:36px;height:46px;object-fit:cover;flex-shrink:0;border:1px solid var(--border);border-radius:6px">
                    @else
                    <div style="width:36px;height:46px;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--faint);font-size:.9rem">
                        <i class="bi bi-image"></i>
                    </div>
                    @endif
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.82rem;font-weight:500;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $product->name }}</div>
                        <div style="font-size:.68rem;color:var(--faint)">{{ number_format($product->sold_count, 0, ',', '.') }} terjual</div>
                    </div>
                    <div style="font-size:.8rem;font-weight:600;color:var(--gold);white-space:nowrap">
                        Rp {{ number_format($product->price,0,',','.') }}
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:28px;color:var(--faint);font-size:.82rem">
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
    border: 1px solid var(--border);
    border-radius: 8px;
    text-decoration: none;
    transition: all .2s;
    flex: 1;
    min-width: 200px;
    background: rgba(255,255,255,.02);
}
.dash-action-card:hover {
    background: rgba(255,255,255,.05);
    border-color: var(--gold-bdr);
    transform: translateY(-1px);
}
.dash-action-icon {
    width: 38px; height: 38px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.dash-action-title { font-size: .83rem; font-weight: 600; color: var(--text); margin-bottom: 2px; }
.dash-action-sub   { font-size: .72rem; color: var(--faint); }

.dash-list-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 18px;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.dash-list-row:hover { background: rgba(255,255,255,.02); }
.dash-list-row:last-child { border-bottom: none; }
</style>
@endpush

@push('scripts')
<script>
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
            else el.textContent = text;
        };
        requestAnimationFrame(step);
    });
});
</script>
@endpush
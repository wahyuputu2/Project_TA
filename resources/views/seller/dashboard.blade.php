@extends('layouts.seller')
@section('title','Dashboard Seller')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;margin-bottom:4px">
            Dashboard Toko
        </h4>
        <p style="font-size:.82rem;color:var(--muted);margin:0">
            {{ $store->name }} · <span style="color:{{ $store->isActive() ? 'green' : 'orange' }}">{{ $store->statusLabel() }}</span>
        </p>
    </div>
    <a href="{{ route('seller.products.create') }}" class="btn-gold">
        <i class="bi bi-plus-lg me-2"></i>Upload Produk
    </a>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="sl-stat">
            <div class="sl-stat-label">Saldo Tersedia</div>
            <div class="sl-stat-value" style="color:green">Rp {{ number_format($currentBalance,0,',','.') }}</div>
            <a href="{{ route('seller.finance') }}" style="font-size:.75rem;color:var(--muted)">Tarik Dana →</a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sl-stat">
            <div class="sl-stat-label">Pending Cair</div>
            <div class="sl-stat-value" style="color:orange">Rp {{ number_format($pendingBalance,0,',','.') }}</div>
            <div style="font-size:.72rem;color:var(--muted)">Setelah order selesai</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sl-stat">
            <div class="sl-stat-label">Total Pesanan</div>
            <div class="sl-stat-value">{{ $totalOrders }}</div>
            <div style="font-size:.72rem;color:orange">{{ $pendingOrders }} perlu diproses</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sl-stat">
            <div class="sl-stat-label">Produk Tersedia</div>
            <div class="sl-stat-value">{{ $availableProducts }}</div>
            <div style="font-size:.72rem;color:var(--muted)">{{ $soldProducts }} terjual</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Recent Orders --}}
    <div class="col-lg-7">
        <div class="sl-card">
            <div class="sl-card-head d-flex justify-content-between">
                <span>Pesanan Terbaru</span>
                <a href="{{ route('seller.orders.index') }}" style="font-size:.78rem;color:var(--muted)">Lihat Semua</a>
            </div>
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th style="padding:10px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">No. Order</th>
                        <th style="padding:10px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Pembeli</th>
                        <th style="padding:10px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Total</th>
                        <th style="padding:10px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr style="border-bottom:1px solid var(--border)">
                        <td style="padding:12px 16px;font-size:.82rem;font-weight:600">{{ $order->order_number }}</td>
                        <td style="padding:12px 16px;font-size:.82rem">{{ $order->user->name }}</td>
                        <td style="padding:12px 16px;font-size:.82rem">Rp {{ number_format($order->total,0,',','.') }}</td>
                        <td style="padding:12px 16px">
                            <span style="font-size:.72rem;font-weight:600;padding:3px 8px;border-radius:4px;background:#f3f3f3">
                                {{ $order->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:24px;text-align:center;color:var(--muted);font-size:.85rem">Belum ada pesanan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="col-lg-5">
        <div class="sl-card">
            <div class="sl-card-head">Produk Terlaris</div>
            <div style="padding:16px">
                @forelse($topProducts as $i => $product)
                <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="font-size:.75rem;font-weight:700;color:var(--muted);width:20px">{{ $i+1 }}</span>
                    @if($product->mainImage())
                    <img src="{{ Storage::url($product->mainImage()->image_path) }}"
                         style="width:40px;height:52px;object-fit:cover;border-radius:4px">
                    @endif
                    <div style="flex:1">
                        <div style="font-size:.82rem;font-weight:500">{{ Str::limit($product->name, 30) }}</div>
                        <div style="font-size:.72rem;color:var(--muted)">{{ $product->sold_count }} terjual</div>
                    </div>
                    <div style="font-size:.82rem;font-weight:600">Rp {{ number_format($product->price,0,',','.') }}</div>
                </div>
                @empty
                <p style="text-align:center;color:var(--muted);font-size:.85rem;padding:20px 0">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
@push('styles')
<style>
.sl-stat { background:white;border:1.5px solid var(--border);border-radius:10px;padding:18px 20px }
.sl-stat-label { font-size:.72rem;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);margin-bottom:8px }
.sl-stat-value { font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:var(--primary);line-height:1;margin-bottom:5px }
.sl-card { background:white;border:1.5px solid var(--border);border-radius:10px;overflow:hidden }
.sl-card-head { padding:14px 16px;border-bottom:1px solid var(--border);font-size:.88rem;font-weight:600;color:var(--primary);display:flex;align-items:center;justify-content:space-between }
</style>
@endpush
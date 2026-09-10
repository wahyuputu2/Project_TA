@extends('layouts.seller')
@section('title', 'Pesanan Masuk')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-weight:700;margin-bottom:4px">Pesanan Masuk</h4>
        <p style="font-size:.82rem;color:var(--muted);margin:0">Kelola & proses pesanan dari pembeli</p>
    </div>
</div>

{{-- FILTER TABS --}}
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:20px">
    @php
        $tabs = [
            ''                => 'Semua',
            'pending_payment' => 'Menunggu Bayar',
            'paid'            => 'Dibayar',
            'processing'      => 'Dikemas',
            'shipped'         => 'Dikirim',
            'delivered'       => 'Selesai',
            'cancelled'       => 'Dibatalkan',
        ];
    @endphp
    @foreach($tabs as $val => $lbl)
    <a href="{{ route('seller.orders.index', ['status' => $val]) }}"
       style="display:inline-block;padding:7px 16px;border-radius:8px;font-size:.78rem;text-decoration:none;border:1px solid var(--border);transition:all .18s;
              {{ request('status','') === $val ? 'background:var(--primary);color:white;border-color:var(--primary);font-weight:600' : 'background:white;color:var(--muted)' }}">
        {{ $lbl }}
    </a>
    @endforeach
</div>

{{-- FILTER SEARCH --}}
<div style="background:white;border:1.5px solid var(--border);border-radius:10px;padding:14px 18px;margin-bottom:20px">
    <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <div style="flex:1;min-width:200px">
            <input type="text" name="search" class="sp-input"
                   placeholder="Cari nomor order atau nama pembeli..."
                   value="{{ request('search') }}" style="padding:8px 14px">
        </div>
        <button type="submit" class="btn-gold" style="padding:9px 18px">
            <i class="bi bi-search me-1"></i>Cari
        </button>
        <a href="{{ route('seller.orders.index') }}"
           style="padding:9px 14px;border:1px solid var(--border);border-radius:6px;color:var(--muted);text-decoration:none;font-size:.85rem">
            Reset
        </a>
    </form>
</div>

{{-- TABEL --}}
<div style="background:white;border:1.5px solid var(--border);border-radius:10px;overflow:hidden">
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead style="background:#f8f8f6">
                <tr>
                    <th style="padding:12px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left;white-space:nowrap">No. Order</th>
                    <th style="padding:12px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Pembeli</th>
                    <th style="padding:12px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Produk</th>
                    <th style="padding:12px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Total</th>
                    <th style="padding:12px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Kurir</th>
                    <th style="padding:12px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Status</th>
                    <th style="padding:12px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Tanggal</th>
                    <th style="padding:12px 16px;font-size:.7rem;color:var(--muted);font-weight:600;text-align:left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr style="border-top:1px solid var(--border)">
                    <td style="padding:14px 16px">
                        <div style="font-weight:700;font-size:.85rem;color:var(--primary)">{{ $order->order_number }}</div>
                        @if($order->tracking_number)
                        <div style="font-size:.7rem;color:var(--muted);margin-top:2px">
                            <i class="bi bi-truck me-1"></i>{{ $order->tracking_number }}
                        </div>
                        @endif
                    </td>
                    <td style="padding:14px 16px">
                        <div style="font-size:.85rem;font-weight:500;color:var(--primary)">{{ $order->user->name }}</div>
                        <div style="font-size:.72rem;color:var(--muted)">{{ $order->shipping_city }}</div>
                    </td>
                    <td style="padding:14px 16px">
                        <div style="font-size:.82rem;color:var(--primary)">
                            {{ $order->items->first()?->product_name ?? '-' }}
                        </div>
                        @if($order->items->count() > 1)
                        <div style="font-size:.7rem;color:var(--muted)">+{{ $order->items->count() - 1 }} item lagi</div>
                        @endif
                    </td>
                    <td style="padding:14px 16px">
                        <div style="font-weight:600;font-size:.88rem;color:var(--primary)">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </div>
                        <div style="font-size:.7rem;color:var(--muted)">+Rp {{ number_format($order->shipping_cost,0,',','.') }} ongkir</div>
                    </td>
                    <td style="padding:14px 16px">
                        <div style="font-size:.8rem;color:var(--primary)">{{ strtoupper($order->courier ?? '-') }}</div>
                        <div style="font-size:.7rem;color:var(--muted)">{{ $order->courier_service }}</div>
                    </td>
                    <td style="padding:14px 16px">
                        @php
                            $colors = [
                                'pending_payment' => ['#d97706','#fffbeb','#fde68a'],
                                'paid'            => ['#2563eb','#eff6ff','#bfdbfe'],
                                'processing'      => ['#d97706','#fffbeb','#fde68a'],
                                'shipped'         => ['#7c3aed','#f5f3ff','#ddd6fe'],
                                'delivered'       => ['#16a34a','#f0fdf4','#bbf7d0'],
                                'cancelled'       => ['#dc2626','#fef2f2','#fecaca'],
                                'refunded'        => ['#6b7280','#f9fafb','#e5e7eb'],
                            ];
                            [$clr,$bg,$border] = $colors[$order->status] ?? ['#6b7280','#f9fafb','#e5e7eb'];
                        @endphp
                        <span style="display:inline-block;padding:4px 10px;border-radius:20px;font-size:.7rem;font-weight:600;color:{{ $clr }};background:{{ $bg }};border:1px solid {{ $border }}">
                            {{ $order->statusLabel() }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;font-size:.75rem;color:var(--muted)">
                        {{ $order->created_at->format('d M Y') }}<br>
                        <span style="font-size:.68rem">{{ $order->created_at->format('H:i') }}</span>
                    </td>
                    <td style="padding:14px 16px">
                        <a href="{{ route('seller.orders.show', $order) }}"
                           class="btn-gold" style="padding:7px 14px;font-size:.75rem;white-space:nowrap">
                            Kelola
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:56px;color:var(--muted)">
                        <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:12px;opacity:.3"></i>
                        <div style="font-size:.95rem;font-weight:500">Belum ada pesanan</div>
                        <div style="font-size:.8rem;margin-top:4px;color:var(--faint)">Pesanan akan muncul di sini saat pembeli checkout</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border)">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>

@endsection
@push('styles')
<style>
.sp-input{width:100%;background:#fafafa;border:1.5px solid var(--border);border-radius:8px;padding:10px 14px;font-size:.88rem;color:var(--primary);font-family:'Jost',sans-serif;outline:none;transition:border-color .2s}
.sp-input:focus{border-color:var(--primary)}
</style>
@endpush
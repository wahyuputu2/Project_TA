@extends('layouts.admin')
@section('title', 'Manajemen Pesanan')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:#f0ece4;margin-bottom:4px">
            Manajemen Pesanan
        </h4>
        <p style="font-size:.82rem;color:rgba(240,236,228,.35);margin:0">
            Pantau seluruh transaksi &mdash; pengelolaan pesanan dilakukan oleh Seller
        </p>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="adm-card mb-4">
    <div class="adm-card-body" style="padding:16px 20px">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <div style="flex:2;min-width:200px">
                <label class="ord-label">Cari No. Order / Nama</label>
                <input type="text" name="search" class="adm-input" placeholder="TF-XXXXXXXXXX..."
                       value="{{ request('search') }}" style="padding:9px 14px">
            </div>
            <div style="flex:1;min-width:150px">
                <label class="ord-label">Status</label>
                <select name="status" class="adm-select" style="padding:9px 14px">
                    <option value="">Semua Status</option>
                    @foreach(['pending_payment'=>'Menunggu Bayar','paid'=>'Dibayar','processing'=>'Dikemas','shipped'=>'Dikirim','delivered'=>'Selesai','cancelled'=>'Dibatalkan','refunded'=>'Dikembalikan'] as $val=>$lbl)
                    <option value="{{ $val }}" @selected(request('status')===$val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="adm-btn-primary" style="padding:9px 18px">
                    <i class="bi bi-search"></i> Cari
                </button>
                <a href="{{ route('admin.orders.index') }}" class="adm-btn-secondary" style="padding:9px 14px">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- STATUS TABS --}}
<div class="ord-tabs mb-3">
    @php
        $statuses = [
            ''                => ['Semua', \App\Models\Order::count()],
            'pending_payment' => ['Menunggu Bayar', \App\Models\Order::where('status','pending_payment')->count()],
            'paid'            => ['Dibayar', \App\Models\Order::where('status','paid')->count()],
            'processing'      => ['Dikemas', \App\Models\Order::where('status','processing')->count()],
            'shipped'         => ['Dikirim', \App\Models\Order::where('status','shipped')->count()],
            'delivered'       => ['Selesai', \App\Models\Order::where('status','delivered')->count()],
            'cancelled'       => ['Batal', \App\Models\Order::where('status','cancelled')->count()],
        ];
    @endphp
    @foreach($statuses as $val => [$lbl, $count])
    <a href="{{ route('admin.orders.index', array_merge(request()->all(), ['status' => $val])) }}"
       class="ord-tab {{ request('status', '') === $val ? 'active' : '' }}">
        {{ $lbl }}
        @if($count > 0)<span class="ord-tab-count">{{ $count }}</span>@endif
    </a>
    @endforeach
</div>

{{-- TABLE --}}
<div class="adm-card">
    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>No. Order</th>
                    <th>Pembeli</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Kurir</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <div style="font-weight:600;color:#f0ece4;font-size:.85rem">{{ $order->order_number }}</div>
                        @if($order->tracking_number)
                        <div style="font-size:.72rem;color:#c9a96e;margin-top:2px">
                            <i class="bi bi-truck me-1"></i>{{ $order->tracking_number }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:.85rem;color:#f0ece4">{{ $order->user->name }}</div>
                        <div style="font-size:.72rem;color:rgba(240,236,228,.35)">{{ $order->user->email }}</div>
                    </td>
                    <td>
                        <div style="font-size:.82rem;color:rgba(240,236,228,.6)">
                            {{ $order->items->first()?->product_name ?? '-' }}
                            @if($order->items->count() > 1)
                            <span style="color:#c9a96e">+{{ $order->items->count() - 1 }} item</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:600;color:#f0ece4;font-size:.88rem">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </div>
                        <div style="font-size:.7rem;color:rgba(240,236,228,.3)">
                            + Rp {{ number_format($order->shipping_cost, 0, ',', '.') }} ongkir
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.8rem;color:rgba(240,236,228,.6)">
                            {{ strtoupper($order->courier ?? '-') }}
                        </div>
                        <div style="font-size:.7rem;color:rgba(240,236,228,.3)">{{ $order->courier_service }}</div>
                    </td>
                    <td>
                        @php
                            $colors = [
                                'pending_payment' => ['#facc15','rgba(234,179,8,.1)'],
                                'paid'            => ['#60a5fa','rgba(59,130,246,.1)'],
                                'processing'      => ['#c9a96e','rgba(201,169,110,.1)'],
                                'shipped'         => ['#a78bfa','rgba(139,92,246,.1)'],
                                'delivered'       => ['#4ade80','rgba(34,197,94,.1)'],
                                'cancelled'       => ['#f87171','rgba(239,68,68,.1)'],
                                'refunded'        => ['rgba(240,236,228,.4)','rgba(255,255,255,.05)'],
                            ];
                            [$clr, $bg] = $colors[$order->status] ?? ['rgba(240,236,228,.4)','rgba(255,255,255,.05)'];
                        @endphp
                        <span style="display:inline-block;padding:4px 10px;border-radius:20px;font-size:.7rem;font-weight:600;background:{{ $bg }};color:{{ $clr }};border:1px solid {{ $clr }}40">
                            {{ $order->statusLabel() }}
                        </span>
                    </td>
                    <td style="font-size:.78rem;color:rgba(240,236,228,.4)">
                        {{ $order->created_at->format('d M Y') }}<br>
                        <span style="font-size:.7rem">{{ $order->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="adm-btn-secondary" style="padding:7px 14px;font-size:.78rem">
                            <i class="bi bi-eye me-1"></i>Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:rgba(240,236,228,.25)">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:10px"></i>
                        Belum ada pesanan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid rgba(255,255,255,.06)">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>

@endsection
@push('styles')
<style>
.ord-label { display:block;font-size:.68rem;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:7px }
.ord-tabs { display:flex;gap:4px;flex-wrap:wrap }
.ord-tab { display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:8px;font-size:.82rem;color:rgba(240,236,228,.45);text-decoration:none;border:1px solid rgba(255,255,255,.06);background:transparent;transition:all .18s }
.ord-tab:hover { color:#f0ece4;border-color:rgba(255,255,255,.15) }
.ord-tab.active { background:rgba(201,169,110,.12);border-color:rgba(201,169,110,.25);color:#c9a96e;font-weight:500 }
.ord-tab-count { background:rgba(255,255,255,.08);color:inherit;font-size:.65rem;font-weight:700;padding:1px 6px;border-radius:10px }
.ord-tab.active .ord-tab-count { background:rgba(201,169,110,.2) }
</style>
@endpush
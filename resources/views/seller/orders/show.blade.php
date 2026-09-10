@extends('layouts.seller')
@section('title', 'Detail Pesanan')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <a href="{{ route('seller.orders.index') }}"
           style="font-size:.82rem;color:var(--muted);text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:8px">
            ← Kembali ke Pesanan
        </a>
        <h4 style="font-weight:700;margin-bottom:4px">{{ $order->order_number }}</h4>
        <p style="font-size:.82rem;color:var(--muted);margin:0">
            {{ $order->created_at->format('d M Y, H:i') }}
        </p>
    </div>
    @php
        $colors = [
            'pending_payment' => ['#d97706','#fffbeb','#fde68a'],
            'paid'            => ['#2563eb','#eff6ff','#bfdbfe'],
            'processing'      => ['#d97706','#fffbeb','#fde68a'],
            'shipped'         => ['#7c3aed','#f5f3ff','#ddd6fe'],
            'delivered'       => ['#16a34a','#f0fdf4','#bbf7d0'],
            'cancelled'       => ['#dc2626','#fef2f2','#fecaca'],
        ];
        [$clr,$bg,$border] = $colors[$order->status] ?? ['#6b7280','#f9fafb','#e5e7eb'];
    @endphp
    <span style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:20px;font-size:.82rem;font-weight:600;color:{{ $clr }};background:{{ $bg }};border:1px solid {{ $border }}">
        {{ $order->statusLabel() }}
    </span>
</div>

<div class="row g-4">

    {{-- KIRI --}}
    <div class="col-lg-8">

        {{-- UPDATE STATUS --}}
        @if(in_array($order->status, ['paid','processing']))
        <div class="so-card mb-4">
            <div class="so-card-head">✏️ Update Status Pesanan</div>
            <div class="so-card-body">
                <form action="{{ route('seller.orders.update', $order) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="so-label">Ubah Status</label>
                            <select name="status" class="sp-input so-select" id="statusSelect"
                                    onchange="toggleResi(this.value)" style="padding:9px 12px">
                                <option value="">— Pilih Status —</option>
                                @if($order->status === 'paid')
                                    <option value="processing">📦 Mulai Kemas</option>
                                @endif
                                @if(in_array($order->status, ['paid','processing']))
                                    <option value="shipped">🚚 Tandai Dikirim</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-5" id="resiField" style="display:none">
                            <label class="so-label">No. Resi <span style="color:red">*</span></label>
                            <input type="text" name="tracking_number" class="sp-input"
                                   placeholder="Contoh: JNE123456789ID"
                                   value="{{ $order->tracking_number }}"
                                   style="padding:9px 14px">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn-gold w-100" style="padding:10px;justify-content:center">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Update resi saja --}}
                @if($order->status === 'shipped')
                <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
                    <form action="{{ route('seller.orders.update', $order) }}" method="POST" class="d-flex gap-3">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="{{ $order->status }}">
                        <div style="flex:1">
                            <label class="so-label">Update No. Resi</label>
                            <input type="text" name="tracking_number" class="sp-input"
                                   placeholder="Masukkan nomor resi..."
                                   value="{{ $order->tracking_number }}" style="padding:9px 14px">
                        </div>
                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn-gold" style="padding:10px 18px">Simpan Resi</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- RESI (jika sudah dikirim) --}}
        @if($order->tracking_number && in_array($order->status, ['shipped','delivered']))
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:10px;padding:16px 20px;margin-bottom:20px">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
                <div>
                    <div style="font-size:.7rem;color:#16a34a;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">Nomor Resi</div>
                    <div style="font-size:1.1rem;font-weight:700;color:#15803d;letter-spacing:1px">{{ $order->tracking_number }}</div>
                    <div style="font-size:.75rem;color:#16a34a;margin-top:2px">{{ strtoupper($order->courier) }} · {{ $order->courier_service }}</div>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}').then(()=>alert('Resi disalin!'))"
                        style="background:#16a34a;border:none;border-radius:7px;padding:8px 16px;color:white;font-size:.78rem;font-weight:600;cursor:pointer">
                    <i class="bi bi-copy me-1"></i>Salin
                </button>
            </div>
        </div>
        @endif

        {{-- ITEM PESANAN --}}
        <div class="so-card mb-4">
            <div class="so-card-head">🛍️ Item Pesanan</div>
            <div style="padding:0">
                @foreach($order->items as $item)
                <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid var(--border)">
                    @if($item->product && $item->product->mainImage())
                    <img src="{{ Storage::url($item->product->mainImage()->image_path) }}"
                         style="width:56px;height:74px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
                    @else
                    <div style="width:56px;height:74px;background:#f8f8f6;border-radius:8px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--faint)">
                        <i class="bi bi-image"></i>
                    </div>
                    @endif
                    <div style="flex:1">
                        <div style="font-size:.9rem;font-weight:600;color:var(--primary)">{{ $item->product_name }}</div>
                        <div style="font-size:.75rem;color:var(--muted);margin-top:3px">
                            Size {{ $item->product_size ?? '-' }} · {{ $item->product_condition }}
                        </div>
                    </div>
                    <div style="font-size:.95rem;font-weight:700;color:var(--primary)">
                        Rp {{ number_format($item->price, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach

                <div style="padding:14px 20px;background:#f8f8f6">
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;color:var(--muted);margin-bottom:5px">
                        <span>Subtotal Produk</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;color:var(--muted);margin-bottom:10px">
                        <span>Ongkos Kirim</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.95rem;font-weight:700;color:var(--primary);padding-top:10px;border-top:1px solid var(--border)">
                        <span>Total</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- KANAN --}}
    <div class="col-lg-4">

        {{-- INFO PEMBELI --}}
        <div class="so-card mb-4">
            <div class="so-card-head">👤 Info Pembeli</div>
            <div class="so-card-body">
                <div class="so-info-row">
                    <span>Nama</span>
                    <strong>{{ $order->user->name }}</strong>
                </div>
                <div class="so-info-row">
                    <span>No. HP</span>
                    <strong>{{ $order->shipping_phone }}</strong>
                </div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$order->shipping_phone) }}?text=Halo+{{ urlencode($order->user->name) }},+terkait+pesanan+{{ $order->order_number }}"
                   target="_blank"
                   style="display:flex;align-items:center;justify-content:center;gap:7px;background:#25d366;border:none;border-radius:8px;padding:10px;color:white;text-decoration:none;font-size:.82rem;font-weight:600;margin-top:12px;transition:background .2s"
                   onmouseover="this.style.background='#1da851'"
                   onmouseout="this.style.background='#25d366'">
                    <i class="bi bi-whatsapp"></i>Chat Pembeli
                </a>
            </div>
        </div>

        {{-- ALAMAT PENGIRIMAN --}}
        <div class="so-card mb-4">
            <div class="so-card-head">📍 Alamat Pengiriman</div>
            <div class="so-card-body">
                <div style="font-size:.88rem;font-weight:600;color:var(--primary);margin-bottom:3px">{{ $order->shipping_name }}</div>
                <div style="font-size:.82rem;color:var(--muted);line-height:1.7">
                    {{ $order->shipping_address }},<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_province }}<br>
                    {{ $order->shipping_postal_code }}
                </div>
                <div style="margin-top:10px;padding-top:10px;border-top:1px solid var(--border)">
                    <div style="font-size:.78rem;color:var(--primary);font-weight:600">
                        {{ strtoupper($order->courier) }} {{ $order->courier_service }}
                    </div>
                </div>
            </div>
        </div>

        {{-- PEMBAYARAN --}}
        <div class="so-card">
            <div class="so-card-head">💳 Info Pembayaran</div>
            <div class="so-card-body">
                <div class="so-info-row">
                    <span>Status</span>
                    <strong style="color:{{ $order->paid_at ? '#16a34a' : '#d97706' }}">
                        {{ $order->paid_at ? 'Lunas' : 'Belum Dibayar' }}
                    </strong>
                </div>
                @if($order->paid_at)
                <div class="so-info-row">
                    <span>Dibayar</span>
                    <strong>{{ $order->paid_at->format('d M Y, H:i') }}</strong>
                </div>
                @endif
                @if($order->payment_method)
                <div class="so-info-row">
                    <span>Metode</span>
                    <strong>{{ strtoupper($order->payment_method) }}</strong>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
@push('styles')
<style>
.so-label { display:block;font-size:.7rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:7px }
.so-select { appearance:none;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23999' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px }
.so-card { background:white;border:1.5px solid var(--border);border-radius:10px;overflow:hidden }
.so-card-head { padding:13px 18px;background:#f8f8f6;border-bottom:1px solid var(--border);font-size:.88rem;font-weight:600;color:var(--primary) }
.so-card-body { padding:16px 18px }
.so-info-row { display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--border);font-size:.85rem }
.so-info-row:last-child { border-bottom:none }
.so-info-row span { color:var(--muted) }
.so-info-row strong { color:var(--primary) }
.sp-input { width:100%;background:#fafafa;border:1.5px solid var(--border);border-radius:8px;padding:10px 14px;font-size:.88rem;color:var(--primary);font-family:'Jost',sans-serif;outline:none;transition:border-color .2s }
.sp-input:focus { border-color:var(--primary) }
</style>
@endpush
@push('scripts')
<script>
function toggleResi(val) {
    document.getElementById('resiField').style.display = val === 'shipped' ? 'block' : 'none';
}
</script>
@endpush
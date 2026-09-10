@extends('layouts.app')
@section('title', 'Pesanan Saya')
@section('content')

<div class="container py-5" style="max-width:900px">

    <div class="ord-page-header">
        <div>
            <h3 class="ord-page-title">Pesanan Saya</h3>
            <p class="ord-page-sub">Riwayat & status semua transaksi kamu</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="ord-btn-shop">
            <i class="bi bi-bag-plus me-2"></i>Lanjut Belanja
        </a>
    </div>

    {{-- TABS --}}
    <div class="ord-tabs">
        @foreach(['' => 'Semua','pending_payment' => 'Menunggu Bayar','paid' => 'Dibayar','processing' => 'Dikemas','shipped' => 'Dikirim','delivered' => 'Selesai','cancelled' => 'Dibatalkan'] as $val => $lbl)
        <a href="{{ route('orders.index', ['status' => $val]) }}"
           class="ord-tab {{ request('status','') === $val ? 'active' : '' }}">{{ $lbl }}</a>
        @endforeach
    </div>

    {{-- LIST --}}
    @forelse($orders as $order)
    <div class="ord-card">

        {{-- Head --}}
        <div class="ord-card-head">
            <div class="ord-card-head-left">
                <span class="ord-number">{{ $order->order_number }}</span>
                <span class="ord-date">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            @php
                $ss = match($order->status) {
                    'pending_payment' => ['#facc15','rgba(234,179,8,.1)','rgba(234,179,8,.25)'],
                    'paid'       => ['#60a5fa','rgba(59,130,246,.1)','rgba(59,130,246,.25)'],
                    'processing' => ['#c9a96e','rgba(201,169,110,.1)','rgba(201,169,110,.25)'],
                    'shipped'    => ['#a78bfa','rgba(139,92,246,.1)','rgba(139,92,246,.25)'],
                    'delivered'  => ['#4ade80','rgba(34,197,94,.1)','rgba(34,197,94,.25)'],
                    'cancelled'  => ['#f87171','rgba(239,68,68,.1)','rgba(239,68,68,.25)'],
                    default      => ['rgba(240,236,228,.4)','rgba(255,255,255,.05)','rgba(255,255,255,.1)'],
                };
                $si = match($order->status) {
                    'pending_payment' => 'bi-clock',
                    'paid'       => 'bi-check-circle',
                    'processing' => 'bi-box-seam',
                    'shipped'    => 'bi-truck',
                    'delivered'  => 'bi-bag-check',
                    'cancelled'  => 'bi-x-circle',
                    default      => 'bi-circle',
                };
            @endphp
            <span class="ord-status-badge" style="color:{{ $ss[0] }};background:{{ $ss[1] }};border-color:{{ $ss[2] }}">
                <i class="bi {{ $si }} me-1"></i>{{ $order->statusLabel() }}
            </span>
        </div>

        {{-- Progress --}}
        @if(!in_array($order->status, ['cancelled','refunded']))
        @php
            $steps   = ['pending_payment','paid','processing','shipped','delivered'];
            $curStep = array_search($order->status, $steps);
            $pct     = $curStep !== false ? ($curStep / (count($steps)-1)) * 100 : 0;
        @endphp
        <div class="ord-progress-wrap">
            <div class="ord-progress-track">
                <div class="ord-progress-fill" style="width:{{ $pct }}%"></div>
            </div>
            <div class="ord-progress-steps">
                @foreach(['Bayar','Dikonfirmasi','Dikemas','Dikirim','Selesai'] as $si2 => $slbl)
                <div class="ord-progress-step {{ $curStep >= $si2 ? 'done' : '' }} {{ $curStep === $si2 ? 'current' : '' }}">
                    <div class="ord-prog-dot">
                        @if($curStep > $si2)<i class="bi bi-check2"></i>
                        @elseif($curStep === $si2)<div class="ord-prog-pulse"></div>
                        @endif
                    </div>
                    <span class="ord-prog-lbl">{{ $slbl }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Items --}}
        <div class="ord-items">
            @foreach($order->items->take(2) as $item)
            <div class="ord-item">
                <div class="ord-item-img">
                    @if($item->product && $item->product->mainImage())
                        <img src="{{ Storage::url($item->product->mainImage()->image_path) }}" alt="{{ $item->product_name }}">
                    @else
                        <div class="ord-item-no-img"><i class="bi bi-image"></i></div>
                    @endif
                </div>
                <div class="ord-item-info">
                    <div class="ord-item-name">{{ $item->product_name }}</div>
                    <div class="ord-item-meta">Size {{ $item->product_size ?? '-' }} · {{ $item->product_condition }}</div>
                </div>
                <div class="ord-item-price">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
            </div>
            @endforeach
            @if($order->items->count() > 2)
            <div class="ord-more-items">+{{ $order->items->count() - 2 }} item lainnya</div>
            @endif
        </div>

        {{-- Resi --}}
        @if($order->tracking_number && in_array($order->status, ['shipped','delivered']))
        <div class="ord-resi-bar">
            <div class="ord-resi-info">
                <i class="bi bi-truck me-2" style="color:#c9a96e"></i>
                <span style="color:rgba(240,236,228,.5);font-size:.8rem">No. Resi:</span>
                <span style="color:#c9a96e;font-weight:700;font-size:.88rem;margin-left:6px">{{ $order->tracking_number }}</span>
                <span style="color:rgba(240,236,228,.3);font-size:.75rem;margin-left:6px">· {{ strtoupper($order->courier) }}</span>
            </div>
            <button onclick="copyResi('{{ $order->tracking_number }}')" class="ord-copy-btn">
                <i class="bi bi-copy me-1"></i>Salin
            </button>
        </div>
        @endif

        {{-- Pay alert --}}
        @if($order->status === 'pending_payment' && $order->payment_url)
        <div class="ord-pay-alert">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="bi bi-exclamation-circle-fill" style="color:#facc15;font-size:1.1rem"></i>
                <div>
                    <div style="font-size:.85rem;font-weight:600;color:#f0ece4">Segera Selesaikan Pembayaran</div>
                    <div style="font-size:.75rem;color:rgba(240,236,228,.4);margin-top:2px">Pesanan akan otomatis dibatalkan jika tidak dibayar</div>
                </div>
            </div>
            <a href="{{ $order->payment_url }}" target="_blank" class="ord-pay-btn">
                Bayar Sekarang <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        @endif

        {{-- Footer --}}
        <div class="ord-card-foot">
            <div class="ord-total">
                <span class="ord-total-lbl">Total</span>
                <span class="ord-total-val">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="ord-actions">
                @if($order->status === 'delivered' && !$order->reviews()->where('user_id', Auth::id())->exists())
                <a href="{{ route('orders.show', $order) }}#review" class="ord-btn-review">
                    <i class="bi bi-star me-1"></i>Beri Ulasan
                </a>
                @endif

                @if($order->status === 'pending_payment')
                <button type="button" class="ord-btn-cancel-sm"
                        onclick="confirmCancel('{{ $order->order_number }}', '{{ route('orders.cancel', $order) }}')">
                    <i class="bi bi-x-circle me-1"></i>Batalkan
                </button>
                @endif

                <a href="{{ route('orders.show', $order) }}" class="ord-btn-detail">
                    Detail <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="ord-empty">
        <div class="ord-empty-icon">🛍️</div>
        <h5 class="ord-empty-title">Belum Ada Pesanan</h5>
        <p class="ord-empty-sub">
            @if(request('status')) Tidak ada pesanan dengan status ini.
            @else Kamu belum pernah berbelanja. Yuk mulai!
            @endif
        </p>
        <a href="{{ route('catalog.index') }}" class="ord-btn-shop" style="display:inline-flex">
            <i class="bi bi-bag me-2"></i>Mulai Belanja
        </a>
    </div>
    @endforelse

    @if($orders->hasPages())
    <div class="d-flex justify-content-center mt-4">{{ $orders->withQueryString()->links() }}</div>
    @endif
</div>

{{-- MODAL KONFIRMASI BATALKAN --}}
<div id="cancelModalIndex" class="ord-modal-backdrop" style="display:none">
    <div class="ord-modal">
        <div class="ord-modal-icon">⚠️</div>
        <h5 class="ord-modal-title">Batalkan Pesanan?</h5>
        <p class="ord-modal-sub" id="cancelModalText"></p>
        <div class="ord-modal-actions">
            <button type="button" class="ord-modal-no"
                    onclick="document.getElementById('cancelModalIndex').style.display='none'">
                Tidak, Kembali
            </button>
            <form id="cancelModalForm" method="POST" style="flex:1">
                @csrf
                <button type="submit" class="ord-modal-yes">
                    <i class="bi bi-x-circle me-2"></i>Ya, Batalkan
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
@push('styles')
<style>
.ord-page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.ord-page-title{font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:700;color:#f0ece4;margin:0 0 4px}
.ord-page-sub{font-size:.82rem;color:rgba(240,236,228,.35);margin:0}
.ord-btn-shop{display:inline-flex;align-items:center;background:rgba(201,169,110,.12);border:1px solid rgba(201,169,110,.25);border-radius:9px;padding:9px 18px;font-size:.85rem;font-weight:500;color:#c9a96e;text-decoration:none;transition:all .2s}
.ord-btn-shop:hover{background:rgba(201,169,110,.2);color:#c9a96e}
.ord-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid rgba(255,255,255,.06)}
.ord-tab{display:inline-block;padding:7px 16px;border-radius:8px;font-size:.8rem;color:rgba(240,236,228,.4);text-decoration:none;border:1px solid rgba(255,255,255,.07);transition:all .18s;white-space:nowrap}
.ord-tab:hover{color:#f0ece4;border-color:rgba(255,255,255,.18)}
.ord-tab.active{background:rgba(201,169,110,.1);border-color:rgba(201,169,110,.3);color:#c9a96e;font-weight:500}
.ord-card{background:#151515;border:1px solid rgba(255,255,255,.07);border-radius:14px;overflow:hidden;margin-bottom:16px;transition:border-color .2s}
.ord-card:hover{border-color:rgba(201,169,110,.2)}
.ord-card-head{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:rgba(255,255,255,.02);border-bottom:1px solid rgba(255,255,255,.05);flex-wrap:wrap;gap:8px}
.ord-card-head-left{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.ord-number{font-size:.88rem;font-weight:700;color:#f0ece4;letter-spacing:.3px}
.ord-date{font-size:.75rem;color:rgba(240,236,228,.3)}
.ord-status-badge{display:inline-flex;align-items:center;padding:5px 12px;border-radius:20px;font-size:.75rem;font-weight:600;border:1px solid}
.ord-progress-wrap{padding:18px 20px 14px;border-bottom:1px solid rgba(255,255,255,.05)}
.ord-progress-track{height:3px;background:rgba(255,255,255,.06);border-radius:10px;margin-bottom:10px;position:relative}
.ord-progress-fill{height:100%;background:linear-gradient(90deg,#c9a96e,#e8c98a);border-radius:10px;transition:width .6s ease}
.ord-progress-steps{display:flex;justify-content:space-between}
.ord-progress-step{display:flex;flex-direction:column;align-items:center;gap:6px}
.ord-prog-dot{width:20px;height:20px;border-radius:50%;background:rgba(255,255,255,.06);border:1.5px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;font-size:.65rem;color:transparent;margin-top:-8px;position:relative;z-index:1}
.ord-progress-step.done .ord-prog-dot{background:#c9a96e;border-color:#c9a96e;color:#0e0e0f}
.ord-progress-step.current .ord-prog-dot{border-color:#c9a96e;background:rgba(201,169,110,.15)}
.ord-prog-pulse{width:8px;height:8px;background:#c9a96e;border-radius:50%;animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.7}}
.ord-prog-lbl{font-size:.68rem;color:rgba(240,236,228,.3);white-space:nowrap}
.ord-progress-step.done .ord-prog-lbl,.ord-progress-step.current .ord-prog-lbl{color:rgba(201,169,110,.8)}
.ord-items{padding:4px 0}
.ord-item{display:flex;align-items:center;gap:14px;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,.04)}
.ord-item:last-child{border-bottom:none}
.ord-item-img{width:52px;height:68px;border-radius:8px;overflow:hidden;flex-shrink:0;border:1px solid rgba(255,255,255,.07)}
.ord-item-img img{width:100%;height:100%;object-fit:cover}
.ord-item-no-img{width:100%;height:100%;background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;color:rgba(240,236,228,.2)}
.ord-item-info{flex:1}
.ord-item-name{font-size:.88rem;font-weight:500;color:#f0ece4;line-height:1.3}
.ord-item-meta{font-size:.75rem;color:rgba(240,236,228,.35);margin-top:3px}
.ord-item-price{font-size:.88rem;font-weight:600;color:#f0ece4;flex-shrink:0}
.ord-more-items{padding:8px 20px;font-size:.75rem;color:rgba(240,236,228,.3);text-align:center;background:rgba(255,255,255,.02)}
.ord-resi-bar{display:flex;justify-content:space-between;align-items:center;padding:10px 20px;background:rgba(201,169,110,.05);border-top:1px solid rgba(201,169,110,.1);border-bottom:1px solid rgba(201,169,110,.1);gap:10px;flex-wrap:wrap}
.ord-resi-info{display:flex;align-items:center;flex-wrap:wrap;gap:4px}
.ord-copy-btn{background:rgba(201,169,110,.1);border:1px solid rgba(201,169,110,.2);border-radius:7px;padding:5px 12px;font-size:.75rem;color:#c9a96e;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .15s;display:flex;align-items:center}
.ord-copy-btn:hover{background:rgba(201,169,110,.2)}
.ord-pay-alert{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:rgba(234,179,8,.06);border-top:1px solid rgba(234,179,8,.15);border-bottom:1px solid rgba(234,179,8,.15);flex-wrap:wrap;gap:12px}
.ord-pay-btn{display:inline-flex;align-items:center;background:#facc15;border:none;border-radius:8px;padding:9px 18px;font-size:.82rem;font-weight:700;color:#0e0e0f;text-decoration:none;transition:background .2s;white-space:nowrap}
.ord-pay-btn:hover{background:#eab308;color:#0e0e0f}
.ord-card-foot{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:rgba(255,255,255,.02);border-top:1px solid rgba(255,255,255,.05);flex-wrap:wrap;gap:12px}
.ord-total{display:flex;align-items:center;gap:10px}
.ord-total-lbl{font-size:.78rem;color:rgba(240,236,228,.35)}
.ord-total-val{font-size:1rem;font-weight:700;color:#c9a96e;font-family:'Cormorant Garamond',serif}
.ord-actions{display:flex;gap:8px;flex-wrap:wrap}
.ord-btn-review{display:inline-flex;align-items:center;background:rgba(201,169,110,.1);border:1px solid rgba(201,169,110,.25);border-radius:8px;padding:8px 16px;font-size:.8rem;font-weight:500;color:#c9a96e;text-decoration:none;transition:all .2s}
.ord-btn-review:hover{background:rgba(201,169,110,.2);color:#c9a96e}
.ord-btn-cancel-sm{display:inline-flex;align-items:center;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:7px 14px;font-size:.78rem;font-weight:500;color:#f87171;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s}
.ord-btn-cancel-sm:hover{background:rgba(239,68,68,.18)}
.ord-btn-detail{display:inline-flex;align-items:center;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:8px 16px;font-size:.8rem;color:rgba(240,236,228,.6);text-decoration:none;transition:all .2s}
.ord-btn-detail:hover{border-color:rgba(255,255,255,.25);color:#f0ece4}
.ord-empty{text-align:center;padding:72px 20px;background:#151515;border:1px dashed rgba(255,255,255,.08);border-radius:14px}
.ord-empty-icon{font-size:3.5rem;margin-bottom:16px}
.ord-empty-title{font-size:1.1rem;font-weight:600;color:rgba(240,236,228,.5);margin-bottom:8px}
.ord-empty-sub{font-size:.85rem;color:rgba(240,236,228,.25);margin-bottom:24px}
/* Modal */
.ord-modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center}
.ord-modal{background:#1a1a1b;border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:32px 28px;max-width:400px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.5)}
.ord-modal-icon{font-size:2.5rem;margin-bottom:16px}
.ord-modal-title{font-size:1.1rem;font-weight:700;color:#f0ece4;margin-bottom:10px}
.ord-modal-sub{font-size:.85rem;color:rgba(240,236,228,.45);line-height:1.6;margin-bottom:24px}
.ord-modal-actions{display:flex;gap:10px}
.ord-modal-no{flex:1;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:11px;font-size:.88rem;color:rgba(240,236,228,.6);cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s}
.ord-modal-no:hover{background:rgba(255,255,255,.1);color:#f0ece4}
.ord-modal-yes{width:100%;background:#ef4444;border:none;border-radius:9px;padding:11px;font-size:.88rem;font-weight:600;color:white;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;transition:background .2s}
.ord-modal-yes:hover{background:#dc2626}
</style>
@endpush
@push('scripts')
<script>
function copyResi(resi) {
    navigator.clipboard.writeText(resi).then(() => {
        const btn = event.target.closest('button');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Tersalin!';
        btn.style.cssText = 'background:rgba(74,222,128,.15);border-color:rgba(74,222,128,.3);color:#4ade80';
        setTimeout(() => { btn.innerHTML = orig; btn.style.cssText = ''; }, 2000);
    });
}

function confirmCancel(orderNumber, cancelUrl) {
    document.getElementById('cancelModalText').innerHTML =
        `Pesanan <strong style="color:#f0ece4">${orderNumber}</strong> akan dibatalkan dan produk dikembalikan ke katalog.`;
    document.getElementById('cancelModalForm').action = cancelUrl;
    document.getElementById('cancelModalIndex').style.display = 'flex';
}

document.getElementById('cancelModalIndex').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('cancelModalIndex').style.display = 'none';
});
</script>
@endpush
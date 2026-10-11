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
                    'pending_payment' => ['#d97706','#fffbeb','#fde68a'],
                    'paid'       => ['#2563eb','#eff6ff','#bfdbfe'],
                    'processing' => ['#b45309','#fef3c7','#fde68a'],
                    'shipped'    => ['#7c3aed','#f5f3ff','#ddd6fe'],
                    'delivered'  => ['#16a34a','#f0fdf4','#bbf7d0'],
                    'cancelled'  => ['#dc2626','#fef2f2','#fecaca'],
                    default      => ['#64748b','#f8fafc','#e2e8f0'],
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
                <i class="bi bi-truck me-2" style="color:#b45309"></i>
                <span style="color:#78350f;font-size:.8rem">No. Resi:</span>
                <span style="color:#92400e;font-weight:700;font-size:.88rem;margin-left:6px">{{ $order->tracking_number }}</span>
                <span style="color:#78350f;font-size:.75rem;margin-left:6px">· {{ strtoupper($order->courier) }}</span>
            </div>
            <button onclick="copyResi('{{ $order->tracking_number }}')" class="ord-copy-btn">
                <i class="bi bi-copy me-1"></i>Salin
            </button>
        </div>
        @endif

        {{-- Countdown Timer & Pay alert --}}
        @if($order->status === 'pending_payment')
        @php
            $expiresAt = $order->effective_expires_at;
        @endphp
        <div class="ord-pay-alert ord-lock-alert" data-order-id="{{ $order->id }}">
            <div style="display:flex;align-items:center;gap:12px;flex:1">
                <div class="ord-timer-badge">
                    <i class="bi bi-hourglass-split ord-sand-icon"></i>
                </div>
                <div>
                    <div style="font-size:.9rem;font-weight:700;color:#92400e;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                        <span>Sisa Waktu Pembayaran:</span>
                        <span class="ord-countdown-timer font-monospace fw-bold"
                              data-expires="{{ $expiresAt?->toIso8601String() }}">
                            15:00
                        </span>
                    </div>
                    <div style="font-size:.78rem;color:#a16207;margin-top:2px">
                        Stok produk dikunci hingga <strong>{{ $expiresAt?->format('H:i') }}</strong> WIB. Selesaikan pembayaran sebelum waktu habis agar stok tidak dilepas kembali.
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                @if($order->payment_url)
                <a href="{{ $order->payment_url }}" target="_blank" class="ord-pay-btn">
                    <i class="bi bi-credit-card me-1"></i>Bayar Sekarang <i class="bi bi-arrow-right ms-1"></i>
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="ord-card-foot">
            <div class="ord-total">
                <span class="ord-total-lbl">Total</span>
                <span class="ord-total-val">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="ord-actions">
                @if($order->status === 'shipped')
                <button type="button" class="ord-btn-receive-sm"
                        onclick="confirmReceive('{{ $order->order_number }}', '{{ route('orders.received', $order) }}')">
                    <i class="bi bi-check2-circle me-1"></i>Pesanan Sudah Diterima
                </button>
                @endif

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

{{-- MODAL KONFIRMASI TERIMA PESANAN --}}
<div id="receiveModalIndex" class="ord-modal-backdrop" style="display:none">
    <div class="ord-modal">
        <div class="ord-modal-icon" style="color:#4ade80">
            <i class="bi bi-patch-check-fill" style="font-size:2.8rem"></i>
        </div>
        <h5 class="ord-modal-title">Konfirmasi Terima Pesanan</h5>
        <p class="ord-modal-sub" id="receiveModalText"></p>
        <div class="ord-modal-actions">
            <button type="button" class="ord-modal-no"
                    onclick="document.getElementById('receiveModalIndex').style.display='none'">
                Batal
            </button>
            <form id="receiveModalForm" method="POST" style="flex:1">
                @csrf
                <button type="submit" class="ord-modal-receive-yes">
                    <i class="bi bi-check2-circle me-1"></i>Ya, Pesanan Sudah Diterima
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
@push('styles')
<style>
.ord-page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.ord-page-title{font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:700;color:#0f2044;margin:0 0 4px}
.ord-page-sub{font-size:.88rem;color:#64748b;margin:0}
.ord-btn-shop{display:inline-flex;align-items:center;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:9px;padding:9px 18px;font-size:.85rem;font-weight:600;color:#0f2044;text-decoration:none;transition:all .2s}
.ord-btn-shop:hover{background:#0f2044;color:#ffffff;border-color:#0f2044}
.ord-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px;padding-bottom:18px;border-bottom:1.5px solid #e2e8f0}
.ord-tab{display:inline-block;padding:8px 18px;border-radius:9px;font-size:.84rem;font-weight:500;color:#64748b;background:#f8fafc;text-decoration:none;border:1.5px solid #e2e8f0;transition:all .2s;white-space:nowrap}
.ord-tab:hover{color:#0f2044;border-color:#cbd5e1;background:#f1f5f9}
.ord-tab.active{background:#0f2044;border-color:#0f2044;color:#ffffff;font-weight:600;box-shadow:0 2px 6px rgba(15,32,68,.15)}
.ord-card{background:#ffffff;border:1.5px solid #e2e8f0;border-radius:14px;overflow:hidden;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.04);transition:all .2s}
.ord-card:hover{border-color:#cbd5e1;box-shadow:0 4px 16px rgba(0,0,0,.07)}
.ord-card-head{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:#f8fafc;border-bottom:1.5px solid #e2e8f0;flex-wrap:wrap;gap:8px}
.ord-card-head-left{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.ord-number{font-size:.92rem;font-weight:700;color:#0f2044;letter-spacing:.3px}
.ord-date{font-size:.78rem;color:#64748b}
.ord-status-badge{display:inline-flex;align-items:center;padding:5px 12px;border-radius:20px;font-size:.75rem;font-weight:600;border:1.5px solid}
.ord-progress-wrap{padding:18px 20px 14px;background:#ffffff;border-bottom:1.5px solid #f1f5f9}
.ord-progress-track{height:4px;background:#f1f5f9;border-radius:10px;margin-bottom:10px;position:relative}
.ord-progress-fill{height:100%;background:linear-gradient(90deg,#16a34a,#22c55e);border-radius:10px;transition:width .6s ease}
.ord-progress-steps{display:flex;justify-content:space-between}
.ord-progress-step{display:flex;flex-direction:column;align-items:center;gap:6px}
.ord-prog-dot{width:22px;height:22px;border-radius:50%;background:#ffffff;border:1.5px solid #cbd5e1;display:flex;align-items:center;justify-content:center;font-size:.7rem;color:transparent;margin-top:-9px;position:relative;z-index:1}
.ord-progress-step.done .ord-prog-dot{background:#16a34a;border-color:#16a34a;color:#ffffff}
.ord-progress-step.current .ord-prog-dot{border-color:#16a34a;background:#f0fdf4}
.ord-prog-pulse{width:8px;height:8px;background:#16a34a;border-radius:50%;animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.7}}
.ord-prog-lbl{font-size:.72rem;color:#94a3b8;white-space:nowrap;font-weight:500}
.ord-progress-step.done .ord-prog-lbl,.ord-progress-step.current .ord-prog-lbl{color:#0f2044;font-weight:600}
.ord-items{padding:4px 0;background:#ffffff}
.ord-item{display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f1f5f9}
.ord-item:last-child{border-bottom:none}
.ord-item-img{width:56px;height:72px;border-radius:8px;overflow:hidden;flex-shrink:0;border:1.5px solid #e2e8f0}
.ord-item-img img{width:100%;height:100%;object-fit:cover}
.ord-item-no-img{width:100%;height:100%;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#94a3b8}
.ord-item-info{flex:1}
.ord-item-name{font-size:.9rem;font-weight:600;color:#0f2044;line-height:1.3}
.ord-item-meta{font-size:.78rem;color:#64748b;margin-top:3px}
.ord-item-price{font-size:.92rem;font-weight:700;color:#0f2044;flex-shrink:0}
.ord-more-items{padding:9px 20px;font-size:.78rem;color:#64748b;text-align:center;background:#f8fafc;border-top:1px solid #f1f5f9}
.ord-resi-bar{display:flex;justify-content:space-between;align-items:center;padding:12px 20px;background:#fffbeb;border-top:1.5px solid #fde68a;border-bottom:1.5px solid #fde68a;gap:10px;flex-wrap:wrap}
.ord-resi-info{display:flex;align-items:center;flex-wrap:wrap;gap:6px;font-size:.85rem}
.ord-copy-btn{background:rgba(180,83,9,.08);border:1px solid rgba(180,83,9,.2);border-radius:7px;padding:6px 14px;font-size:.78rem;color:#b45309;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .15s;display:flex;align-items:center}
.ord-copy-btn:hover{background:rgba(180,83,9,.15)}
.ord-pay-alert{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:#fefce8;border-top:1.5px solid #fde047;border-bottom:1.5px solid #fde047;flex-wrap:wrap;gap:12px}
.ord-pay-btn{display:inline-flex;align-items:center;background:#facc15;border:none;border-radius:8px;padding:9px 18px;font-size:.84rem;font-weight:700;color:#0e0e0f;text-decoration:none;transition:background .2s;white-space:nowrap}
.ord-pay-btn:hover{background:#eab308;color:#0e0e0f}
.ord-card-foot{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:#f8fafc;border-top:1.5px solid #e2e8f0;flex-wrap:wrap;gap:12px}
.ord-total{display:flex;align-items:center;gap:10px}
.ord-total-lbl{font-size:.82rem;color:#64748b}
.ord-total-val{font-size:1.05rem;font-weight:700;color:#0f2044}
.ord-actions{display:flex;gap:8px;flex-wrap:wrap}
.ord-btn-review{display:inline-flex;align-items:center;background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;padding:8px 16px;font-size:.82rem;font-weight:600;color:#16a34a;text-decoration:none;transition:all .2s}
.ord-btn-review:hover{background:#16a34a;color:#ffffff;border-color:#16a34a}
.ord-btn-cancel-sm{display:inline-flex;align-items:center;background:#fef2f2;border:1.5px solid #fecaca;border-radius:8px;padding:8px 14px;font-size:.8rem;font-weight:600;color:#dc2626;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s}
.ord-btn-cancel-sm:hover{background:#ef4444;color:white;border-color:#ef4444}
.ord-btn-receive-sm{display:inline-flex;align-items:center;background:#16a34a;border:none;border-radius:8px;padding:8px 16px;font-size:.82rem;font-weight:600;color:#ffffff;cursor:pointer;font-family:'Outfit',sans-serif;box-shadow:0 2px 6px rgba(22,163,74,.2);transition:all .2s}
.ord-btn-receive-sm:hover{background:#15803d;color:#ffffff}
.ord-btn-detail{display:inline-flex;align-items:center;background:#ffffff;border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 16px;font-size:.82rem;font-weight:500;color:#0f2044;text-decoration:none;transition:all .2s}
.ord-btn-detail:hover{border-color:#0f2044;color:#0f2044;background:#f8fafc}
.ord-empty{text-align:center;padding:72px 20px;background:#ffffff;border:1.5px dashed #cbd5e1;border-radius:14px}
.ord-empty-icon{font-size:3.5rem;margin-bottom:16px}
.ord-empty-title{font-size:1.15rem;font-weight:700;color:#0f2044;margin-bottom:8px}
.ord-empty-sub{font-size:.88rem;color:#64748b;margin-bottom:24px}
/* Modal */
.ord-modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center}
.ord-modal{background:#ffffff;border:1.5px solid #e2e8f0;border-radius:16px;padding:32px 28px;max-width:400px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.12)}
.ord-modal-icon{font-size:2.5rem;margin-bottom:16px}
.ord-modal-title{font-size:1.1rem;font-weight:700;color:#0f2044;margin-bottom:10px}
.ord-modal-sub{font-size:.85rem;color:#64748b;line-height:1.6;margin-bottom:24px}
.ord-modal-actions{display:flex;gap:10px}
.ord-modal-no{flex:1;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:9px;padding:11px;font-size:.88rem;color:#64748b;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s}
.ord-modal-no:hover{background:#f1f5f9;color:#0f2044}
.ord-modal-yes{width:100%;background:#ef4444;border:none;border-radius:9px;padding:11px;font-size:.88rem;font-weight:600;color:white;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;transition:background .2s}
.ord-modal-yes:hover{background:#dc2626}
.ord-modal-receive-yes{width:100%;background:#16a34a;border:none;border-radius:9px;padding:11px;font-size:.88rem;font-weight:600;color:white;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;transition:background .2s}
.ord-modal-receive-yes:hover{background:#15803d}
.ord-timer-badge{width:36px;height:36px;border-radius:50%;background:#fde047;display:flex;align-items:center;justify-content:center;color:#854d0e;font-size:1.1rem;flex-shrink:0}
.ord-countdown-timer{background:#fef08a;padding:2px 8px;border-radius:6px;border:1px solid #fde047;color:#854d0e;letter-spacing:1px}
</style>
@endpush
@push('scripts')
<script>
function initOrderCountdowns() {
    const timers = document.querySelectorAll('.ord-countdown-timer');
    if (!timers.length) return;

    let hasExpired = false;

    function tick() {
        const now = Date.now();
        timers.forEach(timer => {
            const expiresStr = timer.getAttribute('data-expires');
            if (!expiresStr) return;
            const expiresTime = new Date(expiresStr).getTime();
            const diff = expiresTime - now;

            if (diff <= 0) {
                timer.textContent = '00:00 (Waktu Habis)';
                timer.classList.add('text-danger');
                if (!hasExpired) {
                    hasExpired = true;
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            } else {
                const totalSeconds = Math.max(0, Math.floor(diff / 1000));
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                const minStr = String(minutes).padStart(2, '0');
                const secStr = String(seconds).padStart(2, '0');
                timer.textContent = `${minStr}:${secStr}`;
            }
        });
    }

    tick();
    setInterval(tick, 1000);
}
document.addEventListener('DOMContentLoaded', initOrderCountdowns);

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
        `Pesanan <strong style="color:#0f2044">${orderNumber}</strong> akan dibatalkan dan produk dikembalikan ke katalog.`;
    document.getElementById('cancelModalForm').action = cancelUrl;
    document.getElementById('cancelModalIndex').style.display = 'flex';
}

function confirmReceive(orderNumber, receiveUrl) {
    document.getElementById('receiveModalText').innerHTML =
        `Apakah Anda benar-benar sudah menerima pesanan <strong style="color:#0f2044">${orderNumber}</strong>?<br><span style="font-size:.8rem;color:#64748b;display:block;margin-top:6px">Status pesanan akan diubah menjadi <strong>Pesanan Selesai</strong>.</span>`;
    document.getElementById('receiveModalForm').action = receiveUrl;
    document.getElementById('receiveModalIndex').style.display = 'flex';
}

document.getElementById('cancelModalIndex').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.getElementById('receiveModalIndex')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('cancelModalIndex') && (document.getElementById('cancelModalIndex').style.display = 'none');
        document.getElementById('receiveModalIndex') && (document.getElementById('receiveModalIndex').style.display = 'none');
    }
});
</script>
@endpush
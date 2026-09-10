@extends('layouts.app')
@section('title', 'Pesanan Berhasil')
@section('content')

<div class="container py-5" style="max-width:620px">
    <div class="cs-card">

        {{-- Icon --}}
        <div class="cs-icon-wrap">
            <div class="cs-icon">
                <i class="bi bi-bag-check-fill"></i>
            </div>
        </div>

        {{-- Title --}}
        <h3 class="cs-title">Pesanan Berhasil Dibuat!</h3>
        <p class="cs-sub">
            Terima kasih telah berbelanja di ThriftStore.<br>
            Pesanan kamu sedang menunggu konfirmasi pembayaran.
        </p>

        {{-- Order Number --}}
        <div class="cs-order-box">
            <div class="cs-order-label">Nomor Pesanan</div>
            <div class="cs-order-number">{{ $order->order_number }}</div>
            <button onclick="copyText('{{ $order->order_number }}')" class="cs-copy-btn">
                <i class="bi bi-copy me-1"></i>Salin
            </button>
        </div>

        {{-- Info Pembayaran --}}
        @if($order->payment_url)
        <div class="cs-pay-section">
            <div class="cs-pay-title">
                <i class="bi bi-credit-card me-2" style="color:#c9a96e"></i>
                Selesaikan Pembayaran
            </div>
            <p class="cs-pay-desc">
                Klik tombol di bawah untuk melanjutkan ke halaman pembayaran.
                Tersedia Virtual Account, E-Wallet, QRIS, dan metode lainnya.
            </p>
            <a href="{{ $order->payment_url }}" target="_blank" class="cs-btn-pay">
                <i class="bi bi-shield-check me-2"></i>
                Bayar Sekarang — Rp {{ number_format($order->total, 0, ',', '.') }}
            </a>
        </div>
        @else
        {{-- Manual Transfer --}}
        <div class="cs-manual-section">
            <div class="cs-manual-title">
                <i class="bi bi-bank me-2" style="color:#c9a96e"></i>
                Instruksi Pembayaran Manual
            </div>
            <p class="cs-pay-desc">
                Silakan transfer ke rekening berikut dan konfirmasi ke admin via WhatsApp.
            </p>
            <div class="cs-bank-list">
                <div class="cs-bank-item">
                    <div class="cs-bank-name">BCA</div>
                    <div class="cs-bank-number">1234567890</div>
                    <div class="cs-bank-holder">a.n. ThriftStore</div>
                </div>
                <div class="cs-bank-item">
                    <div class="cs-bank-name">BRI</div>
                    <div class="cs-bank-number">0987654321</div>
                    <div class="cs-bank-holder">a.n. ThriftStore</div>
                </div>
            </div>
            <div class="cs-total-transfer">
                <span>Jumlah Transfer</span>
                <span style="color:#c9a96e;font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:700">
                    Rp {{ number_format($order->total, 0, ',', '.') }}
                </span>
            </div>
            <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Halo+admin,+saya+sudah+transfer+untuk+pesanan+{{ $order->order_number }}"
               target="_blank" class="cs-btn-wa">
                <i class="bi bi-whatsapp me-2"></i>Konfirmasi Pembayaran via WhatsApp
            </a>
        </div>
        @endif

        {{-- Order Summary --}}
        <div class="cs-summary">
            <div class="cs-summary-title">Ringkasan Pesanan</div>
            @foreach($order->items as $item)
            <div class="cs-item">
                <div class="cs-item-info">
                    <div class="cs-item-name">{{ $item->product_name }}</div>
                    <div class="cs-item-meta">Size {{ $item->product_size ?? '-' }} · {{ $item->product_condition }}</div>
                </div>
                <div class="cs-item-price">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
            </div>
            @endforeach
            <div class="cs-summary-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="cs-summary-row">
                <span>Ongkos Kirim ({{ $order->courier }} {{ $order->courier_service }})</span>
                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            <div class="cs-summary-total">
                <span>Total</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="cs-buttons">
            <a href="{{ route('orders.show', $order) }}" class="cs-btn-order">
                <i class="bi bi-bag-check me-2"></i>Lihat Detail Pesanan
            </a>
            <a href="{{ route('catalog.index') }}" class="cs-btn-catalog">
                <i class="bi bi-grid me-2"></i>Lanjut Belanja
            </a>
        </div>

    </div>
</div>

@endsection
@push('styles')
<style>
.cs-card { background:#151515;border:1px solid rgba(255,255,255,.07);border-radius:20px;padding:40px 36px;text-align:center }
.cs-icon-wrap { margin-bottom:24px }
.cs-icon { width:80px;height:80px;border-radius:50%;background:rgba(201,169,110,.1);border:2px solid rgba(201,169,110,.25);display:flex;align-items:center;justify-content:center;font-size:2rem;color:#c9a96e;margin:0 auto }
.cs-title { font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:700;color:#f0ece4;margin-bottom:10px }
.cs-sub { font-size:.88rem;color:rgba(240,236,228,.45);line-height:1.7;margin-bottom:28px }
.cs-order-box { display:flex;align-items:center;justify-content:center;gap:12px;background:rgba(201,169,110,.06);border:1px solid rgba(201,169,110,.2);border-radius:12px;padding:16px 20px;margin-bottom:24px;flex-wrap:wrap }
.cs-order-label { font-size:.7rem;color:rgba(240,236,228,.35);text-transform:uppercase;letter-spacing:1px }
.cs-order-number { font-size:1.1rem;font-weight:700;color:#c9a96e;letter-spacing:1px;font-family:'Cormorant Garamond',serif }
.cs-copy-btn { background:rgba(201,169,110,.1);border:1px solid rgba(201,169,110,.2);border-radius:7px;padding:5px 12px;font-size:.75rem;color:#c9a96e;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;transition:all .15s }
.cs-copy-btn:hover { background:rgba(201,169,110,.2) }

/* Pay section */
.cs-pay-section,.cs-manual-section { background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:20px;margin-bottom:24px;text-align:left }
.cs-pay-title,.cs-manual-title { font-size:.92rem;font-weight:600;color:#f0ece4;margin-bottom:10px;display:flex;align-items:center }
.cs-pay-desc { font-size:.82rem;color:rgba(240,236,228,.45);line-height:1.6;margin-bottom:16px }
.cs-btn-pay { display:flex;align-items:center;justify-content:center;background:#c9a96e;border:none;border-radius:10px;padding:14px;font-size:.92rem;font-weight:700;color:#0e0e0f;text-decoration:none;transition:background .2s }
.cs-btn-pay:hover { background:#b8913d;color:#0e0e0f }

/* Bank info */
.cs-bank-list { display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap }
.cs-bank-item { flex:1;min-width:140px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:12px 14px }
.cs-bank-name { font-size:.72rem;color:rgba(240,236,228,.4);text-transform:uppercase;letter-spacing:1px;margin-bottom:5px }
.cs-bank-number { font-size:1rem;font-weight:700;color:#c9a96e;letter-spacing:1px }
.cs-bank-holder { font-size:.72rem;color:rgba(240,236,228,.3);margin-top:3px }
.cs-total-transfer { display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-top:1px solid rgba(255,255,255,.07);border-bottom:1px solid rgba(255,255,255,.07);margin-bottom:14px;font-size:.85rem;color:rgba(240,236,228,.5) }
.cs-btn-wa { display:flex;align-items:center;justify-content:center;background:#25d366;border:none;border-radius:10px;padding:12px;font-size:.88rem;font-weight:600;color:white;text-decoration:none;transition:background .2s }
.cs-btn-wa:hover { background:#1da851;color:white }

/* Summary */
.cs-summary { background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:20px;margin-bottom:24px;text-align:left }
.cs-summary-title { font-size:.72rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:rgba(240,236,228,.3);margin-bottom:14px }
.cs-item { display:flex;justify-content:space-between;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04) }
.cs-item:last-of-type { border-bottom:none }
.cs-item-name { font-size:.85rem;font-weight:500;color:#f0ece4 }
.cs-item-meta { font-size:.72rem;color:rgba(240,236,228,.35);margin-top:2px }
.cs-item-price { font-size:.85rem;font-weight:600;color:#f0ece4;flex-shrink:0 }
.cs-summary-row { display:flex;justify-content:space-between;font-size:.82rem;color:rgba(240,236,228,.4);padding:5px 0 }
.cs-summary-total { display:flex;justify-content:space-between;font-size:.95rem;font-weight:700;color:#f0ece4;padding-top:10px;margin-top:4px;border-top:1px solid rgba(255,255,255,.07) }
.cs-summary-total span:last-child { color:#c9a96e;font-family:'Cormorant Garamond',serif;font-size:1.1rem }

/* Buttons */
.cs-buttons { display:flex;gap:10px;flex-wrap:wrap }
.cs-btn-order { flex:1;display:flex;align-items:center;justify-content:center;background:#c9a96e;border:none;border-radius:10px;padding:13px;font-size:.88rem;font-weight:600;color:#0e0e0f;text-decoration:none;transition:background .2s;min-width:180px }
.cs-btn-order:hover { background:#b8913d;color:#0e0e0f }
.cs-btn-catalog { flex:1;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:13px;font-size:.88rem;color:rgba(240,236,228,.6);text-decoration:none;transition:all .2s;min-width:180px }
.cs-btn-catalog:hover { border-color:rgba(255,255,255,.25);color:#f0ece4 }
</style>
@endpush
@push('scripts')
<script>
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target.closest('button');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Tersalin!';
        btn.style.cssText = 'background:rgba(74,222,128,.15);border-color:rgba(74,222,128,.3);color:#4ade80';
        setTimeout(() => { btn.innerHTML = orig; btn.style.cssText = ''; }, 2000);
    });
}
</script>
@endpush
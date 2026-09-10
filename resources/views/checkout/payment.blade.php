@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')

<div class="container py-5" style="max-width:580px">
    <div class="cs-card">
        <div class="cs-icon-wrap">
            <div class="cs-icon"><i class="bi bi-credit-card-fill"></i></div>
        </div>
        <h3 class="cs-title">Selesaikan Pembayaran</h3>
        <p class="cs-sub">
            Pesanan <strong style="color:#c9a96e">{{ $order->order_number }}</strong><br>
            Total: <strong style="color:#c9a96e">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
        </p>

        <div style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:20px;margin-bottom:20px">
            <div style="font-size:.82rem;color:rgba(240,236,228,.45);margin-bottom:16px;line-height:1.6">
                Tersedia berbagai metode pembayaran: Virtual Account, GoPay, OVO, ShopeePay, QRIS, dan Kartu Kredit.
            </div>
            <button id="payBtn" class="cs-btn-pay" onclick="openMidtrans()">
                <i class="bi bi-shield-check me-2"></i>
                Bayar Rp {{ number_format($order->total, 0, ',', '.') }}
            </button>
        </div>

        {{-- Info metode pembayaran --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:20px">
            @foreach(['bi-bank2' => 'Virtual Account', 'bi-phone' => 'E-Wallet', 'bi-qr-code' => 'QRIS'] as $icon => $label)
            <div style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:9px;padding:12px 8px;text-align:center">
                <i class="bi {{ $icon }}" style="font-size:1.3rem;color:#c9a96e;display:block;margin-bottom:6px"></i>
                <span style="font-size:.72rem;color:rgba(240,236,228,.4)">{{ $label }}</span>
            </div>
            @endforeach
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('orders.show', $order) }}" class="cs-btn-catalog" style="flex:1;justify-content:center;padding:11px">
                <i class="bi bi-bag-check me-2"></i>Lihat Pesanan
            </a>
        </div>

        <div style="margin-top:16px;font-size:.72rem;color:rgba(240,236,228,.25);text-align:center">
            <i class="bi bi-shield-check me-1"></i>
            Transaksi diproses secara aman oleh Midtrans
        </div>
    </div>
</div>

@endsection
@push('styles')
<style>
.cs-card{background:#151515;border:1px solid rgba(255,255,255,.07);border-radius:20px;padding:36px;text-align:center}
.cs-icon-wrap{margin-bottom:20px}
.cs-icon{width:72px;height:72px;border-radius:50%;background:rgba(201,169,110,.1);border:2px solid rgba(201,169,110,.25);display:flex;align-items:center;justify-content:center;font-size:1.8rem;color:#c9a96e;margin:0 auto}
.cs-title{font-family:'Cormorant Garamond',serif;font-size:1.7rem;font-weight:700;color:#f0ece4;margin-bottom:10px}
.cs-sub{font-size:.88rem;color:rgba(240,236,228,.45);line-height:1.7;margin-bottom:24px}
.cs-btn-pay{display:flex;align-items:center;justify-content:center;background:#c9a96e;border:none;border-radius:10px;padding:14px;font-size:.92rem;font-weight:700;color:#0e0e0f;cursor:pointer;width:100%;font-family:'Outfit',sans-serif;transition:background .2s}
.cs-btn-pay:hover{background:#b8913d}
.cs-btn-pay:disabled{background:rgba(201,169,110,.4);cursor:not-allowed}
.cs-btn-catalog{display:flex;align-items:center;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:12px 20px;font-size:.88rem;color:rgba(240,236,228,.6);text-decoration:none;transition:all .2s}
.cs-btn-catalog:hover{border-color:rgba(255,255,255,.25);color:#f0ece4}
</style>
@endpush
@push('scripts')
{{-- Gunakan sandbox atau production sesuai config --}}
<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
const snapToken = '{{ $snap["token"] ?? "" }}';

function openMidtrans() {
    if (!snapToken) {
        alert('Token pembayaran tidak valid. Hubungi admin.');
        return;
    }

    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memuat...';

    window.snap.pay(snapToken, {
        onSuccess: function(result) {
            // Redirect ke halaman sukses
            window.location.href = '{{ route("payment.finish") }}?order_id={{ $order->order_number }}&transaction_status=settlement&payment_type=' + (result.payment_type || '');
        },
        onPending: function(result) {
            window.location.href = '{{ route("orders.show", $order) }}';
        },
        onError: function(result) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Coba Bayar Lagi';
            window.location.href = '{{ route("payment.error") }}?order_id={{ $order->order_number }}';
        },
        onClose: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Bayar Rp {{ number_format($order->total, 0, ',', ".") }}';
        }
    });
}

// Auto buka snap jika ada token
document.addEventListener('DOMContentLoaded', function() {
    if (snapToken) {
        setTimeout(openMidtrans, 800);
    }
});
</script>
@endpush
@extends('layouts.app')
@section('title','Keranjang Belanja')
@section('content')
<div class="container py-4" style="max-width:700px">
    <h3 class="fw-bold mb-4">🛒 Keranjang Belanja</h3>

    @if($items->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-bag fs-1 text-muted"></i>
            <p class="text-muted mt-2">Keranjang kamu kosong.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">Lihat Katalog</a>
        </div>
    @else
        @foreach($items as $item)
        @php $p = $item->product; @endphp
        <div class="card border-0 shadow-sm mb-3 p-3">
            <div class="d-flex gap-3">
                @if($p->mainImage())
                    <img src="{{ Storage::url($p->mainImage()->image_path) }}"
                         class="rounded" style="width:90px;height:90px;object-fit:cover">
                @endif
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1">{{ $p->name }}</h6>
                    <small class="text-muted">{{ $p->brand?->name }} · Size {{ $p->size }} · {{ $p->conditionLabel() }}</small>
                    <div class="fw-bold mt-1">Rp {{ number_format($p->price,0,',','.') }}</div>
                    <div class="lock-timer mt-2 d-flex align-items-center gap-2 flex-wrap">
                        <small class="text-muted d-flex align-items-center gap-1">
                            <i class="bi bi-clock"></i>
                            Dikunci hingga: <span class="fw-semibold text-dark">{{ $item->expires_at?->format('H:i') }}</span>
                        </small>
                        @if($item->expires_at)
                            <span class="badge bg-light text-dark border cart-countdown px-2 py-1"
                                  data-expires="{{ $item->expires_at->toIso8601String() }}">
                                <i class="bi bi-hourglass-split text-warning me-1"></i><span class="countdown-text">...</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <form action="{{ route('cart.remove', $p) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        <div class="card border-0 shadow-sm p-4 mt-3">
            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal</span>
                <strong>Rp {{ number_format($total,0,',','.') }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted small">Ongkir dihitung saat checkout</span>
            </div>
            @auth
                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg w-100">
                    Lanjut ke Checkout <i class="bi bi-arrow-right ms-2"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">Login untuk Checkout</a>
            @endauth
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const countdownEls = document.querySelectorAll('.cart-countdown');
    if (!countdownEls.length) return;

    function updateCountdowns() {
        countdownEls.forEach(el => {
            const expiresStr = el.getAttribute('data-expires');
            if (!expiresStr) return;
            const expiresTime = new Date(expiresStr).getTime();
            const now = new Date().getTime();
            const distance = expiresTime - now;

            const textEl = el.querySelector('.countdown-text');
            if (!textEl) return;

            if (distance <= 0) {
                textEl.textContent = 'Waktu habis';
                el.className = 'badge bg-danger-subtle text-danger border border-danger-subtle cart-countdown px-2 py-1';
            } else {
                const totalSeconds = Math.floor(distance / 1000);
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                textEl.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds} tersisa`;
            }
        });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 1000);
});
</script>
@endpush
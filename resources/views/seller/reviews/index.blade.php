@extends('layouts.seller')
@section('title', 'Ulasan Produk Saya')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;margin-bottom:4px">
            Ulasan Produk
        </h4>
        <p style="font-size:.82rem;color:var(--muted);margin:0">
            Ulasan pembeli terhadap produk di toko <strong>{{ $store->name }}</strong>
        </p>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="rv-stat">
            <div class="rv-stat-icon"><i class="bi bi-chat-square-text-fill"></i></div>
            <div class="rv-stat-label">Total Ulasan</div>
            <div class="rv-stat-value">{{ $totalReviews }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rv-stat">
            <div class="rv-stat-icon" style="color:#f59e0b"><i class="bi bi-star-fill"></i></div>
            <div class="rv-stat-label">Rata-rata Rating</div>
            <div class="rv-stat-value" style="color:#f59e0b">{{ number_format($avgRating, 1) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rv-stat">
            <div class="rv-stat-icon" style="color:#22c55e"><i class="bi bi-emoji-smile-fill"></i></div>
            <div class="rv-stat-label">Rating 4–5 Bintang</div>
            <div class="rv-stat-value" style="color:#22c55e">{{ $rating5 + $rating4 }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rv-stat">
            <div class="rv-stat-icon" style="color:#ef4444"><i class="bi bi-emoji-frown-fill"></i></div>
            <div class="rv-stat-label">Rating 1–2 Bintang</div>
            <div class="rv-stat-value" style="color:#ef4444">{{ $rating1 + $rating2 }}</div>
        </div>
    </div>
</div>

{{-- DISTRIBUSI RATING --}}
@if($totalReviews > 0)
<div class="rv-bar-card mb-4">
    <div class="rv-bar-title"><i class="bi bi-bar-chart-fill me-2" style="color:var(--accent)"></i>Distribusi Rating</div>
    <div class="rv-bar-body">
        @foreach([5 => $rating5, 4 => $rating4, 3 => $rating3, 2 => $rating2, 1 => $rating1] as $star => $count)
        <div class="rv-bar-row">
            <div class="rv-bar-label">
                @for($s=1;$s<=5;$s++)
                <i class="bi bi-star{{ $s<=$star?'-fill':'' }}" style="font-size:.7rem;color:{{ $s<=$star?'#f59e0b':'#d1d5db' }}"></i>
                @endfor
            </div>
            <div class="rv-bar-track">
                <div class="rv-bar-fill" style="width:{{ $totalReviews>0 ? round(($count/$totalReviews)*100) : 0 }}%"></div>
            </div>
            <div class="rv-bar-count">{{ $count }}</div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- FILTER RATING --}}
<div class="rv-filter-bar mb-3">
    <span style="font-size:.78rem;color:var(--muted);font-weight:500">Filter:</span>
    <a href="{{ route('seller.reviews.index') }}"
       class="rv-filter-btn {{ !request('rating') ? 'active' : '' }}">Semua</a>
    @for($s=5;$s>=1;$s--)
    <a href="{{ route('seller.reviews.index', ['rating'=>$s]) }}"
       class="rv-filter-btn {{ request('rating')==$s ? 'active' : '' }}">
        {{ $s }} <i class="bi bi-star-fill" style="font-size:.65rem;color:#f59e0b"></i>
    </a>
    @endfor
</div>

{{-- DAFTAR ULASAN --}}
<div class="d-flex flex-column gap-3">
    @forelse($reviews as $review)
    <div class="rv-card">
        {{-- Header: pembeli & rating & tanggal --}}
        <div class="rv-card-head">
            <div class="rv-user-info">
                <div class="rv-avatar">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</div>
                <div>
                    <div class="rv-user-name">{{ $review->user->name ?? 'Pengguna Dihapus' }}</div>
                    <div class="rv-user-date">
                        <i class="bi bi-calendar3 me-1"></i>{{ $review->created_at->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
            <div class="rv-stars-badge">
                @for($s=1;$s<=5;$s++)
                <i class="bi bi-star{{ $s<=$review->rating?'-fill':'' }}"
                   style="color:#f59e0b;font-size:.95rem"></i>
                @endfor
                <span class="rv-rating-num">{{ $review->rating }}/5</span>
            </div>
        </div>

        {{-- Produk & Pesanan --}}
        <div class="rv-product-row">
            @if($review->product && $review->product->mainImage())
            <img src="{{ Storage::url($review->product->mainImage()->image_path) }}"
                 class="rv-product-img" alt="{{ $review->product->name }}">
            @else
            <div class="rv-product-img-placeholder"><i class="bi bi-image"></i></div>
            @endif
            <div>
                <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);margin-bottom:3px">Produk</div>
                <div style="font-size:.88rem;font-weight:600;color:var(--primary)">
                    {{ $review->product->name ?? 'Produk telah dihapus' }}
                </div>
                @if($review->order)
                <div style="font-size:.72rem;color:var(--muted);margin-top:3px">
                    <i class="bi bi-receipt me-1"></i>Order: {{ $review->order->order_number }}
                </div>
                @endif
            </div>
        </div>

        {{-- Komentar --}}
        <div class="rv-comment-body">
            <i class="bi bi-chat-quote me-2" style="color:rgba(212,168,67,.5)"></i>
            {{ $review->comment }}
        </div>

        {{-- Foto ulasan --}}
        @if($review->photos && count($review->photos) > 0)
        <div class="rv-photos">
            @foreach($review->photos as $photo)
            <a href="{{ Storage::url($photo) }}" target="_blank">
                <img src="{{ Storage::url($photo) }}" class="rv-photo-thumb" alt="Foto ulasan">
            </a>
            @endforeach
        </div>
        @endif

        {{-- Status & Aksi --}}
        <div class="rv-card-foot">
            <span class="rv-status-badge {{ $review->is_approved ? 'rv-status-approved' : 'rv-status-pending' }}">
                <i class="bi bi-{{ $review->is_approved ? 'eye-fill' : 'eye-slash-fill' }} me-1"></i>
                {{ $review->is_approved ? 'Ditampilkan' : 'Disembunyikan' }}
            </span>
            <form action="{{ route('seller.reviews.toggle', $review) }}" method="POST" style="display:inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="rv-toggle-btn {{ $review->is_approved ? 'rv-toggle-hide' : 'rv-toggle-show' }}">
                    <i class="bi bi-{{ $review->is_approved ? 'eye-slash' : 'eye' }} me-1"></i>
                    {{ $review->is_approved ? 'Sembunyikan' : 'Tampilkan' }}
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="rv-empty">
        <i class="bi bi-star rv-empty-icon"></i>
        <div class="rv-empty-title">Belum Ada Ulasan</div>
        <div class="rv-empty-sub">
            Ulasan dari pembeli akan muncul di sini setelah pesanan selesai
        </div>
    </div>
    @endforelse
</div>

{{-- PAGINATION --}}
@if($reviews->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $reviews->withQueryString()->links() }}
</div>
@endif

@endsection
@push('styles')
<style>
/* ── STAT CARDS ── */
.rv-stat {
    background: white;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
}
.rv-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--accent);
}
.rv-stat-icon { font-size: 1.5rem; color: var(--accent); margin-bottom: 8px; }
.rv-stat-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .9px; color: var(--muted); margin-bottom: 5px; font-weight: 600; }
.rv-stat-value { font-family: 'Playfair Display', serif; font-size: 1.75rem; font-weight: 700; color: var(--primary); line-height: 1; }

/* ── RATING BAR CARD ── */
.rv-bar-card { background: white; border: 1.5px solid var(--border); border-radius: 10px; overflow: hidden; }
.rv-bar-title { padding: 14px 18px; border-bottom: 1px solid var(--border); font-size: .88rem; font-weight: 600; color: var(--primary); display: flex; align-items: center; }
.rv-bar-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 10px; }
.rv-bar-row { display: flex; align-items: center; gap: 10px; }
.rv-bar-label { display: flex; gap: 2px; width: 90px; flex-shrink: 0; }
.rv-bar-track { flex: 1; height: 8px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.rv-bar-fill { height: 100%; background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 99px; transition: width .6s ease; }
.rv-bar-count { font-size: .78rem; font-weight: 600; color: var(--muted); width: 24px; text-align: right; flex-shrink: 0; }

/* ── FILTER BAR ── */
.rv-filter-bar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.rv-filter-btn { display: inline-flex; align-items: center; gap: 3px; padding: 6px 14px; border-radius: 99px; font-size: .78rem; font-weight: 500; color: var(--muted); border: 1.5px solid var(--border); background: white; text-decoration: none; transition: all .18s; }
.rv-filter-btn:hover { border-color: var(--accent); color: var(--primary); }
.rv-filter-btn.active { background: var(--accent); border-color: var(--accent); color: white; font-weight: 700; }

/* ── REVIEW CARD ── */
.rv-card {
    background: white;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
}
.rv-card:hover { border-color: rgba(212,168,67,.4); box-shadow: 0 4px 20px rgba(212,168,67,.08); }

/* Card Head */
.rv-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    background: #fafafa;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
    gap: 10px;
}
.rv-user-info { display: flex; align-items: center; gap: 12px; }
.rv-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: rgba(212,168,67,.15);
    border: 1.5px solid rgba(212,168,67,.3);
    color: var(--accent);
    font-size: .82rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.rv-user-name { font-size: .88rem; font-weight: 600; color: var(--primary); }
.rv-user-date { font-size: .72rem; color: var(--muted); margin-top: 2px; }
.rv-stars-badge { display: flex; align-items: center; gap: 3px; }
.rv-rating-num { font-size: .72rem; color: var(--muted); margin-left: 5px; font-weight: 600; }

/* Product row */
.rv-product-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 20px;
    border-bottom: 1px solid #f1f5f9;
    background: #fdfaf4;
}
.rv-product-img { width: 44px; height: 56px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border); flex-shrink: 0; }
.rv-product-img-placeholder { width: 44px; height: 56px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 1.2rem; flex-shrink: 0; }

/* Comment */
.rv-comment-body { padding: 14px 20px; font-size: .88rem; color: #374151; line-height: 1.75; border-bottom: 1px solid #f1f5f9; }

/* Photos */
.rv-photos { display: flex; gap: 8px; flex-wrap: wrap; padding: 12px 20px; border-bottom: 1px solid #f1f5f9; }
.rv-photo-thumb { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); transition: opacity .2s; }
.rv-photo-thumb:hover { opacity: .8; }

/* Card Footer with actions */
.rv-card-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    background: #fafafa;
    border-top: 1px solid #f1f5f9;
    gap: 10px;
    flex-wrap: wrap;
}
.rv-status-badge {
    display: inline-flex;
    align-items: center;
    font-size: .72rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 99px;
    border: 1.5px solid;
}
.rv-status-approved { color: #16a34a; background: #f0fdf4; border-color: #86efac; }
.rv-status-pending  { color: #dc2626; background: #fef2f2; border-color: #fecaca; }
.rv-toggle-btn {
    display: inline-flex;
    align-items: center;
    font-size: .75rem;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 8px;
    border: 1.5px solid;
    cursor: pointer;
    font-family: 'Outfit', sans-serif;
    transition: all .18s;
    background: white;
}
.rv-toggle-hide { color: #d97706; border-color: #fcd34d; }
.rv-toggle-hide:hover { background: #fef3c7; color: #92400e; border-color: #f59e0b; }
.rv-toggle-show { color: #16a34a; border-color: #86efac; }
.rv-toggle-show:hover { background: #f0fdf4; color: #15803d; border-color: #4ade80; }

/* Empty state */
.rv-empty {
    text-align: center;
    padding: 64px 24px;
    background: white;
    border: 2px dashed var(--border);
    border-radius: 12px;
}
.rv-empty-icon { font-size: 2.8rem; color: #d1d5db; display: block; margin-bottom: 14px; }
.rv-empty-title { font-size: 1rem; font-weight: 600; color: var(--primary); margin-bottom: 6px; }
.rv-empty-sub { font-size: .82rem; color: var(--muted); max-width: 300px; margin: 0 auto; }
</style>
@endpush

@extends('layouts.admin')
@section('title', 'Manajemen Ulasan')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:#f0ece4;margin-bottom:4px">
            Manajemen Ulasan
        </h4>
        <p style="font-size:.82rem;color:rgba(240,236,228,.35);margin:0">
            Moderasi ulasan dari pembeli
        </p>
    </div>
</div>

{{-- FILTER --}}
<div class="adm-card mb-4">
    <div class="adm-card-body" style="padding:14px 20px">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <div>
                <label class="rv-label">Status</label>
                <select name="status" class="adm-select" style="width:160px;padding:8px 12px" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="pending"  @selected(request('status')==='pending')>Menunggu Persetujuan</option>
                    <option value="approved" @selected(request('status')==='approved')>Sudah Disetujui</option>
                </select>
            </div>
            <div>
                <label class="rv-label">Rating</label>
                <select name="rating" class="adm-select" style="width:130px;padding:8px 12px" onchange="this.form.submit()">
                    <option value="">Semua Rating</option>
                    @for($i=5;$i>=1;$i--)
                    <option value="{{ $i }}" @selected(request('rating')==$i)>
                        {{ $i }} Bintang
                    </option>
                    @endfor
                </select>
            </div>
            <a href="{{ route('admin.reviews.index') }}" class="adm-btn-secondary" style="padding:8px 14px">
                Reset
            </a>
        </form>
    </div>
</div>

{{-- STATS --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon"><i class="bi bi-star-fill"></i></div>
            <div class="stat-card-label">Total Ulasan</div>
            <div class="stat-card-value">{{ \App\Models\Review::count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="color:#facc15"><i class="bi bi-clock"></i></div>
            <div class="stat-card-label">Menunggu Persetujuan</div>
            <div class="stat-card-value" style="color:#facc15">{{ \App\Models\Review::where('is_approved',false)->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="color:#4ade80"><i class="bi bi-check-circle"></i></div>
            <div class="stat-card-label">Disetujui</div>
            <div class="stat-card-value" style="color:#4ade80">{{ \App\Models\Review::where('is_approved',true)->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="color:#c9a96e"><i class="bi bi-star-half"></i></div>
            <div class="stat-card-label">Rata-rata Rating</div>
            <div class="stat-card-value" style="color:#c9a96e">{{ number_format(\App\Models\Review::where('is_approved',true)->avg('rating') ?? 0, 1) }}</div>
        </div>
    </div>
</div>

{{-- REVIEW LIST --}}
<div class="d-flex flex-column gap-3">
    @forelse($reviews as $review)
    <div class="rv-card {{ $review->is_approved ? '' : 'rv-card-pending' }}">

        {{-- Header --}}
        <div class="rv-card-head">
            <div class="rv-user">
                <div class="rv-avatar">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
                <div>
                    <div class="rv-user-name">{{ $review->user->name }}</div>
                    <div class="rv-user-date">{{ $review->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                {{-- Rating --}}
                <div class="rv-stars">
                    @for($i=1;$i<=5;$i++)
                    <i class="bi bi-star{{ $i<=$review->rating?'-fill':'' }}"
                       style="color:#c9a96e;font-size:.85rem"></i>
                    @endfor
                    <span style="font-size:.78rem;color:rgba(240,236,228,.4);margin-left:4px">{{ $review->rating }}/5</span>
                </div>
                {{-- Status badge --}}
                @if($review->is_approved)
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:.7rem;font-weight:600;background:rgba(34,197,94,.1);color:#4ade80;border:1px solid rgba(34,197,94,.2)">
                    <i class="bi bi-check-circle-fill"></i> Disetujui
                </span>
                @else
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:.7rem;font-weight:600;background:rgba(234,179,8,.1);color:#facc15;border:1px solid rgba(234,179,8,.2)">
                    <i class="bi bi-clock"></i> Menunggu
                </span>
                @endif
            </div>
        </div>

        {{-- Produk --}}
        <div class="rv-product">
            @if($review->product && $review->product->mainImage())
            <img src="{{ Storage::url($review->product->mainImage()->image_path) }}"
                 style="width:44px;height:56px;object-fit:cover;border-radius:7px;border:1px solid rgba(255,255,255,.08)">
            @endif
            <div>
                <div style="font-size:.8rem;color:rgba(240,236,228,.4);text-transform:uppercase;letter-spacing:.8px;font-size:.65rem;margin-bottom:3px">Produk</div>
                <div style="font-size:.88rem;font-weight:500;color:#f0ece4">{{ $review->product?->name ?? 'Produk dihapus' }}</div>
                <div style="font-size:.72rem;color:rgba(240,236,228,.3);margin-top:2px">
                    Order: {{ $review->order?->order_number ?? '-' }}
                </div>
            </div>
        </div>

        {{-- Komentar --}}
        <div class="rv-comment">
            <i class="bi bi-chat-quote me-2" style="color:rgba(201,169,110,.4)"></i>
            {{ $review->comment }}
        </div>

        {{-- Foto --}}
        @if($review->photos && count($review->photos) > 0)
        <div class="rv-photos">
            @foreach($review->photos as $photo)
            <a href="{{ Storage::url($photo) }}" target="_blank">
                <img src="{{ Storage::url($photo) }}"
                     style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:1px solid rgba(255,255,255,.08);transition:opacity .2s"
                     onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
            </a>
            @endforeach
        </div>
        @endif

        {{-- Actions --}}
        <div class="rv-actions">
            <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                @csrf @method('PUT')
                <button type="submit" class="{{ $review->is_approved ? 'rv-btn-hide' : 'rv-btn-approve' }}">
                    @if($review->is_approved)
                        <i class="bi bi-eye-slash me-1"></i>Sembunyikan
                    @else
                        <i class="bi bi-check2-circle me-1"></i>Setujui & Tampilkan
                    @endif
                </button>
            </form>
            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                  onsubmit="return confirm('Hapus ulasan ini secara permanen?')">
                @csrf @method('DELETE')
                <button type="submit" class="rv-btn-delete">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:60px 20px;background:#151515;border:1px dashed rgba(255,255,255,.07);border-radius:14px">
        <i class="bi bi-star" style="font-size:2.5rem;color:rgba(240,236,228,.15);display:block;margin-bottom:12px"></i>
        <div style="font-size:.95rem;color:rgba(240,236,228,.3);font-weight:500">Belum ada ulasan</div>
        <div style="font-size:.8rem;color:rgba(240,236,228,.18);margin-top:4px">Ulasan akan muncul setelah pembeli mengirimkan review</div>
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
.rv-label { display:block;font-size:.68rem;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:7px }

/* Card */
.rv-card { background:#151515;border:1px solid rgba(255,255,255,.07);border-radius:14px;overflow:hidden;transition:border-color .2s }
.rv-card:hover { border-color:rgba(201,169,110,.2) }
.rv-card-pending { border-color:rgba(234,179,8,.15) }
.rv-card-pending:hover { border-color:rgba(234,179,8,.3) }

/* Head */
.rv-card-head { display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:rgba(255,255,255,.02);border-bottom:1px solid rgba(255,255,255,.05);flex-wrap:wrap;gap:10px }
.rv-user { display:flex;align-items:center;gap:12px }
.rv-avatar { width:36px;height:36px;border-radius:50%;background:rgba(201,169,110,.15);border:1px solid rgba(201,169,110,.25);color:#c9a96e;font-size:.82rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0 }
.rv-user-name { font-size:.88rem;font-weight:600;color:#f0ece4 }
.rv-user-date { font-size:.72rem;color:rgba(240,236,228,.3);margin-top:1px }
.rv-stars { display:flex;align-items:center;gap:2px }

/* Product */
.rv-product { display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,.04) }

/* Comment */
.rv-comment { padding:14px 20px;font-size:.88rem;color:rgba(240,236,228,.65);line-height:1.7;border-bottom:1px solid rgba(255,255,255,.04) }

/* Photos */
.rv-photos { display:flex;gap:8px;flex-wrap:wrap;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,.04) }

/* Actions */
.rv-actions { display:flex;gap:10px;padding:14px 20px;background:rgba(255,255,255,.02) }
.rv-btn-approve { display:inline-flex;align-items:center;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);border-radius:8px;padding:8px 18px;font-size:.82rem;font-weight:600;color:#4ade80;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s }
.rv-btn-approve:hover { background:rgba(34,197,94,.2) }
.rv-btn-hide { display:inline-flex;align-items:center;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:8px 18px;font-size:.82rem;font-weight:500;color:rgba(240,236,228,.5);cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s }
.rv-btn-hide:hover { background:rgba(255,255,255,.1);color:#f0ece4 }
.rv-btn-delete { display:inline-flex;align-items:center;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.15);border-radius:8px;padding:8px 16px;font-size:.82rem;color:#f87171;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s }
.rv-btn-delete:hover { background:rgba(239,68,68,.18) }
</style>
@endpush
@extends('layouts.app')
@section('title','Semua Toko')
@section('content')

<div class="container py-5">
    <div class="section-title-wrap">
        <div class="section-eyebrow">Marketplace</div>
        <h2 class="section-title">Semua Toko Thrift</h2>
        <p class="section-sub">Temukan toko thrift terpercaya dari seluruh Indonesia</p>
    </div>

    <div class="row g-4">
        @forelse($stores as $store)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('stores.show', $store->slug) }}" style="text-decoration:none;color:inherit">
                <div style="background:white;border:1.5px solid var(--border);border-radius:12px;overflow:hidden;transition:all .2s" onmouseover="this.style.borderColor='var(--primary)';this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='var(--border)';this.style.transform='none'">
                    {{-- Banner --}}
                    <div style="height:80px;background:#f8f8f6;overflow:hidden">
                        @if($store->banner)
                        <img src="{{ Storage::url($store->banner) }}" style="width:100%;height:100%;object-fit:cover">
                        @else
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,#1a1a1a,#444)"></div>
                        @endif
                    </div>
                    <div style="padding:16px">
                        {{-- Logo --}}
                        <div style="margin-top:-28px;margin-bottom:10px">
                            @if($store->logo)
                            <img src="{{ Storage::url($store->logo) }}"
                                 style="width:52px;height:52px;border-radius:50%;border:3px solid white;object-fit:cover;background:white">
                            @else
                            <div style="width:52px;height:52px;border-radius:50%;border:3px solid white;background:var(--primary);display:flex;align-items:center;justify-content:center;color:white;font-size:1.2rem;font-weight:700">
                                {{ strtoupper(substr($store->name,0,1)) }}
                            </div>
                            @endif
                        </div>
                        <div style="font-weight:700;font-size:.95rem;margin-bottom:4px">{{ $store->name }}</div>
                        <div style="font-size:.75rem;color:var(--muted);margin-bottom:10px">{{ $store->city }}, {{ $store->province }}</div>
                        <div style="display:flex;justify-content:space-between;font-size:.75rem">
                            <span style="color:var(--muted)"><i class="bi bi-tags me-1"></i>{{ $store->products_count }} produk</span>
                            <span style="color:var(--muted)"><i class="bi bi-star-fill me-1" style="color:#f59e0b"></i>{{ number_format($store->rating,1) }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-shop" style="font-size:3rem;color:var(--faint);display:block;margin-bottom:16px"></i>
            <p style="color:var(--muted)">Belum ada toko aktif.</p>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">{{ $stores->links() }}</div>

    {{-- CTA buka toko --}}
    @guest
    <div style="text-align:center;margin-top:48px;padding:40px;background:#f8f8f6;border:1.5px dashed var(--border);border-radius:14px">
        <h5 style="font-weight:700;margin-bottom:8px">Punya Baju Bekas? Jual di Sini!</h5>
        <p style="color:var(--muted);font-size:.88rem;margin-bottom:20px">Daftar gratis, buka toko, dan mulai berjualan hari ini.</p>
        <a href="{{ route('seller.register') }}" class="btn-gold">Buka Toko Sekarang →</a>
    </div>
    @endguest
</div>

@endsection
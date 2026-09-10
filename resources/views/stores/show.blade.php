@extends('layouts.app')
@section('title', $store->name)
@section('content')

{{-- Store Header --}}
<div style="background:var(--primary);color:white;padding:0">
    {{-- Banner --}}
    <div style="height:200px;overflow:hidden;position:relative">
        @if($store->banner)
        <img src="{{ Storage::url($store->banner) }}" style="width:100%;height:100%;object-fit:cover;opacity:.6">
        @else
        <div style="width:100%;height:100%;background:linear-gradient(135deg,#1a1a1a 0%,#333 100%)"></div>
        @endif
    </div>

    <div class="container" style="position:relative;padding-top:0">
        <div style="display:flex;align-items:flex-end;gap:20px;margin-top:-40px;padding-bottom:24px;flex-wrap:wrap">
            {{-- Logo --}}
            @if($store->logo)
            <img src="{{ Storage::url($store->logo) }}"
                 style="width:80px;height:80px;border-radius:50%;border:4px solid white;object-fit:cover">
            @else
            <div style="width:80px;height:80px;border-radius:50%;border:4px solid white;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:white">
                {{ strtoupper(substr($store->name,0,1)) }}
            </div>
            @endif

            <div style="flex:1;padding-bottom:4px">
                <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:white;margin-bottom:4px">
                    {{ $store->name }}
                </h1>
                <div style="font-size:.82rem;color:rgba(255,255,255,.6)">
                    <i class="bi bi-geo-alt me-1"></i>{{ $store->city }}, {{ $store->province }}
                    &nbsp;·&nbsp;
                    <i class="bi bi-tags me-1"></i>{{ $products->total() }} produk
                    &nbsp;·&nbsp;
                    <i class="bi bi-star-fill me-1" style="color:#f59e0b"></i>{{ number_format($store->rating,1) }}
                </div>
            </div>

            <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$store->phone) }}"
               target="_blank"
               style="background:#25d366;color:white;padding:10px 18px;border-radius:6px;text-decoration:none;font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:7px">
                <i class="bi bi-whatsapp"></i>Chat Penjual
            </a>
        </div>
    </div>
</div>

{{-- Deskripsi --}}
@if($store->description)
<div style="background:#f8f8f6;border-bottom:1px solid var(--border)">
    <div class="container" style="padding:16px 12px">
        <p style="font-size:.85rem;color:var(--muted);margin:0">{{ $store->description }}</p>
    </div>
</div>
@endif

{{-- Produk --}}
<div class="container py-4">
    <h5 style="font-weight:700;margin-bottom:20px">Produk Toko</h5>

    @if($products->isEmpty())
    <div style="text-align:center;padding:48px;background:#f8f8f6;border:1.5px dashed var(--border);border-radius:12px">
        <i class="bi bi-box" style="font-size:2.5rem;color:var(--faint);display:block;margin-bottom:12px"></i>
        <p style="color:var(--muted)">Belum ada produk tersedia.</p>
    </div>
    @else
    <div class="product-grid">
        @foreach($products as $i => $product)
        <div class="product-card" style="animation-delay:{{ $i * 0.04 }}s">
            <div class="pcard-img-wrap">
                @if($product->mainImage())
                    <img src="{{ Storage::url($product->mainImage()->image_path) }}"
                         alt="{{ $product->name }}" loading="lazy">
                @else
                    <div class="pcard-no-img"><i class="bi bi-image"></i></div>
                @endif
                @if($product->isSold())
                    <div class="pcard-sold-overlay"><span class="pcard-sold-tag">Terjual</span></div>
                @else
                    <div class="pcard-overlay">
                        <a href="{{ route('catalog.show', $product->slug) }}" class="btn-card-detail">Lihat Detail</a>
                    </div>
                @endif
                <span class="pcard-cond-badge">{{ $product->conditionLabel() }}</span>
            </div>
            <div class="pcard-body">
                <div class="pcard-brand">{{ $product->brand?->name ?? 'No Brand' }}</div>
                <div class="pcard-name">{{ $product->name }}</div>
                <div class="pcard-meta">Size {{ $product->size ?? '-' }}</div>
                <div class="pcard-foot">
                    <div class="pcard-price">Rp {{ number_format($product->price,0,',','.') }}</div>
                    <a href="{{ route('catalog.show', $product->slug) }}" class="pcard-btn">Detail →</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center mt-4">{{ $products->links() }}</div>
    @endif
</div>

@endsection
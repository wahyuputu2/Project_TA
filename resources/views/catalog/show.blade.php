@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Katalog</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Galeri Foto -->
        <div class="col-lg-6">
            <div id="productCarousel" class="carousel slide shadow-sm rounded" data-bs-ride="carousel">
                <div class="carousel-inner rounded">
                    @foreach($product->images as $i => $image)
                    <div class="carousel-item {{ $i===0 ? 'active' : '' }}">
                        <img src="{{ Storage::url($image->image_path) }}" class="d-block w-100"
                             style="aspect-ratio:1;object-fit:cover" alt="{{ $image->angleLabel() }}">
                        <div class="carousel-caption d-none d-md-block" style="background:rgba(0,0,0,.4);border-radius:6px;padding:4px 10px;bottom:10px">
                            <small>{{ $image->angleLabel() }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($product->images->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                @endif
            </div>
            <!-- Thumbnail -->
            <div class="d-flex gap-2 mt-2 flex-wrap">
                @foreach($product->images as $i => $image)
                <img src="{{ Storage::url($image->image_path) }}"
                     onclick="document.querySelector('.carousel').querySelector('[data-bs-slide-to]') || bootstrap.Carousel.getInstance(document.getElementById('productCarousel')).to({{ $i }})"
                     class="rounded border" style="width:60px;height:60px;object-fit:cover;cursor:pointer"
                     data-bs-target="#productCarousel" data-bs-slide-to="{{ $i }}"
                     title="{{ $image->angleLabel() }}">
                @endforeach
            </div>
        </div>

        <!-- Detail Produk -->
        <div class="col-lg-6">
            <p class="text-muted small mb-1">{{ $product->brand?->name }} · {{ $product->category->name }}</p>
            <h1 class="h3 fw-bold" style="font-family:'Playfair Display',serif">{{ $product->name }}</h1>

            <!-- Rating -->
            @php $avg = $product->averageRating(); @endphp
            <div class="d-flex align-items-center gap-2 mb-3">
                @for($i=1;$i<=5;$i++)
                    <i class="bi bi-star{{ $i<=$avg ? '-fill' : '' }}" style="color:var(--accent);font-size:.9rem"></i>
                @endfor
                <span class="text-muted small">({{ $product->reviews->where('is_approved',true)->count() }} ulasan)</span>
            </div>

            <h2 class="fw-bold mb-3" style="color:var(--primary);font-size:1.8rem">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </h2>

            <!-- Status Badge -->
            <div class="mb-3">
                @if($product->isAvailable())
                    <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Tersedia</span>
                @elseif($product->isLocked())
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-lock-fill me-1"></i>Sedang Dipesan (Stok Dikunci)</span>
                @else
                    <span class="badge bg-danger fs-6 px-3 py-2"><i class="bi bi-x-circle me-1"></i>Sudah Terjual</span>
                @endif
            </div>

            <!-- Info Grid -->
            <div class="row g-2 mb-4">
                <div class="col-6">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Kondisi</div>
                        <div class="fw-bold">{{ $product->conditionLabel() }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Size</div>
                        <div class="fw-bold">{{ $product->size ?? '-' }}</div>
                    </div>
                </div>
                @if($product->length_cm && $product->width_cm)
                <div class="col-6">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Panjang (P)</div>
                        <div class="fw-bold">{{ $product->length_cm }} cm</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Lebar (L)</div>
                        <div class="fw-bold">{{ $product->width_cm }} cm</div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Deskripsi -->
            <div class="mb-3">
                <h6 class="fw-bold">Deskripsi Produk</h6>
                <p class="text-muted">{{ $product->description }}</p>
            </div>

            <!-- Catatan Minus -->
            @if($product->condition_notes)
            <div class="alert alert-warning mb-3">
                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Catatan Kondisi (Jujur)</h6>
                <p class="mb-0 small">{{ $product->condition_notes }}</p>
            </div>
            @endif

            <!-- Tombol Aksi -->
            @if($product->isAvailable())
                <form action="{{ route('cart.add', $product) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                        <i class="bi bi-bag-plus me-2"></i>Tambah ke Keranjang
                    </button>
                </form>
                <p class="text-muted small text-center"><i class="bi bi-clock me-1"></i>Barang terkunci 15 menit saat masuk keranjang</p>
            @elseif($product->isLocked())
                <button type="button" class="btn btn-secondary btn-lg w-100 mb-2" disabled>
                    <i class="bi bi-lock-fill me-2"></i>Sedang Dipesan (Stok Dikunci)
                </button>
                <p class="text-warning-emphasis small text-center"><i class="bi bi-hourglass-split me-1"></i>Produk sedang dalam proses transaksi pembeli lain.</p>
            @else
                <button type="button" class="btn btn-secondary btn-lg w-100 mb-2" disabled>
                    <i class="bi bi-x-circle me-2"></i>Sudah Terjual
                </button>
                <p class="text-muted small text-center">Produk ini sudah laku terjual.</p>
            @endif

            <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Halo, saya ingin tanya tentang produk: {{ $product->name }}"
               target="_blank" class="btn btn-success w-100 mt-2">
                <i class="bi bi-whatsapp me-2"></i>Tanya via WhatsApp
            </a>
        </div>
    </div>

    <!-- Review Section -->
    <div class="mt-5">
        <h4 class="fw-bold mb-4">Ulasan Pembeli</h4>
        @forelse($product->reviews->where('is_approved',true) as $review)
        <div class="card border-0 shadow-sm mb-3 p-4">
            <div class="d-flex gap-3">
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                     style="width:40px;height:40px;flex-shrink:0">{{ substr($review->user->name,0,1) }}</div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $review->user->name }}</strong>
                        <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                    </div>
                    <div class="mb-1">
                        @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$review->rating?'-fill':'' }}" style="color:var(--accent);font-size:.8rem"></i>@endfor
                    </div>
                    <p class="mb-2">{{ $review->comment }}</p>
                    @if($review->photos)
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($review->photos as $photo)
                        <img src="{{ Storage::url($photo) }}" class="rounded" style="width:80px;height:80px;object-fit:cover">
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
            <p class="text-muted">Belum ada ulasan untuk produk ini.</p>
        @endforelse
    </div>
</div>
@endsection
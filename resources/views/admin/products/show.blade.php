@extends('layouts.admin')
@section('title', 'Detail Produk — ' . $product->name)
@section('content')

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('admin.products.index') }}" class="text-decoration-none" style="color:rgba(240,236,228,.4);font-size:.82rem">
                <i class="bi bi-arrow-left me-1"></i>Daftar Produk
            </a>
            <span style="color:rgba(240,236,228,.2)">/</span>
            <span style="color:rgba(240,236,228,.6);font-size:.82rem">{{ $product->category?->name ?? 'Produk' }}</span>
        </div>
        <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.75rem;font-weight:700;color:#f0ece4;margin-bottom:4px">
            {{ $product->name }}
        </h4>
        <p style="font-size:.82rem;color:rgba(240,236,228,.4);margin:0">
            Didaftarkan {{ $product->created_at->format('d M Y, H:i') }} ·
            @php
                $sc = match($product->status) {
                    'available' => ['#4ade80','rgba(34,197,94,.1)'],
                    'locked'    => ['#facc15','rgba(234,179,8,.1)'],
                    'sold'      => ['#f87171','rgba(239,68,68,.1)'],
                    default     => ['rgba(240,236,228,.4)','rgba(255,255,255,.05)'],
                };
            @endphp
            <span style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:.72rem;font-weight:600;color:{{ $sc[0] }};background:{{ $sc[1] }};margin-left:4px">
                {{ $product->statusLabel() }}
            </span>
        </p>
    </div>

    <div class="d-flex gap-2 align-items-center flex-wrap">
        <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;font-size:.72rem;font-weight:600;background:rgba(96,165,250,.08);border:1px solid rgba(96,165,250,.2);color:#60a5fa">
            <i class="bi bi-shield-check"></i> Mode Pemantauan (Read-Only)
        </span>
        <a href="{{ route('admin.products.index') }}" class="adm-btn-secondary" style="padding:7px 14px;text-decoration:none">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <a href="{{ route('catalog.show', $product->slug) }}" target="_blank" class="adm-btn-primary" style="padding:7px 14px;text-decoration:none">
            <i class="bi bi-box-arrow-up-right me-1"></i>Lihat di Katalog
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- KOLOM KIRI: FOTO, DESKRIPSI & SPESIFIKASI --}}
    <div class="col-lg-8">
        {{-- FOTO PRODUK --}}
        <div class="adm-card mb-4">
            <div class="adm-card-header" style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.06)">
                <span class="adm-card-title"><i class="bi bi-images me-2"></i>Foto Produk ({{ $product->images->count() }} Foto)</span>
            </div>
            <div class="adm-card-body" style="padding:20px">
                @if($product->images->count() > 0)
                <div class="row g-3">
                    @foreach($product->images as $img)
                    <div class="col-6 col-md-3">
                        <div style="position:relative;border-radius:9px;overflow:hidden;border:1px solid rgba(255,255,255,.08);background:rgba(0,0,0,.2)">
                            <img src="{{ Storage::url($img->image_path) }}"
                                 alt="{{ $product->name }}"
                                 style="width:100%;height:170px;object-fit:cover;display:block">
                            <span style="position:absolute;bottom:6px;left:6px;background:rgba(0,0,0,.7);color:var(--gold);font-size:.65rem;padding:2px 7px;border-radius:4px;text-transform:uppercase;letter-spacing:.5px;font-weight:600">
                                {{ $img->angle ?? 'Foto' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="text-align:center;padding:36px;color:rgba(240,236,228,.3)">
                    <i class="bi bi-image" style="font-size:2.5rem;display:block;margin-bottom:8px"></i>
                    Belum ada foto yang diunggah untuk produk ini
                </div>
                @endif
            </div>
        </div>

        {{-- INFORMASI LENGKAP PRODUK --}}
        <div class="adm-card mb-4">
            <div class="adm-card-header" style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.06)">
                <span class="adm-card-title"><i class="bi bi-tag-fill me-2"></i>Detail Spesifikasi Produk</span>
            </div>
            <div class="adm-card-body" style="padding:20px">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div style="font-size:.7rem;text-transform:uppercase;color:rgba(240,236,228,.35);font-weight:600;margin-bottom:4px">Harga Jual</div>
                        <div style="font-size:1.4rem;font-weight:700;color:var(--gold)">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:.7rem;text-transform:uppercase;color:rgba(240,236,228,.35);font-weight:600;margin-bottom:4px">Kondisi Barang</div>
                        <span style="display:inline-block;padding:4px 12px;border-radius:20px;font-size:.78rem;font-weight:600;background:rgba(201,169,110,.12);color:#c9a96e;border:1px solid rgba(201,169,110,.25)">
                            {{ $product->conditionLabel() }}
                        </span>
                    </div>
                </div>

                <div class="row g-3 p-3 rounded mb-4" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.05)">
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:rgba(240,236,228,.35);margin-bottom:2px">Kategori</div>
                        <div style="font-weight:600;color:#f0ece4">{{ $product->category?->name ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:rgba(240,236,228,.35);margin-bottom:2px">Brand / Merek</div>
                        <div style="font-weight:600;color:#f0ece4">{{ $product->brand?->name ?? 'Tanpa Brand' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:rgba(240,236,228,.35);margin-bottom:2px">Ukuran (Size)</div>
                        <div style="font-weight:600;color:#f0ece4">{{ $product->size ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:rgba(240,236,228,.35);margin-bottom:2px">Target Gender</div>
                        <div style="font-weight:600;color:#f0ece4;text-transform:capitalize">{{ $product->gender ?? 'Unisex' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:rgba(240,236,228,.35);margin-bottom:2px">Panjang (P)</div>
                        <div style="font-weight:600;color:#f0ece4">{{ $product->length_cm ? $product->length_cm . ' cm' : '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:rgba(240,236,228,.35);margin-bottom:2px">Lebar (L)</div>
                        <div style="font-weight:600;color:#f0ece4">{{ $product->width_cm ? $product->width_cm . ' cm' : '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:rgba(240,236,228,.35);margin-bottom:2px">Berat Produk</div>
                        <div style="font-weight:600;color:#f0ece4">{{ $product->weight_gram ?? 500 }} gram</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="font-size:.7rem;color:rgba(240,236,228,.35);margin-bottom:2px">Slug URL</div>
                        <div style="font-size:.75rem;color:rgba(240,236,228,.6);word-break:break-all">{{ $product->slug }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <div style="font-size:.72rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:8px">
                        Deskripsi Produk
                    </div>
                    <div style="font-size:.88rem;color:rgba(240,236,228,.8);line-height:1.7;white-space:pre-line">
                        {{ $product->description ?: 'Tidak ada deskripsi.' }}
                    </div>
                </div>

                @if($product->condition_notes)
                <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);border-radius:8px;padding:14px 16px">
                    <div style="font-weight:600;color:#fbbf24;font-size:.82rem;margin-bottom:4px;display:flex;align-items:center;gap:6px">
                        <i class="bi bi-exclamation-triangle"></i> Catatan Minus / Kondisi Fisik
                    </div>
                    <p style="margin:0;font-size:.82rem;color:rgba(240,236,228,.8)">
                        {{ $product->condition_notes }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- ULASAN PEMBELI (JIKA ADA) --}}
        @if($product->reviews->count() > 0)
        <div class="adm-card">
            <div class="adm-card-header" style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.06)">
                <span class="adm-card-title"><i class="bi bi-star-fill text-warning me-2"></i>Ulasan Pembeli ({{ $product->reviews->count() }})</span>
            </div>
            <div class="adm-card-body" style="padding:20px">
                @foreach($product->reviews as $rev)
                <div class="p-3 mb-3 rounded" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.05)">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong style="color:#f0ece4;font-size:.85rem">{{ $rev->user?->name ?? 'Pembeli' }}</strong>
                        <span style="font-size:.72rem;color:rgba(240,236,228,.35)">{{ $rev->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="mb-2">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star{{ $i <= $rev->rating ? '-fill text-warning' : ' text-secondary' }}" style="font-size:.75rem"></i>
                        @endfor
                    </div>
                    <p style="font-size:.82rem;color:rgba(240,236,228,.7);margin:0">{{ $rev->comment }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- KOLOM KANAN: INFORMASI SELLER & STATUS STOK --}}
    <div class="col-lg-4">
        {{-- INFORMASI TOKO / SELLER --}}
        <div class="adm-card mb-4">
            <div class="adm-card-header" style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.06)">
                <span class="adm-card-title"><i class="bi bi-shop me-2"></i>Informasi Toko / Seller</span>
            </div>
            <div class="adm-card-body" style="padding:20px">
                @if($product->store)
                <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom:1px solid rgba(255,255,255,.06)">
                    @if($product->store->logo)
                        <img src="{{ Storage::url($product->store->logo) }}"
                             style="width:48px;height:48px;border-radius:10px;object-fit:cover;border:1px solid rgba(255,255,255,.1)">
                    @else
                        <div style="width:48px;height:48px;border-radius:10px;background:rgba(201,169,110,.15);color:#c9a96e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;border:1px solid rgba(201,169,110,.3)">
                            {{ substr($product->store->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight:700;color:#f0ece4;font-size:.95rem">{{ $product->store->name }}</div>
                        <div style="font-size:.75rem;color:rgba(240,236,228,.4)">
                            {{ $product->store->city ?? 'Indonesia' }}, {{ $product->store->province ?? '' }}
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div style="font-size:.7rem;text-transform:uppercase;color:rgba(240,236,228,.35);font-weight:600;margin-bottom:2px">Pemilik Akun (User)</div>
                    <div style="font-weight:500;color:#f0ece4;font-size:.85rem">{{ $product->store->user?->name ?? '—' }}</div>
                    <div style="font-size:.75rem;color:rgba(240,236,228,.4)">{{ $product->store->user?->email ?? '—' }}</div>
                </div>

                @if($product->store->phone)
                <div class="mb-3">
                    <div style="font-size:.7rem;text-transform:uppercase;color:rgba(240,236,228,.35);font-weight:600;margin-bottom:2px">Nomor Kontak</div>
                    <div style="font-weight:500;color:#f0ece4;font-size:.85rem">
                        <i class="bi bi-telephone me-1 text-muted"></i>{{ $product->store->phone }}
                    </div>
                </div>
                @endif

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.05);padding:10px;border-radius:8px;text-align:center">
                            <div style="font-size:.68rem;color:rgba(240,236,228,.4)">Status Toko</div>
                            <span style="font-size:.75rem;font-weight:600;color:#4ade80;text-transform:capitalize">
                                {{ $product->store->status ?? 'active' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.05);padding:10px;border-radius:8px;text-align:center">
                            <div style="font-size:.68rem;color:rgba(240,236,228,.4)">Total Penjualan</div>
                            <span style="font-size:.8rem;font-weight:700;color:var(--gold)">
                                {{ $product->store->total_sales ?? 0 }} Produk
                            </span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('stores.show', $product->store->slug) }}" target="_blank"
                   style="display:block;text-align:center;padding:8px 12px;border-radius:8px;font-size:.78rem;text-decoration:none;background:rgba(255,255,255,.04);color:rgba(240,236,228,.7);border:1px solid rgba(255,255,255,.08)">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Kunjungi Profil Toko
                </a>
                @else
                <div style="text-align:center;padding:20px;color:rgba(240,236,228,.3);font-size:.82rem">
                    Produk ini tidak terikat pada toko aktif mana pun.
                </div>
                @endif
            </div>
        </div>

        {{-- STATUS STOK & SIKLUS PENGUNCIAN --}}
        <div class="adm-card">
            <div class="adm-card-header" style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.06)">
                <span class="adm-card-title"><i class="bi bi-clock-history me-2"></i>Status Stok & Transaksi</span>
            </div>
            <div class="adm-card-body" style="padding:20px">
                <div class="mb-3">
                    <div style="font-size:.7rem;text-transform:uppercase;color:rgba(240,236,228,.35);font-weight:600;margin-bottom:4px">Ketersediaan Stok</div>
                    <div style="font-size:.9rem;font-weight:600;color:#f0ece4">
                        1 Unit <span style="font-size:.75rem;color:rgba(240,236,228,.4);font-weight:400">(Barang Thrift / Preloved Satuan)</span>
                    </div>
                </div>

                <div class="mb-3">
                    <div style="font-size:.7rem;text-transform:uppercase;color:rgba(240,236,228,.35);font-weight:600;margin-bottom:4px">Status Siklus</div>
                    @if($product->isAvailable())
                        <div style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:8px;padding:12px;font-size:.8rem;color:#4ade80">
                            <i class="bi bi-check-circle me-1"></i> <strong>Tersedia</strong> untuk dibeli di katalog.
                        </div>
                    @elseif($product->isLocked())
                        <div style="background:rgba(234,179,8,.08);border:1px solid rgba(234,179,8,.2);border-radius:8px;padding:12px;font-size:.8rem;color:#facc15">
                            <div style="font-weight:600;margin-bottom:2px"><i class="bi bi-lock-fill me-1"></i> Sedang Dipesan (Stok Terkunci)</div>
                            <div style="font-size:.72rem;color:rgba(240,236,228,.6)">
                                Pengunci: {{ $product->locked_by_session ?? 'Sesi Pembeli' }}<br>
                                Waktu Kunci: {{ $product->locked_at ? $product->locked_at->format('H:i:s, d M Y') : '—' }}
                            </div>
                        </div>
                    @else
                        <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:12px;font-size:.8rem;color:#f87171">
                            <i class="bi bi-x-circle me-1"></i> <strong>Sudah Terjual</strong> (Transaksi telah diselesaikan).
                        </div>
                    @endif
                </div>

                <div style="border-top:1px solid rgba(255,255,255,.06);padding-top:12px;font-size:.75rem;color:rgba(240,236,228,.4)">
                    <i class="bi bi-info-circle me-1 text-warning"></i>
                    Admin tidak diizinkan mengubah status barang secara manual guna menjaga konsistensi stok dan integritas transaksi seller.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

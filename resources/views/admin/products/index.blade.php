@extends('layouts.admin')
@section('title', 'Manajemen Produk')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:#f0ece4;margin-bottom:4px">
            Manajemen Produk
        </h4>
        <p style="font-size:.82rem;color:rgba(240,236,228,.35);margin:0">
            Semua produk dari semua toko
        </p>
   <div style="font-size:.78rem;color:var(--muted);display:flex;align-items:center;gap:6px">
    <i class="bi bi-info-circle"></i>
    Produk dikelola oleh masing-masing seller
</div>

{{-- FILTER --}}
<div class="adm-card mb-4">
    <div class="adm-card-body" style="padding:16px 20px">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label style="display:block;font-size:.68rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:6px">Cari Produk</label>
                <input type="text" name="search" class="adm-input"
                       placeholder="Nama produk..." value="{{ request('search') }}"
                       style="padding:8px 14px">
            </div>
            <div class="col-md-3">
                <label style="display:block;font-size:.68rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:6px">Status</label>
                <select name="status" class="adm-select" style="padding:8px 12px">
                    <option value="">Semua Status</option>
                    <option value="available" @selected(request('status')==='available')>Tersedia</option>
                    <option value="locked"    @selected(request('status')==='locked')>Dipesan</option>
                    <option value="sold"      @selected(request('status')==='sold')>Terjual</option>
                </select>
            </div>
            <div class="col-md-3">
                <label style="display:block;font-size:.68rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:6px">Filter Toko</label>
                <select name="store_id" class="adm-select" style="padding:8px 12px">
                    <option value="">Semua Toko</option>
                    @foreach($stores as $s)
                    <option value="{{ $s->id }}" @selected(request('store_id')==$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="adm-btn-primary" style="padding:9px 16px;flex:1;justify-content:center">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('admin.products.index') }}" class="adm-btn-secondary" style="padding:9px 14px">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- TABEL --}}
<div class="adm-card">
    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Produk</th>
                    <th>Toko</th>
                    <th>Brand</th>
                    <th>Size</th>
                    <th>Kondisi</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        @if($product->mainImage())
                            <img src="{{ Storage::url($product->mainImage()->image_path) }}"
                                 style="width:50px;height:65px;object-fit:cover;border-radius:7px;border:1px solid rgba(255,255,255,.08)">
                        @else
                            <div style="width:50px;height:65px;background:rgba(255,255,255,.04);border-radius:7px;display:flex;align-items:center;justify-content:center;color:rgba(240,236,228,.2)">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600;color:#f0ece4;font-size:.88rem">{{ $product->name }}</div>
                        <div style="font-size:.72rem;color:rgba(240,236,228,.3)">{{ Str::limit($product->description, 40) }}</div>
                    </td>
                    <td>
                        @if($product->store)
                            <span style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:.72rem;font-weight:600;background:rgba(201,169,110,.1);color:#c9a96e;border:1px solid rgba(201,169,110,.2)">
                                {{ $product->store->name }}
                            </span>
                        @else
                            <span style="font-size:.75rem;color:rgba(240,236,228,.3)">—</span>
                        @endif
                    </td>
                    <td style="font-size:.82rem;color:rgba(240,236,228,.6)">{{ $product->brand?->name ?? '—' }}</td>
                    <td style="font-size:.82rem;color:rgba(240,236,228,.6)">{{ $product->size ?? '—' }}</td>
                    <td>
                        <span style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:.7rem;font-weight:600;background:rgba(201,169,110,.1);color:#c9a96e;border:1px solid rgba(201,169,110,.2)">
                            {{ $product->conditionLabel() }}
                        </span>
                    </td>
                    <td style="font-weight:600;color:#f0ece4;font-size:.88rem">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td>
                        @php
                            $sc = match($product->status) {
                                'available' => ['#4ade80','rgba(34,197,94,.1)'],
                                'locked'    => ['#facc15','rgba(234,179,8,.1)'],
                                'sold'      => ['#f87171','rgba(239,68,68,.1)'],
                                default     => ['rgba(240,236,228,.4)','rgba(255,255,255,.05)'],
                            };
                        @endphp
                        <span style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:.7rem;font-weight:600;color:{{ $sc[0] }};background:{{ $sc[1] }}">
                            {{ $product->statusLabel() }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('catalog.show', $product->slug) }}"
                               target="_blank"
                               style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:7px;padding:6px 10px;color:rgba(240,236,228,.5);font-size:.78rem;text-decoration:none"
                               title="Preview">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}"
                               style="background:rgba(201,169,110,.1);border:1px solid rgba(201,169,110,.2);border-radius:7px;padding:6px 12px;color:#c9a96e;font-size:.78rem;text-decoration:none"
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:7px;padding:6px 10px;color:#f87171;cursor:pointer;font-size:.78rem">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px;color:rgba(240,236,228,.25)">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:10px"></i>
                        Belum ada produk
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid rgba(255,255,255,.06)">
        {{ $products->withQueryString()->links() }}
    </div>
</div>

@endsection
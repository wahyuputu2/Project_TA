@extends('layouts.seller')
@section('title','Produk Saya')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 style="font-weight:700;margin:0">Produk Saya</h4>
    <a href="{{ route('seller.products.create') }}" class="btn-gold">
        <i class="bi bi-plus-lg me-2"></i>Upload Produk
    </a>
</div>

<div style="background:white;border:1.5px solid var(--border);border-radius:10px;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
        <thead style="background:#f8f8f6">
            <tr>
                <th style="padding:12px 16px;font-size:.72rem;color:var(--muted);font-weight:600;text-align:left">Produk</th>
                <th style="padding:12px 16px;font-size:.72rem;color:var(--muted);font-weight:600;text-align:left">Kondisi</th>
                <th style="padding:12px 16px;font-size:.72rem;color:var(--muted);font-weight:600;text-align:left">Harga</th>
                <th style="padding:12px 16px;font-size:.72rem;color:var(--muted);font-weight:600;text-align:left">Status</th>
                <th style="padding:12px 16px;font-size:.72rem;color:var(--muted);font-weight:600;text-align:left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr style="border-top:1px solid var(--border)">
                <td style="padding:14px 16px">
                    <div style="display:flex;align-items:center;gap:12px">
                        @if($product->mainImage())
                        <img src="{{ Storage::url($product->mainImage()->image_path) }}"
                             style="width:48px;height:60px;object-fit:cover;border-radius:6px">
                        @endif
                        <div>
                            <div style="font-size:.88rem;font-weight:600">{{ $product->name }}</div>
                            <div style="font-size:.72rem;color:var(--muted)">{{ $product->brand?->name }} · Size {{ $product->size }}</div>
                        </div>
                    </div>
                </td>
                <td style="padding:14px 16px;font-size:.82rem">{{ $product->conditionLabel() }}</td>
                <td style="padding:14px 16px;font-size:.88rem;font-weight:600">Rp {{ number_format($product->price,0,',','.') }}</td>
                <td style="padding:14px 16px">
                    @php $colors = ['available'=>'green','locked'=>'orange','sold'=>'red'] @endphp
                    <span style="font-size:.72rem;font-weight:600;color:{{ $colors[$product->status] ?? 'gray' }}">
                        {{ $product->statusLabel() }}
                    </span>
                </td>
                <td style="padding:14px 16px">
                    <div style="display:flex;gap:8px">
                        <a href="{{ route('seller.products.edit', $product) }}"
                           style="font-size:.78rem;padding:5px 12px;border:1px solid var(--border);border-radius:6px;color:var(--text);text-decoration:none">
                            Edit
                        </a>
                        <form action="{{ route('seller.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="font-size:.78rem;padding:5px 12px;border:1px solid #fecaca;border-radius:6px;background:#fef2f2;color:#dc2626;cursor:pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding:40px;text-align:center;color:var(--muted)">
                    Belum ada produk. <a href="{{ route('seller.products.create') }}">Upload sekarang</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px">{{ $products->links() }}</div>
</div>

@endsection
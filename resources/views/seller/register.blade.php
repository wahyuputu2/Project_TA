@extends('layouts.app')
@section('title', 'Daftar Jadi Seller')
@section('content')

<div class="container py-5" style="max-width:680px">
    <div class="text-center mb-5">
        <h2 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:var(--primary)">
            Buka Toko Thrift-mu
        </h2>
        <p style="color:var(--muted);font-size:.9rem">Daftar gratis, mulai jual baju bekasmu hari ini</p>
    </div>

    {{-- Benefits --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['bi-shop','Toko Online Sendiri','Kelola produk, pesanan, dan keuangan dari satu dashboard'],
            ['bi-cash-stack','Terima Pembayaran Otomatis','Integrasi Midtrans — VA, E-Wallet, QRIS'],
            ['bi-truck','Ongkir Otomatis','Kalkulasi ongkir real-time via RajaOngkir'],
        ] as [$icon, $title, $desc])
        <div class="col-md-4">
            <div style="background:#f8f8f6;border:1px solid var(--border);border-radius:10px;padding:18px;text-align:center">
                <i class="bi {{ $icon }}" style="font-size:1.5rem;color:var(--primary);margin-bottom:10px;display:block"></i>
                <div style="font-weight:600;font-size:.88rem;margin-bottom:5px">{{ $title }}</div>
                <div style="font-size:.75rem;color:var(--muted)">{{ $desc }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <form action="{{ route('seller.register.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

        {{-- Info Toko --}}
        <div class="sl-card mb-4">
            <div class="sl-card-head">📦 Informasi Toko</div>
            <div class="sl-card-body">
                <div class="mb-3">
                    <label class="sl-label">Nama Toko *</label>
                    <input type="text" name="name" class="sl-input" placeholder="Contoh: ThriftByAna" required value="{{ old('name') }}">
                </div>
                <div class="mb-3">
                    <label class="sl-label">Deskripsi Toko *</label>
                    <textarea name="description" class="sl-input sl-textarea" rows="3"
                              placeholder="Ceritakan tentang toko kamu, jenis barang yang dijual, dll..." required>{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="sl-label">No. HP / WhatsApp *</label>
                    <input type="text" name="phone" class="sl-input" placeholder="08xxxxxxxxxx" required value="{{ old('phone') }}">
                </div>
                <div class="mb-0">
                    <label class="sl-label">Logo Toko</label>
                    <input type="file" name="logo" class="sl-input" accept="image/*" style="padding:9px 12px">
                </div>
            </div>
        </div>

        {{-- Alamat --}}
        <div class="sl-card mb-4">
            <div class="sl-card-head">📍 Alamat Toko (Asal Pengiriman)</div>
            <div class="sl-card-body">
                <div class="mb-3">
                    <label class="sl-label">Alamat Lengkap *</label>
                    <textarea name="address" class="sl-input sl-textarea" rows="2" required>{{ old('address') }}</textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="sl-label">Kota *</label>
                        <input type="text" name="city" class="sl-input" required value="{{ old('city') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="sl-label">Provinsi *</label>
                        <input type="text" name="province" class="sl-input" required value="{{ old('province') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="sl-label">Kode Pos *</label>
                        <input type="text" name="postal_code" class="sl-input" required value="{{ old('postal_code') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekening --}}
        <div class="sl-card mb-4">
            <div class="sl-card-head">🏦 Rekening Bank (untuk Pencairan Dana)</div>
            <div class="sl-card-body">
                <div class="mb-3">
                    <label class="sl-label">Nama Bank *</label>
                    <select name="bank_name" class="sl-input sl-select" required>
                        <option value="">-- Pilih Bank --</option>
                        @foreach(['BCA','BNI','BRI','Mandiri','BSI','CIMB Niaga','Danamon','Permata'] as $bank)
                        <option value="{{ $bank }}" @selected(old('bank_name')===$bank)>{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="sl-label">Nomor Rekening *</label>
                    <input type="text" name="bank_account_number" class="sl-input" placeholder="1234567890" required value="{{ old('bank_account_number') }}">
                </div>
                <div class="mb-0">
                    <label class="sl-label">Nama Pemilik Rekening *</label>
                    <input type="text" name="bank_account_name" class="sl-input" placeholder="Nama sesuai buku tabungan" required value="{{ old('bank_account_name') }}">
                </div>
            </div>
        </div>

        {{-- Syarat --}}
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px 20px;margin-bottom:20px;font-size:.82rem;color:#92400e">
            <i class="bi bi-info-circle-fill me-2"></i>
            Dengan mendaftar, kamu menyetujui bahwa platform mengambil komisi <strong>10%</strong> dari setiap transaksi.
            Dana akan cair ke rekeningmu setelah pembeli menerima barang.
        </div>

        <button type="submit" class="btn-gold w-100" style="padding:14px;font-size:.9rem;text-align:center;display:block;justify-content:center">
            Daftar Jadi Seller Sekarang →
        </button>
    </form>
</div>

@endsection
@push('styles')
<style>
.sl-label { display:block;font-size:.72rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:7px }
.sl-input { width:100%;background:#fafafa;border:1.5px solid var(--border);border-radius:8px;padding:10px 14px;font-size:.88rem;color:var(--text);font-family:'Jost',sans-serif;outline:none;transition:border-color .2s }
.sl-input:focus { border-color:var(--primary) }
.sl-textarea { resize:vertical;min-height:80px }
.sl-select { appearance:none;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23999' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px }
.sl-card { background:white;border:1.5px solid var(--border);border-radius:12px;overflow:hidden }
.sl-card-head { padding:13px 20px;background:#f8f8f6;border-bottom:1px solid var(--border);font-size:.88rem;font-weight:600;color:var(--primary) }
.sl-card-body { padding:20px }
</style>
@endpush
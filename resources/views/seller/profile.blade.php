@extends('layouts.seller')
@section('title', 'Profil Toko')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-weight:700;margin-bottom:4px">Profil Toko</h4>
        <p style="font-size:.82rem;color:var(--muted);margin:0">Kelola informasi & pengaturan toko</p>
    </div>
</div>

<form action="{{ route('seller.profile.update') }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-4">
    <div class="col-lg-8">

        {{-- INFO TOKO --}}
        <div class="sf-card mb-4">
            <div class="sf-card-head">🏪 Informasi Toko</div>
            <div class="sf-card-body">
                <div class="mb-3">
                    <label class="sf-label">Nama Toko</label>
                    <input type="text" name="name" class="sf-input"
                           value="{{ old('name', $store->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="sf-label">Deskripsi Toko</label>
                    <textarea name="description" class="sf-input" rows="4"
                              style="resize:vertical;min-height:100px"
                              required>{{ old('description', $store->description) }}</textarea>
                </div>
                <div class="mb-0">
                    <label class="sf-label">No. HP / WhatsApp</label>
                    <input type="text" name="phone" class="sf-input"
                           value="{{ old('phone', $store->phone) }}" required>
                </div>
            </div>
        </div>

        {{-- ALAMAT --}}
        <div class="sf-card mb-4">
            <div class="sf-card-head">📍 Alamat Toko</div>
            <div class="sf-card-body">
                <div class="mb-3">
                    <label class="sf-label">Alamat Lengkap</label>
                    <textarea name="address" class="sf-input" rows="2"
                              style="resize:none">{{ old('address', $store->address) }}</textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="sf-label">Kota</label>
                        <input type="text" name="city" class="sf-input"
                               value="{{ old('city', $store->city) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="sf-label">Provinsi</label>
                        <input type="text" name="province" class="sf-input"
                               value="{{ old('province', $store->province) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="sf-label">Kode Pos</label>
                        <input type="text" name="postal_code" class="sf-input"
                               value="{{ old('postal_code', $store->postal_code) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- REKENING --}}
        <div class="sf-card">
            <div class="sf-card-head">🏦 Rekening Bank</div>
            <div class="sf-card-body">
                <div class="mb-3">
                    <label class="sf-label">Nama Bank</label>
                    <select name="bank_name" class="sf-input sf-select" required>
                        @foreach(['BCA','BNI','BRI','Mandiri','BSI','CIMB Niaga','Danamon','Permata'] as $bank)
                        <option value="{{ $bank }}" @selected(old('bank_name', $store->bank_name) === $bank)>{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="sf-label">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" class="sf-input"
                           value="{{ old('bank_account_number', $store->bank_account_number) }}" required>
                </div>
                <div class="mb-0">
                    <label class="sf-label">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_name" class="sf-input"
                           value="{{ old('bank_account_name', $store->bank_account_name) }}" required>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-4">

        {{-- LOGO & BANNER --}}
        <div class="sf-card mb-4">
            <div class="sf-card-head">🖼️ Logo & Banner</div>
            <div class="sf-card-body">
                <div class="mb-4">
                    <label class="sf-label">Logo Toko</label>
                    @if($store->logo)
                    <div style="margin-bottom:10px">
                        <img src="{{ Storage::url($store->logo) }}"
                             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--border)">
                    </div>
                    @endif
                    <input type="file" name="logo" class="sf-input" accept="image/*"
                           style="padding:8px 12px">
                </div>
                <div class="mb-0">
                    <label class="sf-label">Banner Toko</label>
                    @if($store->banner)
                    <div style="margin-bottom:10px">
                        <img src="{{ Storage::url($store->banner) }}"
                             style="width:100%;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
                    </div>
                    @endif
                    <input type="file" name="banner" class="sf-input" accept="image/*"
                           style="padding:8px 12px">
                </div>
            </div>
        </div>

        {{-- INFO TOKO --}}
        <div class="sf-card mb-4">
            <div class="sf-card-head">📊 Info Toko</div>
            <div class="sf-card-body">
                <div style="display:flex;justify-content:space-between;font-size:.85rem;padding:6px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--muted)">Status</span>
                    <span style="font-weight:600;color:{{ $store->isActive() ? '#16a34a' : '#d97706' }}">
                        {{ $store->statusLabel() }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.85rem;padding:6px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--muted)">Komisi Platform</span>
                    <span style="font-weight:600;color:var(--primary)">{{ $store->commission_rate }}%</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.85rem;padding:6px 0">
                    <span style="color:var(--muted)">Bergabung</span>
                    <span style="font-weight:500;color:var(--primary)">{{ $store->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- SUBMIT --}}
        <button type="submit" class="sf-submit-btn">
            <i class="bi bi-save me-2"></i>Simpan Perubahan
        </button>

    </div>
</div>
</form>

@endsection
@push('styles')
<style>
.sf-card { background:white;border:1.5px solid var(--border);border-radius:10px;overflow:hidden }
.sf-card-head { padding:13px 18px;background:#f8f8f6;border-bottom:1px solid var(--border);font-size:.88rem;font-weight:600;color:var(--primary);display:flex;align-items:center }
.sf-card-body { padding:18px }
.sf-label { display:block;font-size:.72rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:7px }
.sf-input { width:100%;background:#fafafa;border:1.5px solid var(--border);border-radius:8px;padding:10px 14px;font-size:.88rem;color:var(--primary);font-family:'Jost',sans-serif;outline:none;transition:border-color .2s }
.sf-input:focus { border-color:var(--primary) }
.sf-select { appearance:none;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23999' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px }
.sf-submit-btn { width:100%;background:var(--primary);border:none;border-radius:9px;padding:13px;font-size:.88rem;font-weight:700;color:white;cursor:pointer;font-family:'Jost',sans-serif;transition:background .2s;display:flex;align-items:center;justify-content:center }
.sf-submit-btn:hover { background:var(--accent) }
</style>
@endpush
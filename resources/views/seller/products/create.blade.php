@extends('layouts.seller')
@section('title', 'Upload Produk')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 style="font-weight:700;font-size:1.2rem;margin-bottom:3px;color:#1a1a1a">Upload Produk Baru</h4>
        <p style="font-size:.8rem;color:#888;margin:0">Isi detail produk dengan lengkap & jujur</p>
    </div>
    <a href="{{ route('seller.products.index') }}" class="up-btn-back">
        ← Kembali
    </a>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px">
    <div style="font-size:.82rem;font-weight:600;color:#dc2626;margin-bottom:8px"><i class="bi bi-exclamation-circle me-2"></i>Mohon perbaiki kesalahan berikut:</div>
    <ul style="margin:0;padding-left:20px;font-size:.8rem;color:#b91c1c">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
@csrf

<div class="row g-4">

    {{-- ══════ KIRI ══════ --}}
    <div class="col-lg-8">

        {{-- INFO PRODUK --}}
        <div class="up-card mb-4">
            <div class="up-card-head">
                <i class="bi bi-tag-fill" style="color:#3b6fe8"></i>
                Informasi Produk
            </div>
            <div class="up-card-body">
                <div class="mb-4">
                    <label class="up-label">Nama Produk <span class="up-required">*</span></label>
                    <input type="text" name="name" class="up-input {{ $errors->has('name') ? 'up-input-error' : '' }}"
                           placeholder="Contoh: Kemeja Flannel Tartan Oversized"
                           value="{{ old('name') }}" required>
                    @error('name')<p class="up-error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label class="up-label">Deskripsi Produk <span class="up-required">*</span></label>
                    <textarea name="description" rows="4" required
                              class="up-input up-textarea {{ $errors->has('description') ? 'up-input-error' : '' }}"
                              placeholder="Ceritakan detail produk: bahan, warna, motif, potongan, dll...">{{ old('description') }}</textarea>
                    @error('description')<p class="up-error-msg">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="up-label">
                        Catatan Minus / Kekurangan
                        <span style="font-size:.72rem;color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0"> — opsional, jujur apa adanya</span>
                    </label>
                    <textarea name="condition_notes" rows="3"
                              class="up-input up-textarea"
                              placeholder="Contoh: Ada noda kecil ±0.5cm di bagian bawah belakang...">{{ old('condition_notes') }}</textarea>
                    <p class="up-hint"><i class="bi bi-info-circle me-1"></i>Kejujuran meningkatkan kepercayaan pembeli. Kosongkan jika sempurna.</p>
                </div>
            </div>
        </div>

        {{-- FOTO PRODUK --}}
        <div class="up-card mb-4">
            <div class="up-card-head">
                <i class="bi bi-images" style="color:#3b6fe8"></i>
                Foto Produk
            </div>
            <div class="up-card-body">

                {{-- Dropzone --}}
                <div class="up-dropzone" id="dropzone" onclick="document.getElementById('quickUpload').click()">
                    <i class="bi bi-cloud-upload" style="font-size:2rem;color:#3b6fe8;display:block;margin-bottom:10px"></i>
                    <div style="font-size:.88rem;font-weight:600;color:#0f2044">Drag & drop foto disini</div>
                    <div style="font-size:.78rem;color:#94a3b8;margin-top:4px">atau <span style="color:#3b6fe8;font-weight:600;cursor:pointer">pilih dari komputer</span></div>
                    <div style="font-size:.7rem;color:#cbd5e1;margin-top:8px">JPG · PNG · WEBP · Maks 5MB per foto</div>
                    <input type="file" id="quickUpload" multiple accept="image/*" style="display:none">
                </div>

                {{-- Preview Grid --}}
                <div id="previewGrid" class="up-preview-grid"></div>

                {{-- Image Input Rows --}}
                <div id="imageRows"></div>

                <button type="button" id="addRowBtn" class="up-add-row-btn">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Foto Manual
                </button>

                {{-- Panduan Sudut --}}
                <div class="up-angle-guide">
                    <div style="font-size:.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;font-weight:600">Panduan Sudut Foto</div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="up-angle-chip" style="background:#eff6ff;color:#2563eb;border-color:#bfdbfe">📸 Depan</span>
                        <span class="up-angle-chip" style="background:#f0fdf4;color:#16a34a;border-color:#bbf7d0">🔄 Belakang</span>
                        <span class="up-angle-chip" style="background:#fffbeb;color:#d97706;border-color:#fde68a">🏷️ Detail Merek</span>
                        <span class="up-angle-chip" style="background:#fef2f2;color:#dc2626;border-color:#fecaca">⚠️ Detail Minus</span>
                        <span class="up-angle-chip" style="background:#f8f9fa;color:#64748b;border-color:#e2e8f0">📷 Lainnya</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- UKURAN FISIK --}}
        <div class="up-card">
            <div class="up-card-head">
                <i class="bi bi-rulers" style="color:#3b6fe8"></i>
                Ukuran Fisik
            </div>
            <div class="up-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="up-label">Panjang / P (cm)</label>
                        <div class="up-input-addon">
                            <input type="number" name="length_cm" step="0.1" min="0"
                                   class="up-input" placeholder="68.5" value="{{ old('length_cm') }}">
                            <span class="up-addon">cm</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="up-label">Lebar / L (cm)</label>
                        <div class="up-input-addon">
                            <input type="number" name="width_cm" step="0.1" min="0"
                                   class="up-input" placeholder="52.0" value="{{ old('width_cm') }}">
                            <span class="up-addon">cm</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="up-label">Berat <span class="up-required">*</span></label>
                        <div class="up-input-addon">
                            <input type="number" name="weight_gram" min="1"
                                   class="up-input" placeholder="500"
                                   value="{{ old('weight_gram', 500) }}" required>
                            <span class="up-addon">gram</span>
                        </div>
                        <p class="up-hint">Untuk kalkulasi ongkir otomatis</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════ KANAN ══════ --}}
    <div class="col-lg-4">

        {{-- HARGA --}}
        <div class="up-card mb-4">
            <div class="up-card-head">
                <i class="bi bi-cash-stack" style="color:#3b6fe8"></i>
                Harga Jual
            </div>
            <div class="up-card-body">
                <label class="up-label">Harga (Rp) <span class="up-required">*</span></label>
                <div class="up-price-wrap">
                    <span class="up-price-prefix">Rp</span>
                    <input type="number" name="price" min="0" id="priceInput"
                           class="up-input {{ $errors->has('price') ? 'up-input-error' : '' }}"
                           placeholder="85000" value="{{ old('price') }}"
                           style="padding-left:42px" required>
                </div>
                <div id="pricePreview" style="display:none;margin-top:8px;padding:8px 12px;background:#f0f7ff;border:1px solid #bfdbfe;border-radius:7px;font-size:.78rem;color:#1d4ed8">
                    <i class="bi bi-check-circle me-1"></i>Tampil sebagai: <strong id="priceFormatted"></strong>
                </div>
                @error('price')<p class="up-error-msg">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- KLASIFIKASI --}}
        <div class="up-card mb-4">
            <div class="up-card-head">
                <i class="bi bi-grid-3x3-gap" style="color:#3b6fe8"></i>
                Klasifikasi
            </div>
            <div class="up-card-body">
                <div class="mb-3">
                    <label class="up-label">Kategori <span class="up-required">*</span></label>
                    <select name="category_id" class="up-input up-select {{ $errors->has('category_id') ? 'up-input-error' : '' }}" required>
                        <option value="">— Pilih Kategori —</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="up-error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="mb-3">
                    <label class="up-label">Brand / Merek</label>
                    <select name="brand_id" class="up-input up-select">
                        <option value="">No Brand / Tidak Diketahui</option>
                        @foreach($brands as $b)
                        <option value="{{ $b->id }}" @selected(old('brand_id') == $b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="up-label">Gender</label>
                    <div class="d-flex gap-2">
                        @foreach(['unisex' => '🤝 Unisex', 'pria' => '👔 Pria', 'wanita' => '👗 Wanita'] as $val => $lbl)
                        <label style="flex:1;cursor:pointer;text-align:center">
                            <input type="radio" name="gender" value="{{ $val }}"
                                   @checked(old('gender','unisex')===$val)
                                   class="up-radio-hidden" data-group="gender">
                            <span class="up-pill {{ old('gender','unisex')===$val ? 'up-pill-active' : '' }}">{{ $lbl }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- KONDISI --}}
        <div class="up-card mb-4">
            <div class="up-card-head">
                <i class="bi bi-patch-check" style="color:#3b6fe8"></i>
                Kondisi Barang
            </div>
            <div class="up-card-body" style="padding:12px">
                <div class="d-flex flex-column gap-2">
                    @foreach([
                        'like_new' => ['Like New', '✨', 'Seperti baru, tidak ada cacat', '#f0fdf4', '#16a34a'],
                        '9/10'     => ['9/10', '⭐', 'Hampir sempurna, sangat minor', '#eff6ff', '#2563eb'],
                        '8/10'     => ['8/10', '👍', 'Baik, sedikit tanda pemakaian', '#f0f9ff', '#0284c7'],
                        '7/10'     => ['7/10', '👌', 'Cukup baik, ada bekas pakai', '#fefce8', '#ca8a04'],
                        '6/10'     => ['6/10', '🆗', 'Masih layak, ada kekurangan', '#fff7ed', '#ea580c'],
                    ] as $val => [$lbl, $icon, $desc, $bg, $clr])
                    <label style="cursor:pointer">
                        <input type="radio" name="condition" value="{{ $val }}"
                               @checked(old('condition','9/10')===$val)
                               class="up-radio-hidden" data-group="condition">
                        <div class="up-condition-item {{ old('condition','9/10')===$val ? 'up-condition-active' : '' }}"
                             style="{{ old('condition','9/10')===$val ? '--cbg:'.$bg.';--cclr:'.$clr : '' }}">
                            <span style="font-size:1.1rem;width:26px;text-align:center">{{ $icon }}</span>
                            <div style="flex:1">
                                <div style="font-size:.85rem;font-weight:600;color:#0f2044">{{ $lbl }}</div>
                                <div style="font-size:.7rem;color:#64748b;margin-top:1px">{{ $desc }}</div>
                            </div>
                            <div class="up-condition-check {{ old('condition','9/10')===$val ? 'up-condition-checked' : '' }}">
                                <i class="bi bi-check2" style="font-size:.65rem"></i>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('condition')<p class="up-error-msg mt-2">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- UKURAN --}}
        <div class="up-card mb-4">
            <div class="up-card-head">
                <i class="bi bi-t-square" style="color:#3b6fe8"></i>
                Ukuran / Size
            </div>
            <div class="up-card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['XS','S','M','L','XL','XXL','XXXL','27','28','29','30','31','32','33','34','35','36','37','38','39','40','Free Size'] as $sz)
                    <label style="cursor:pointer">
                        <input type="radio" name="size" value="{{ $sz }}"
                               @checked(old('size')===$sz)
                               class="up-radio-hidden" data-group="size">
                        <span class="up-size-btn {{ old('size')===$sz ? 'up-size-active' : '' }}" style="font-size:{{ strlen($sz)>3 ? '.6rem' : '.75rem' }}">{{ $sz }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SUBMIT --}}
        <div class="up-card">
            <div class="up-card-body">
                <button type="submit" class="up-submit-btn">
                    <i class="bi bi-cloud-upload me-2"></i>Upload & Simpan Produk
                </button>
                <a href="{{ route('seller.products.index') }}" class="up-cancel-btn">
                    Batal
                </a>
            </div>
        </div>

    </div>
</div>
</form>

@endsection
@push('styles')
<style>
/* ── LABELS & INPUTS ── */
.up-label {
    display: block;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .6px;
    text-transform: uppercase;
    color: #475569;
    margin-bottom: 7px;
}
.up-required { color: #ef4444; }
.up-input {
    display: block;
    width: 100%;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    padding: 10px 14px;
    font-size: .88rem;
    color: #0f2044;
    font-family: inherit;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.up-input:focus {
    border-color: #3b6fe8;
    box-shadow: 0 0 0 3px rgba(59,111,232,.08);
}
.up-input::placeholder { color: #cbd5e1; }
.up-input-error { border-color: #ef4444 !important; }
.up-textarea { resize: vertical; min-height: 90px; line-height: 1.6; }
.up-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
    cursor: pointer;
}
.up-error-msg { font-size: .72rem; color: #dc2626; margin-top: 5px; margin-bottom: 0; }
.up-hint { font-size: .72rem; color: #94a3b8; margin-top: 5px; margin-bottom: 0; }

/* ── CARDS ── */
.up-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(15,32,68,.04);
}
.up-card-head {
    padding: 14px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-size: .88rem;
    font-weight: 700;
    color: #0f2044;
    display: flex;
    align-items: center;
    gap: 8px;
}
.up-card-body { padding: 20px 18px; }

/* ── BACK BTN ── */
.up-btn-back {
    background: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    padding: 8px 16px;
    font-size: .8rem;
    font-weight: 500;
    color: #64748b;
    text-decoration: none;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.up-btn-back:hover { border-color: #3b6fe8; color: #3b6fe8; }

/* ── PRICE ── */
.up-price-wrap { position: relative; }
.up-price-prefix {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: .78rem;
    color: #94a3b8;
    font-weight: 600;
    pointer-events: none;
}

/* ── INPUT ADDON ── */
.up-input-addon { position: relative; }
.up-addon {
    position: absolute;
    right: 13px;
    top: 50%;
    transform: translateY(-50%);
    font-size: .72rem;
    color: #94a3b8;
    font-weight: 600;
    pointer-events: none;
}
.up-input-addon .up-input { padding-right: 48px; }

/* ── PILLS (GENDER) ── */
.up-radio-hidden { display: none; }
.up-pill {
    display: block;
    padding: 8px 6px;
    border-radius: 9px;
    border: 1.5px solid #e2e8f0;
    color: #64748b;
    font-size: .75rem;
    font-weight: 500;
    transition: all .15s;
    text-align: center;
    white-space: nowrap;
}
.up-pill:hover { border-color: #3b6fe8; color: #3b6fe8; }
.up-pill-active { background: #0f2044; border-color: #0f2044; color: white; font-weight: 600; }

/* ── CONDITION ── */
.up-condition-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    transition: all .15s;
}
.up-condition-item:hover { border-color: #3b6fe8; background: #f8fafc; }
.up-condition-active {
    border-color: #3b6fe8 !important;
    background: var(--cbg, #eff6ff) !important;
}
.up-condition-check {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1.5px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: transparent;
    flex-shrink: 0;
    transition: all .15s;
}
.up-condition-checked {
    background: #3b6fe8;
    border-color: #3b6fe8;
    color: white;
}

/* ── SIZE ── */
.up-size-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 6px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all .15s;
    text-align: center;
    line-height: 1.2;
}
.up-size-btn:hover { border-color: #3b6fe8; color: #3b6fe8; }
.up-size-active { border-color: #0f2044; background: #0f2044; color: white; }

/* ── DROPZONE ── */
.up-dropzone {
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    padding: 28px 20px;
    text-align: center;
    cursor: pointer;
    background: #f8fafc;
    transition: all .2s;
    margin-bottom: 16px;
}
.up-dropzone:hover, .up-dropzone.drag-over {
    border-color: #3b6fe8;
    background: #eff6ff;
}

/* ── PREVIEW GRID ── */
.up-preview-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 12px;
}
.up-preview-item {
    position: relative;
    aspect-ratio: 3/4;
    border-radius: 9px;
    overflow: hidden;
    border: 1.5px solid #e2e8f0;
}
.up-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.up-preview-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    background: rgba(220,38,38,.85);
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: .62rem;
    cursor: pointer;
}
.up-preview-angle {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(15,32,68,.65);
    font-size: .58rem;
    color: white;
    text-align: center;
    padding: 3px;
}

/* ── IMAGE ROW ── */
.up-image-row {
    display: grid;
    grid-template-columns: 1fr 150px 36px;
    gap: 8px;
    align-items: center;
    padding: 10px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    margin-bottom: 8px;
}
.up-image-row .up-input { padding: 8px 10px; font-size: .78rem; }
.up-row-del {
    width: 36px;
    height: 36px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dc2626;
    cursor: pointer;
    font-size: .85rem;
    transition: all .15s;
}
.up-row-del:hover { background: #fee2e2; }
.up-add-row-btn {
    width: 100%;
    background: transparent;
    border: 1.5px dashed #e2e8f0;
    border-radius: 9px;
    padding: 9px;
    font-size: .8rem;
    color: #94a3b8;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 6px;
}
.up-add-row-btn:hover { border-color: #3b6fe8; color: #3b6fe8; background: #f8fafc; }

/* ── ANGLE GUIDE ── */
.up-angle-guide {
    margin-top: 14px;
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
}
.up-angle-chip {
    font-size: .72rem;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid;
    font-weight: 500;
}

/* ── SUBMIT ── */
.up-submit-btn {
    width: 100%;
    background: #0f2044;
    border: none;
    border-radius: 10px;
    padding: 13px;
    font-size: .9rem;
    font-weight: 700;
    color: white;
    cursor: pointer;
    font-family: inherit;
    transition: background .2s;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.up-submit-btn:hover { background: #3b6fe8; }
.up-cancel-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    background: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 11px;
    font-size: .85rem;
    color: #64748b;
    text-decoration: none;
    transition: all .2s;
}
.up-cancel-btn:hover { border-color: #3b6fe8; color: #3b6fe8; }
</style>
@endpush
@push('scripts')
<script>
// ── PRICE FORMAT ──
const priceInput     = document.getElementById('priceInput');
const pricePreview   = document.getElementById('pricePreview');
const priceFormatted = document.getElementById('priceFormatted');
priceInput?.addEventListener('input', function() {
    const val = parseInt(this.value);
    if (val > 0) {
        priceFormatted.textContent = 'Rp ' + val.toLocaleString('id-ID');
        pricePreview.style.display = 'block';
    } else {
        pricePreview.style.display = 'none';
    }
});
// Trigger on load jika ada old value
if (priceInput?.value > 0) {
    priceFormatted.textContent = 'Rp ' + parseInt(priceInput.value).toLocaleString('id-ID');
    pricePreview.style.display = 'block';
}

// ── RADIO PILLS ──
document.querySelectorAll('.up-radio-hidden').forEach(radio => {
    radio.addEventListener('change', function() {
        const group = this.dataset.group;

        if (group === 'gender') {
            document.querySelectorAll('[data-group="gender"]').forEach(r => {
                r.nextElementSibling.classList.remove('up-pill-active');
            });
            this.nextElementSibling.classList.add('up-pill-active');
        }

        if (group === 'condition') {
            document.querySelectorAll('[data-group="condition"]').forEach(r => {
                r.nextElementSibling.classList.remove('up-condition-active');
                r.nextElementSibling.querySelector('.up-condition-check').classList.remove('up-condition-checked');
            });
            this.nextElementSibling.classList.add('up-condition-active');
            this.nextElementSibling.querySelector('.up-condition-check').classList.add('up-condition-checked');
        }

        if (group === 'size') {
            document.querySelectorAll('[data-group="size"]').forEach(r => {
                r.nextElementSibling.classList.remove('up-size-active');
            });
            this.nextElementSibling.classList.add('up-size-active');
        }
    });
});

// ── PHOTO UPLOAD ──
const dropzone    = document.getElementById('dropzone');
const quickUpload = document.getElementById('quickUpload');
const previewGrid = document.getElementById('previewGrid');
const imageRows   = document.getElementById('imageRows');
let rowIdx = 0;

const angleOpts = `
    <option value="front">📸 Depan</option>
    <option value="back">🔄 Belakang</option>
    <option value="brand_detail">🏷️ Detail Merek</option>
    <option value="minus_detail">⚠️ Detail Minus</option>
    <option value="other">📷 Lainnya</option>
`;
const angleLabels = {
    front: 'Depan', back: 'Belakang',
    brand_detail: 'Detail Merek', minus_detail: 'Detail Minus', other: 'Lainnya'
};

function addRow(file = null) {
    const idx = rowIdx++;
    const row = document.createElement('div');
    row.className = 'up-image-row';
    row.id = 'row-' + idx;
    row.innerHTML = `
        <input type="file" name="images[]" class="up-input" accept="image/*" style="padding:7px 10px;font-size:.78rem">
        <select name="image_angles[]" class="up-input up-select" id="angle-${idx}"
                onchange="updateAngle(${idx},this.value)"
                style="padding:8px 10px;font-size:.78rem">${angleOpts}</select>
        <button type="button" class="up-row-del" onclick="removeRow(${idx})">
            <i class="bi bi-trash3"></i>
        </button>
    `;
    imageRows.appendChild(row);

    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        row.querySelector('input[type=file]').files = dt.files;
        addPreview(file, idx);
        row.querySelector('input[type=file]').style.display = 'none';
    }
}

function addPreview(file, idx) {
    const reader = new FileReader();
    reader.onload = e => {
        const item = document.createElement('div');
        item.className = 'up-preview-item';
        item.id = 'preview-' + idx;
        item.innerHTML = `
            <img src="${e.target.result}" alt="preview">
            <button type="button" class="up-preview-remove" onclick="removeRow(${idx})">✕</button>
            <div class="up-preview-angle" id="alabel-${idx}">Depan</div>
        `;
        previewGrid.appendChild(item);
    };
    reader.readAsDataURL(file);
}

function updateAngle(idx, val) {
    const lbl = document.getElementById('alabel-' + idx);
    if (lbl) lbl.textContent = angleLabels[val] || val;
}

function removeRow(idx) {
    document.getElementById('row-' + idx)?.remove();
    document.getElementById('preview-' + idx)?.remove();
}

dropzone.addEventListener('click', () => quickUpload.click());
dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('drag-over');
    Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/')).forEach(f => addRow(f));
});
quickUpload.addEventListener('change', function() {
    Array.from(this.files).forEach(f => addRow(f));
    this.value = '';
});
document.getElementById('addRowBtn').addEventListener('click', () => addRow());

// Default 1 baris kosong
addRow();
</script>
@endpush
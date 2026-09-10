@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('content')

{{-- PAGE HEADER --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:#f0ece4;margin-bottom:4px">
            Tambah Produk Baru
        </h4>
        <p style="font-size:.82rem;color:rgba(240,236,228,.35);margin:0">
            Setiap item bersifat 1-of-1 · Isi detail dengan jujur untuk membangun kepercayaan
        </p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="adm-btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
@csrf

<div class="row g-4">

{{-- ══ KIRI ══ --}}
<div class="col-xl-8">

    {{-- INFO PRODUK --}}
    <div class="adm-card mb-4">
        <div class="adm-card-header">
            <i class="bi bi-tag-fill"></i> Informasi Produk
        </div>
        <div class="adm-card-body">
            <div class="mb-4">
                <label class="cp-label">Nama Produk <span style="color:#f87171">*</span></label>
                <input type="text" name="name" class="adm-input @error('name') cp-invalid @enderror"
                       placeholder="Contoh: Kemeja Flannel Tartan Oversized — H&M" value="{{ old('name') }}" required>
                @error('name')<p class="cp-err">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="cp-label">Deskripsi Produk <span style="color:#f87171">*</span></label>
                <textarea name="description" rows="4" class="adm-input cp-textarea @error('description') cp-invalid @enderror"
                          placeholder="Ceritakan detail produk: bahan, warna, motif, potongan, dll..." required>{{ old('description') }}</textarea>
                @error('description')<p class="cp-err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="cp-label">
                    Catatan Kondisi / Minus
                    <span class="cp-label-sub">— jujur apa adanya</span>
                </label>
                <textarea name="condition_notes" rows="3" class="adm-input cp-textarea"
                          placeholder="Contoh: Ada noda kecil ±0.5cm di bagian bawah belakang, warna sedikit pudar di area kerah. Tidak mengganggu penampilan keseluruhan.">{{ old('condition_notes') }}</textarea>
                <div class="cp-hint mt-2">
                    <i class="bi bi-lightbulb me-1"></i>
                    Kosongkan jika kondisi sempurna. Kejujuran meningkatkan kepercayaan & rating toko.
                </div>
            </div>
        </div>
    </div>

    {{-- FOTO PRODUK --}}
    <div class="adm-card mb-4">
        <div class="adm-card-header">
            <i class="bi bi-images"></i> Foto Produk
            <span style="margin-left:auto;font-size:.72rem;font-weight:400;color:rgba(240,236,228,.3)">
                Upload dari berbagai sudut untuk transparansi maksimal
            </span>
        </div>
        <div class="adm-card-body">

            {{-- Dropzone --}}
            <div class="cp-dropzone" id="dropzone">
                <div class="cp-dropzone-icon">
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>
                <div class="cp-dropzone-text">
                    Drag & drop foto disini
                </div>
                <div class="cp-dropzone-sub">
                    atau <span class="cp-dropzone-browse" onclick="document.getElementById('quickUpload').click()">pilih dari komputer</span>
                </div>
                <div class="cp-dropzone-info">JPG · PNG · WEBP · Maks 5MB per foto</div>
                <input type="file" id="quickUpload" multiple accept="image/*" style="display:none">
            </div>

            {{-- Preview Grid --}}
            <div id="previewGrid" class="cp-preview-grid"></div>

            {{-- Manual Rows --}}
            <div id="imageRows"></div>

            {{-- Add Button --}}
            <button type="button" id="addRowBtn" class="cp-add-row-btn">
                <i class="bi bi-plus-circle"></i> Tambah Baris Foto Manual
            </button>

            {{-- Angle Guide --}}
            <div class="cp-angle-guide">
                <div class="cp-angle-guide-title">Panduan Sudut Foto</div>
                <div class="cp-angle-tags">
                    <span class="cp-angle-tag" style="background:rgba(59,130,246,.08);border-color:rgba(59,130,246,.2);color:#60a5fa">📸 Depan</span>
                    <span class="cp-angle-tag" style="background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.2);color:#34d399">🔄 Belakang</span>
                    <span class="cp-angle-tag" style="background:rgba(201,169,110,.08);border-color:rgba(201,169,110,.2);color:#c9a96e">🏷️ Detail Merek</span>
                    <span class="cp-angle-tag" style="background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.2);color:#f87171">⚠️ Detail Minus</span>
                    <span class="cp-angle-tag" style="background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.08);color:rgba(240,236,228,.4)">📷 Lainnya</span>
                </div>
            </div>
        </div>
    </div>

    {{-- UKURAN FISIK --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <i class="bi bi-rulers"></i> Ukuran Fisik
        </div>
        <div class="adm-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="cp-label">Panjang / P</label>
                    <div class="cp-input-affixed">
                        <input type="number" name="length_cm" step="0.1" min="0"
                               class="adm-input" placeholder="68.5" value="{{ old('length_cm') }}">
                        <span class="cp-suffix">cm</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="cp-label">Lebar / L</label>
                    <div class="cp-input-affixed">
                        <input type="number" name="width_cm" step="0.1" min="0"
                               class="adm-input" placeholder="52.0" value="{{ old('width_cm') }}">
                        <span class="cp-suffix">cm</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="cp-label">Berat <span style="color:#f87171">*</span></label>
                    <div class="cp-input-affixed">
                        <input type="number" name="weight_gram" min="1"
                               class="adm-input" placeholder="500" value="{{ old('weight_gram', 500) }}" required>
                        <span class="cp-suffix">gr</span>
                    </div>
                    <div class="cp-hint mt-1">Untuk kalkulasi ongkir otomatis</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ KANAN ══ --}}
<div class="col-xl-4">

    {{-- HARGA --}}
    <div class="adm-card mb-4">
        <div class="adm-card-header">
            <i class="bi bi-cash-stack"></i> Harga Jual
        </div>
        <div class="adm-card-body">
            <label class="cp-label">Harga (Rp) <span style="color:#f87171">*</span></label>
            <div class="cp-input-affixed">
                <span class="cp-prefix">Rp</span>
                <input type="number" name="price" min="0" id="priceInput"
                       class="adm-input @error('price') cp-invalid @enderror"
                       placeholder="85000" value="{{ old('price') }}" required>
            </div>
            <div class="cp-price-preview" id="pricePreview">
                <i class="bi bi-tag me-1"></i>
                Akan tampil sebagai: <strong id="priceFormatted"></strong>
            </div>
            @error('price')<p class="cp-err">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- KLASIFIKASI --}}
    <div class="adm-card mb-4">
        <div class="adm-card-header">
            <i class="bi bi-collection"></i> Klasifikasi
        </div>
        <div class="adm-card-body">
            <div class="mb-3">
                <label class="cp-label">Kategori <span style="color:#f87171">*</span></label>
                <select name="category_id" class="adm-select @error('category_id') cp-invalid @enderror" required>
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="cp-err">{{ $message }}</p>@enderror
            </div>
            <div class="mb-3">
                <label class="cp-label">Brand / Merek</label>
                <select name="brand_id" class="adm-select">
                    <option value="">No Brand / Tidak Diketahui</option>
                    @foreach($brands as $b)
                    <option value="{{ $b->id }}" @selected(old('brand_id') == $b->id)>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="cp-label">Gender</label>
                <div class="cp-pill-group">
                    @foreach(['unisex'=>'🤝 Unisex','pria'=>'👔 Pria','wanita'=>'👗 Wanita'] as $val=>$lbl)
                    <label class="cp-pill">
                        <input type="radio" name="gender" value="{{ $val }}" @checked(old('gender','unisex')===$val)>
                        <span>{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- KONDISI --}}
    <div class="adm-card mb-4">
        <div class="adm-card-header">
            <i class="bi bi-patch-check"></i> Kondisi Barang
        </div>
        <div class="adm-card-body">
            <div class="cp-condition-list">
                @foreach([
                    'like_new' => ['Like New','✨','Seperti baru, tidak ada cacat','#4ade80','rgba(34,197,94,.08)','rgba(34,197,94,.2)'],
                    '9/10'     => ['9/10','⭐','Hampir sempurna, sangat minor','#c9a96e','rgba(201,169,110,.08)','rgba(201,169,110,.2)'],
                    '8/10'     => ['8/10','👍','Baik, sedikit tanda pemakaian','#60a5fa','rgba(59,130,246,.08)','rgba(59,130,246,.2)'],
                    '7/10'     => ['7/10','👌','Cukup baik, ada bekas pakai','#a78bfa','rgba(139,92,246,.08)','rgba(139,92,246,.2)'],
                    '6/10'     => ['6/10','🆗','Masih layak, ada kekurangan','#f87171','rgba(239,68,68,.08)','rgba(239,68,68,.2)'],
                ] as $val => [$lbl, $icon, $desc, $color, $bg, $border])
                <label class="cp-condition-item">
                    <input type="radio" name="condition" value="{{ $val }}" @checked(old('condition','9/10')===$val)>
                    <span class="cp-condition-box" data-color="{{ $color }}" data-bg="{{ $bg }}" data-border="{{ $border }}">
                        <span class="cp-cond-icon">{{ $icon }}</span>
                        <span class="cp-cond-info">
                            <span class="cp-cond-lbl">{{ $lbl }}</span>
                            <span class="cp-cond-desc">{{ $desc }}</span>
                        </span>
                        <span class="cp-cond-check"><i class="bi bi-check2"></i></span>
                    </span>
                </label>
                @endforeach
            </div>
            @error('condition')<p class="cp-err">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- UKURAN --}}
    <div class="adm-card mb-4">
        <div class="adm-card-header">
            <i class="bi bi-grid-3x3"></i> Ukuran / Size
        </div>
        <div class="adm-card-body">
            <div class="cp-size-grid">
                @foreach(['XS','S','M','L','XL','XXL','XXXL','27','28','29','30','31','32','33','34','35','36','37','38','39','40'] as $sz)
                <label class="cp-size-item">
                    <input type="radio" name="size" value="{{ $sz }}" @checked(old('size')===$sz)>
                    <span>{{ $sz }}</span>
                </label>
                @endforeach
                <label class="cp-size-item">
                    <input type="radio" name="size" value="" @checked(!old('size'))>
                    <span style="font-size:.6rem;line-height:1.2">FREE<br>SIZE</span>
                </label>
            </div>
        </div>
    </div>

    {{-- SUBMIT --}}
    <div class="adm-card">
        <div class="adm-card-body">
            <button type="submit" class="cp-submit-btn">
                <i class="bi bi-cloud-upload-fill me-2"></i>Upload & Simpan Produk
            </button>
            <a href="{{ route('admin.products.index') }}" class="cp-cancel-btn">Batal</a>
        </div>
    </div>

</div>
</div>
</form>

@endsection

@push('styles')
<style>
/* ── LABELS & HINTS ── */
.cp-label {
    display: block;
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: rgba(240,236,228,.4);
    margin-bottom: 8px;
}
.cp-label-sub { font-size: .7rem; color: rgba(240,236,228,.25); font-weight: 400; text-transform: none; letter-spacing: 0; }
.cp-hint { font-size: .72rem; color: rgba(240,236,228,.28); display: flex; align-items: flex-start; gap: 4px; }
.cp-err { font-size: .75rem; color: #f87171; margin-top: 5px; margin-bottom: 0; }
.cp-invalid { border-color: #f87171 !important; }
.cp-textarea { resize: vertical; min-height: 100px; line-height: 1.6; }

/* ── INPUT AFFIXED ── */
.cp-input-affixed { position: relative; }
.cp-input-affixed .adm-input { padding-right: 40px; }
.cp-input-affixed .adm-input:first-of-type { padding-left: 38px; }
.cp-prefix {
    position: absolute; left: 13px; top: 50%;
    transform: translateY(-50%);
    font-size: .78rem; color: rgba(240,236,228,.3);
    pointer-events: none; z-index: 1;
}
.cp-suffix {
    position: absolute; right: 13px; top: 50%;
    transform: translateY(-50%);
    font-size: .72rem; color: rgba(240,236,228,.25);
    pointer-events: none;
}

/* ── PRICE PREVIEW ── */
.cp-price-preview {
    display: none;
    margin-top: 10px;
    padding: 8px 12px;
    background: rgba(201,169,110,.06);
    border: 1px solid rgba(201,169,110,.15);
    border-radius: 8px;
    font-size: .78rem;
    color: rgba(240,236,228,.5);
}
.cp-price-preview strong { color: #c9a96e; }

/* ── DROPZONE ── */
.cp-dropzone {
    border: 2px dashed rgba(201,169,110,.2);
    border-radius: 12px;
    padding: 36px 24px;
    text-align: center;
    cursor: pointer;
    background: rgba(201,169,110,.02);
    transition: all .25s;
    margin-bottom: 16px;
}
.cp-dropzone:hover, .cp-dropzone.drag-over {
    border-color: #c9a96e;
    background: rgba(201,169,110,.06);
}
.cp-dropzone-icon { font-size: 2.2rem; color: rgba(201,169,110,.4); margin-bottom: 12px; }
.cp-dropzone-text { font-size: .9rem; color: rgba(240,236,228,.55); font-weight: 500; }
.cp-dropzone-sub { font-size: .8rem; color: rgba(240,236,228,.3); margin-top: 4px; }
.cp-dropzone-browse { color: #c9a96e; font-weight: 600; cursor: pointer; text-decoration: underline; text-decoration-style: dotted; }
.cp-dropzone-info { font-size: .7rem; color: rgba(240,236,228,.2); margin-top: 12px; }

/* ── PREVIEW GRID ── */
.cp-preview-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 12px;
}
.cp-preview-item {
    position: relative;
    aspect-ratio: 3/4;
    border-radius: 9px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.08);
}
.cp-preview-item img { width: 100%; height: 100%; object-fit: cover; }
.cp-preview-remove {
    position: absolute; top: 5px; right: 5px;
    background: rgba(14,14,15,.85);
    border: none; border-radius: 50%;
    width: 22px; height: 22px;
    display: flex; align-items: center; justify-content: center;
    color: #f87171; font-size: .65rem; cursor: pointer;
    transition: background .15s;
}
.cp-preview-remove:hover { background: rgba(239,68,68,.3); }
.cp-preview-angle {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: rgba(14,14,15,.8);
    font-size: .58rem; color: #c9a96e; text-align: center;
    padding: 3px 4px;
}

/* ── IMAGE ROWS ── */
.cp-image-row {
    display: grid;
    grid-template-columns: 1fr 140px 36px;
    gap: 8px;
    align-items: center;
    padding: 10px 12px;
    background: rgba(255,255,255,.02);
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 9px;
    margin-bottom: 7px;
}
.cp-image-row .adm-input { padding: 8px 12px; font-size: .8rem; }
.cp-image-row .adm-select { padding: 8px 28px 8px 10px; font-size: .78rem; }
.cp-row-del {
    width: 36px; height: 36px;
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.15);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #f87171; font-size: .8rem; cursor: pointer;
    transition: all .15s;
}
.cp-row-del:hover { background: rgba(239,68,68,.18); }
.cp-add-row-btn {
    width: 100%;
    background: transparent;
    border: 1px dashed rgba(255,255,255,.1);
    border-radius: 9px;
    padding: 10px;
    font-size: .8rem;
    color: rgba(240,236,228,.35);
    cursor: pointer;
    font-family: 'Outfit', sans-serif;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    transition: all .2s; margin-top: 6px;
}
.cp-add-row-btn:hover { border-color: #c9a96e; color: #c9a96e; }

/* ── ANGLE GUIDE ── */
.cp-angle-guide {
    margin-top: 14px;
    padding: 12px 14px;
    background: rgba(255,255,255,.02);
    border: 1px solid rgba(255,255,255,.05);
    border-radius: 8px;
}
.cp-angle-guide-title { font-size: .65rem; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(240,236,228,.25); margin-bottom: 8px; }
.cp-angle-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.cp-angle-tag { font-size: .7rem; padding: 3px 10px; border-radius: 20px; border: 1px solid; font-weight: 500; }

/* ── PILLS ── */
.cp-pill-group { display: flex; gap: 6px; flex-wrap: wrap; }
.cp-pill input[type=radio] { display: none; }
.cp-pill span {
    display: block; padding: 7px 14px;
    border-radius: 50px; border: 1px solid rgba(255,255,255,.1);
    color: rgba(240,236,228,.45); font-size: .8rem;
    cursor: pointer; transition: all .18s; white-space: nowrap;
}
.cp-pill input:checked + span { background: #c9a96e; border-color: #c9a96e; color: #0e0e0f; font-weight: 600; }
.cp-pill span:hover { border-color: rgba(201,169,110,.5); color: #c9a96e; }

/* ── CONDITION LIST ── */
.cp-condition-list { display: flex; flex-direction: column; gap: 6px; }
.cp-condition-item input[type=radio] { display: none; }
.cp-condition-box {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 9px;
    border: 1px solid rgba(255,255,255,.07);
    cursor: pointer; transition: all .18s;
    background: rgba(255,255,255,.02);
}
.cp-condition-item input:checked + .cp-condition-box {
    border-color: var(--active-border, rgba(201,169,110,.3));
    background: var(--active-bg, rgba(201,169,110,.06));
}
.cp-condition-box:hover { border-color: rgba(255,255,255,.15); }
.cp-cond-icon { font-size: 1.1rem; width: 26px; text-align: center; flex-shrink: 0; }
.cp-cond-info { flex: 1; }
.cp-cond-lbl { display: block; font-size: .85rem; font-weight: 600; color: #f0ece4; line-height: 1; }
.cp-cond-desc { display: block; font-size: .7rem; color: rgba(240,236,228,.35); margin-top: 3px; }
.cp-cond-check {
    width: 20px; height: 20px;
    border-radius: 50%; border: 1.5px solid rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; color: transparent; flex-shrink: 0; transition: all .18s;
}
.cp-condition-item input:checked + .cp-condition-box .cp-cond-check {
    background: #c9a96e; border-color: #c9a96e; color: #0e0e0f;
}

/* ── SIZE GRID ── */
.cp-size-grid { display: flex; flex-wrap: wrap; gap: 6px; }
.cp-size-item input[type=radio] { display: none; }
.cp-size-item span {
    display: flex; align-items: center; justify-content: center;
    width: 40px; height: 40px;
    border: 1px solid rgba(255,255,255,.1); border-radius: 8px;
    font-size: .75rem; font-weight: 600; color: rgba(240,236,228,.4);
    cursor: pointer; transition: all .15s; text-align: center;
}
.cp-size-item input:checked + span { border-color: #c9a96e; background: rgba(201,169,110,.1); color: #c9a96e; }
.cp-size-item span:hover { border-color: rgba(201,169,110,.4); color: rgba(201,169,110,.8); }

/* ── SUBMIT ── */
.cp-submit-btn {
    width: 100%; background: #c9a96e; border: none;
    border-radius: 10px; padding: 14px;
    font-size: .92rem; font-weight: 600; color: #0e0e0f;
    cursor: pointer; font-family: 'Outfit', sans-serif;
    transition: background .2s; margin-bottom: 8px;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.cp-submit-btn:hover { background: #b8913d; }
.cp-cancel-btn {
    display: flex; align-items: center; justify-content: center;
    width: 100%; text-align: center;
    background: transparent; border: 1px solid rgba(255,255,255,.08);
    border-radius: 10px; padding: 11px;
    font-size: .85rem; color: rgba(240,236,228,.35);
    text-decoration: none; transition: all .2s;
}
.cp-cancel-btn:hover { border-color: rgba(255,255,255,.18); color: rgba(240,236,228,.6); }
</style>
@endpush

@push('scripts')
<script>
// ── FORMAT HARGA ──
const priceInput   = document.getElementById('priceInput');
const pricePreview = document.getElementById('pricePreview');
const priceFormatted = document.getElementById('priceFormatted');
priceInput.addEventListener('input', function () {
    const val = parseInt(this.value);
    if (val > 0) {
        priceFormatted.textContent = 'Rp ' + val.toLocaleString('id-ID');
        pricePreview.style.display = 'flex';
        pricePreview.style.alignItems = 'center';
        pricePreview.style.gap = '6px';
    } else {
        pricePreview.style.display = 'none';
    }
});

// ── KONDISI WARNA DINAMIS ──
document.querySelectorAll('.cp-condition-item input[type=radio]').forEach(radio => {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.cp-condition-box').forEach(box => {
            box.style.removeProperty('border-color');
            box.style.removeProperty('background');
        });
        if (this.checked) {
            const box = this.nextElementSibling;
            box.style.borderColor = box.dataset.border;
            box.style.background  = box.dataset.bg;
        }
    });
    // Set initial state
    if (radio.checked) {
        const box = radio.nextElementSibling;
        box.style.borderColor = box.dataset.border;
        box.style.background  = box.dataset.bg;
    }
});

// ── UPLOAD & PREVIEW ──
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
const angleLabels = { front:'Depan', back:'Belakang', brand_detail:'Detail Merek', minus_detail:'Detail Minus', other:'Lainnya' };

function addRow(file = null) {
    const idx = rowIdx++;
    const row = document.createElement('div');
    row.className = 'cp-image-row';
    row.id = 'row-' + idx;
    row.innerHTML = `
        <input type="file" name="images[]" class="adm-input" accept="image/*" style="padding:7px 10px;font-size:.78rem">
        <select name="image_angles[]" class="adm-select" id="angle-${idx}" onchange="updateAngleLabel(${idx}, this.value)">${angleOpts}</select>
        <button type="button" class="cp-row-del" onclick="removeRow(${idx})"><i class="bi bi-trash"></i></button>
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
        item.className = 'cp-preview-item';
        item.id = 'preview-' + idx;
        item.innerHTML = `
            <img src="${e.target.result}" alt="preview">
            <button type="button" class="cp-preview-remove" onclick="removeRow(${idx})"><i class="bi bi-x"></i></button>
            <div class="cp-preview-angle" id="alabel-${idx}">Depan</div>
        `;
        previewGrid.appendChild(item);
    };
    reader.readAsDataURL(file);
}

function updateAngleLabel(idx, val) {
    const lbl = document.getElementById('alabel-' + idx);
    if (lbl) lbl.textContent = angleLabels[val] || val;
}

function removeRow(idx) {
    const row = document.getElementById('row-' + idx);
    const prev = document.getElementById('preview-' + idx);
    if (row) row.remove();
    if (prev) prev.remove();
}

// Dropzone events
dropzone.addEventListener('click', () => quickUpload.click());
dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('drag-over');
    Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/')).forEach(f => addRow(f));
});
quickUpload.addEventListener('change', function () {
    Array.from(this.files).forEach(f => addRow(f));
    this.value = '';
});
document.getElementById('addRowBtn').addEventListener('click', () => addRow());

// Default 1 baris kosong
addRow();
</script>
@endpush
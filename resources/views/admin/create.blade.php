@extends('layouts.admin')
@section('title','Tambah Produk')
@section('content')
<div class="d-flex justify-content-between mb-4">
    <h4 class="fw-bold">➕ Tambah Produk Baru</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-bold mb-3">Informasi Produk</h6>
                <div class="mb-3">
                    <label class="form-label">Nama Produk *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi *</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Kondisi / Minus <span class="text-muted small">(jujur, apa adanya)</span></label>
                    <textarea name="condition_notes" class="form-control" rows="3" placeholder="Contoh: Ada noda kecil di bagian bawah belakang, warna sedikit pudar di kerah..."></textarea>
                </div>
            </div>

            <!-- Upload Foto -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-bold mb-3">📸 Foto Produk</h6>
                <p class="text-muted small">Upload foto dari berbagai sudut. Pilih kategori sudut untuk setiap foto.</p>
                <div id="imageUploads">
                    <div class="image-upload-row mb-3 border rounded p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label small">Foto</label>
                                <input type="file" name="images[]" class="form-control form-control-sm" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Sudut Foto</label>
                                <select name="image_angles[]" class="form-select form-select-sm">
                                    <option value="front">Depan</option>
                                    <option value="back">Belakang</option>
                                    <option value="brand_detail">Detail Merek</option>
                                    <option value="minus_detail">Detail Minus</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" onclick="this.closest('.image-upload-row').remove()" class="btn btn-outline-danger btn-sm w-100">✕</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="addImageBtn" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-plus me-1"></i>Tambah Foto Lagi
                </button>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-bold mb-3">Detail & Harga</h6>
                <div class="mb-3">
                    <label class="form-label small">Harga (Rp) *</label>
                    <input type="number" name="price" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Kategori *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Pilih...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">No Brand</option>
                        @foreach($brands as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Ukuran</label>
                    <select name="size" class="form-select">
                        <option value="">-</option>
                        @foreach(['XS','S','M','L','XL','XXL','XXXL','27','28','29','30','31','32','33','34','35','36','37','38','39','40'] as $sz)
                        <option value="{{ $sz }}">{{ $sz }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Kondisi *</label>
                    <select name="condition" class="form-select" required>
                        <option value="like_new">Like New</option>
                        <option value="9/10">9/10</option>
                        <option value="8/10">8/10</option>
                        <option value="7/10">7/10</option>
                        <option value="6/10">6/10</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="unisex">Unisex</option>
                        <option value="pria">Pria</option>
                        <option value="wanita">Wanita</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small">Panjang (cm)</label>
                        <input type="number" name="length_cm" class="form-control" step="0.1">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Lebar (cm)</label>
                        <input type="number" name="width_cm" class="form-control" step="0.1">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Berat (gram) *</label>
                    <input type="number" name="weight_gram" class="form-control" value="500" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">
                <i class="bi bi-upload me-2"></i>Simpan Produk
            </button>
        </div>
    </div>
</form>
@endsection
@push('scripts')
<script>
document.getElementById('addImageBtn').addEventListener('click', function() {
    const template = document.querySelector('.image-upload-row').cloneNode(true);
    template.querySelector('input[type=file]').value = '';
    document.getElementById('imageUploads').appendChild(template);
});
</script>
@endpush
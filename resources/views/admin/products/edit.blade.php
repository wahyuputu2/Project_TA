@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('content')

<div class="d-flex justify-content-between mb-4">
    <h4 class="fw-bold">✏️ Edit Produk</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-bold mb-3">Informasi Produk</h6>
                <div class="mb-3">
                    <label class="form-label">Nama Produk *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi *</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Kondisi / Minus</label>
                    <textarea name="condition_notes" class="form-control" rows="3">{{ old('condition_notes', $product->condition_notes) }}</textarea>
                </div>
            </div>

            {{-- Foto yang sudah ada --}}
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-bold mb-3">📸 Foto Saat Ini</h6>
                <div class="row g-2 mb-3">
                    @foreach($product->images as $image)
                    <div class="col-3 position-relative" id="img-{{ $image->id }}">
                        <img src="{{ Storage::url($image->image_path) }}"
                             class="img-fluid rounded border"
                             style="aspect-ratio:1;object-fit:cover">
                        <div class="text-center mt-1">
                            <small class="text-muted">{{ $image->angleLabel() }}</small>
                        </div>
                        <button type="button"
                                onclick="deleteImage({{ $image->id }})"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 py-0 px-1">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    @endforeach
                </div>

                <h6 class="fw-semibold mb-2">Tambah Foto Baru</h6>
                <div id="imageUploads">
                    <div class="image-upload-row mb-3 border rounded p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <input type="file" name="images[]" class="form-control form-control-sm" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <select name="image_angles[]" class="form-select form-select-sm">
                                    <option value="front">Depan</option>
                                    <option value="back">Belakang</option>
                                    <option value="brand_detail">Detail Merek</option>
                                    <option value="minus_detail">Detail Minus</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" onclick="this.closest('.image-upload-row').remove()"
                                        class="btn btn-outline-danger btn-sm w-100">✕</button>
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
                    <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Kategori *</label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($product->category_id == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">No Brand</option>
                        @foreach($brands as $b)
                        <option value="{{ $b->id }}" @selected($product->brand_id == $b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Ukuran</label>
                    <select name="size" class="form-select">
                        <option value="">-</option>
                        @foreach(['XS','S','M','L','XL','XXL','XXXL','27','28','29','30','31','32','33','34','35','36','37','38','39','40'] as $sz)
                        <option value="{{ $sz }}" @selected($product->size == $sz)>{{ $sz }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Kondisi *</label>
                    <select name="condition" class="form-select" required>
                        @foreach(['like_new'=>'Like New','9/10'=>'9/10','8/10'=>'8/10','7/10'=>'7/10','6/10'=>'6/10'] as $val => $label)
                        <option value="{{ $val }}" @selected($product->condition == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Gender</label>
                    <select name="gender" class="form-select">
                        @foreach(['unisex'=>'Unisex','pria'=>'Pria','wanita'=>'Wanita'] as $val => $label)
                        <option value="{{ $val }}" @selected($product->gender == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small">Panjang (cm)</label>
                        <input type="number" name="length_cm" class="form-control" step="0.1" value="{{ old('length_cm', $product->length_cm) }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Lebar (cm)</label>
                        <input type="number" name="width_cm" class="form-control" step="0.1" value="{{ old('width_cm', $product->width_cm) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Berat (gram) *</label>
                    <input type="number" name="weight_gram" class="form-control" value="{{ old('weight_gram', $product->weight_gram) }}" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">
                <i class="bi bi-save me-2"></i>Simpan Perubahan
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

function deleteImage(id) {
    if (!confirm('Hapus foto ini?')) return;
    fetch(`/admin/products/images/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json'
        }
    }).then(r => r.json()).then(data => {
        if (data.success) document.getElementById(`img-${id}`).remove();
    });
}
</script>
@endpush
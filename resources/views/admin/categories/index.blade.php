@extends('layouts.admin')
@section('title', 'Kategori')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:#f0ece4;margin-bottom:4px">Kategori</h4>
        <p style="font-size:.82rem;color:rgba(240,236,228,.35);margin:0">Kelola kategori produk toko</p>
    </div>
    <button type="button" class="adm-btn-primary" onclick="document.getElementById('addModal').style.display='flex'">
        <i class="bi bi-plus-lg"></i> Tambah Kategori
    </button>
</div>

<div class="adm-card">
    <table class="adm-table">
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah Produk</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
            <tr>
                <td>
                    <div style="font-weight:600;color:#f0ece4">{{ $cat->name }}</div>
                    <div style="font-size:.72rem;color:rgba(240,236,228,.3)">/{{ $cat->slug }}</div>
                </td>
                <td style="font-size:.82rem">{{ $cat->description ?? '-' }}</td>
                <td>
                    <span class="adm-badge {{ $cat->products_count > 0 ? 'adm-badge-gold' : 'adm-badge-muted' }}">
                        {{ $cat->products_count }} produk
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <button class="adm-btn-secondary" style="padding:6px 12px;font-size:.78rem"
                                onclick="openEdit({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->description }}')">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                              onsubmit="return confirm('Hapus kategori {{ $cat->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:6px 12px;color:#f87171;cursor:pointer;font-size:.78rem">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;padding:40px;color:rgba(240,236,228,.25)">
                    <i class="bi bi-grid" style="font-size:2rem;display:block;margin-bottom:10px"></i>
                    Belum ada kategori
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL TAMBAH --}}
<div id="addModal" class="cat-modal-backdrop" style="display:none">
    <div class="cat-modal">
        <div class="cat-modal-head">
            <h6 class="cat-modal-title">Tambah Kategori</h6>
            <button onclick="document.getElementById('addModal').style.display='none'" class="cat-modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="cat-modal-body">
                <div class="mb-3">
                    <label class="cat-label">Nama Kategori *</label>
                    <input type="text" name="name" class="adm-input" placeholder="Contoh: Jaket" required>
                </div>
                <div class="mb-3">
                    <label class="cat-label">Deskripsi</label>
                    <textarea name="description" class="adm-input" rows="2"
                              placeholder="Deskripsi singkat kategori..." style="resize:none"></textarea>
                </div>
                <div>
                    <label class="cat-label">Gambar (Opsional)</label>
                    <input type="file" name="image" class="adm-input" accept="image/*" style="padding:8px 12px">
                </div>
            </div>
            <div class="cat-modal-foot">
                <button type="button" class="adm-btn-secondary"
                        onclick="document.getElementById('addModal').style.display='none'">Batal</button>
                <button type="submit" class="adm-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="editModal" class="cat-modal-backdrop" style="display:none">
    <div class="cat-modal">
        <div class="cat-modal-head">
            <h6 class="cat-modal-title">Edit Kategori</h6>
            <button onclick="document.getElementById('editModal').style.display='none'" class="cat-modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="cat-modal-body">
                <div class="mb-3">
                    <label class="cat-label">Nama Kategori *</label>
                    <input type="text" name="name" id="editName" class="adm-input" required>
                </div>
                <div class="mb-3">
                    <label class="cat-label">Deskripsi</label>
                    <textarea name="description" id="editDesc" class="adm-input" rows="2" style="resize:none"></textarea>
                </div>
                <div>
                    <label class="cat-label">Gambar Baru (Opsional)</label>
                    <input type="file" name="image" class="adm-input" accept="image/*" style="padding:8px 12px">
                </div>
            </div>
            <div class="cat-modal-foot">
                <button type="button" class="adm-btn-secondary"
                        onclick="document.getElementById('editModal').style.display='none'">Batal</button>
                <button type="submit" class="adm-btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('styles')
<style>
.cat-label{display:block;font-size:.7rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(240,236,228,.4);margin-bottom:7px}
.cat-modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center}
.cat-modal{background:#1a1a1b;border:1px solid rgba(255,255,255,.1);border-radius:14px;width:100%;max-width:440px;box-shadow:0 24px 60px rgba(0,0,0,.5)}
.cat-modal-head{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid rgba(255,255,255,.07)}
.cat-modal-title{font-size:.95rem;font-weight:600;color:#f0ece4;margin:0}
.cat-modal-close{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:7px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;color:rgba(240,236,228,.5);cursor:pointer;font-size:.8rem}
.cat-modal-close:hover{background:rgba(255,255,255,.1);color:#f0ece4}
.cat-modal-body{padding:20px 22px;display:flex;flex-direction:column;gap:4px}
.cat-modal-foot{display:flex;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:1px solid rgba(255,255,255,.07)}
</style>
@endpush
@push('scripts')
<script>
function openEdit(id, name, desc) {
    document.getElementById('editForm').action = `/admin/categories/${id}`;
    document.getElementById('editName').value  = name;
    document.getElementById('editDesc').value  = desc || '';
    document.getElementById('editModal').style.display = 'flex';
}
['addModal','editModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('addModal').style.display  = 'none';
        document.getElementById('editModal').style.display = 'none';
    }
});
</script>
@endpush
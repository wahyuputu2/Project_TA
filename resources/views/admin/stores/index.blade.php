@extends('layouts.admin')
@section('title', 'Manajemen Toko')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:#f0ece4;margin-bottom:4px">
            Manajemen Toko
        </h4>
        <p style="font-size:.82rem;color:rgba(240,236,228,.35);margin:0">
            Verifikasi & kelola semua toko seller
        </p>
    </div>
</div>

{{-- STATS --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon"><i class="bi bi-shop"></i></div>
            <div class="stat-card-label">Total Toko</div>
            <div class="stat-card-value">{{ $totalStores }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="color:#facc15"><i class="bi bi-clock"></i></div>
            <div class="stat-card-label">Menunggu Verifikasi</div>
            <div class="stat-card-value" style="color:#facc15">{{ $pendingStores }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="color:#4ade80"><i class="bi bi-check-circle"></i></div>
            <div class="stat-card-label">Toko Aktif</div>
            <div class="stat-card-value" style="color:#4ade80">{{ $activeStores }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="color:#f87171"><i class="bi bi-slash-circle"></i></div>
            <div class="stat-card-label">Ditangguhkan</div>
            <div class="stat-card-value" style="color:#f87171">{{ $suspendedStores }}</div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="adm-card mb-4">
    <div class="adm-card-body" style="padding:14px 20px">
        <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div>
                <label style="display:block;font-size:.68rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:6px">Cari Toko</label>
                <input type="text" name="search" class="adm-input"
                       placeholder="Nama toko..." value="{{ request('search') }}"
                       style="padding:8px 14px;width:200px">
            </div>
            <div>
                <label style="display:block;font-size:.68rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:6px">Status</label>
                <select name="status" class="adm-select" style="width:180px;padding:8px 12px">
                    <option value="">Semua Status</option>
                    <option value="pending"   @selected(request('status')==='pending')>⏳ Menunggu Verifikasi</option>
                    <option value="active"    @selected(request('status')==='active')>✅ Aktif</option>
                    <option value="suspended" @selected(request('status')==='suspended')>🚫 Ditangguhkan</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="adm-btn-primary" style="padding:9px 16px">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="{{ route('admin.stores.index') }}" class="adm-btn-secondary" style="padding:9px 14px">Reset</a>
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
                    <th>Toko</th>
                    <th>Pemilik</th>
                    <th>Lokasi</th>
                    <th>Produk</th>
                    <th>Rekening</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stores as $store)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px">
                            @if($store->logo)
                                <img src="{{ Storage::url($store->logo) }}"
                                     style="width:42px;height:42px;border-radius:50%;object-fit:cover;border:1px solid rgba(255,255,255,.1)">
                            @else
                                <div style="width:42px;height:42px;border-radius:50%;background:rgba(201,169,110,.15);border:1px solid rgba(201,169,110,.25);display:flex;align-items:center;justify-content:center;color:#c9a96e;font-weight:700;font-size:.95rem;flex-shrink:0">
                                    {{ strtoupper(substr($store->name,0,1)) }}
                                </div>
                            @endif
                            <div>
                                <div style="font-weight:600;color:#f0ece4;font-size:.88rem">{{ $store->name }}</div>
                                <div style="font-size:.7rem;color:rgba(240,236,228,.3)">Komisi: {{ $store->commission_rate }}%</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.85rem;color:#f0ece4">{{ $store->user->name }}</div>
                        <div style="font-size:.72rem;color:rgba(240,236,228,.35)">{{ $store->user->email }}</div>
                        <div style="font-size:.68rem;color:rgba(240,236,228,.25)">Role: {{ $store->user->role }}</div>
                    </td>
                    <td>
                        <div style="font-size:.82rem;color:rgba(240,236,228,.6)">{{ $store->city ?? '—' }}</div>
                        <div style="font-size:.72rem;color:rgba(240,236,228,.3)">{{ $store->province ?? '' }}</div>
                    </td>
                    <td>
                        <span style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:.72rem;font-weight:600;background:rgba(201,169,110,.1);color:#c9a96e;border:1px solid rgba(201,169,110,.2)">
                            {{ $store->products_count }} produk
                        </span>
                    </td>
                    <td>
                        <div style="font-size:.78rem;color:#f0ece4">{{ $store->bank_name ?? '—' }}</div>
                        <div style="font-size:.72rem;color:rgba(240,236,228,.4);letter-spacing:.5px">{{ $store->bank_account_number ?? '' }}</div>
                        <div style="font-size:.68rem;color:rgba(240,236,228,.25)">{{ $store->bank_account_name ?? '' }}</div>
                    </td>
                    <td>
                        @php
                            $sc = match($store->status) {
                                'active'    => ['#4ade80','rgba(34,197,94,.1)','rgba(34,197,94,.2)'],
                                'pending'   => ['#facc15','rgba(234,179,8,.1)','rgba(234,179,8,.2)'],
                                'suspended' => ['#f87171','rgba(239,68,68,.1)','rgba(239,68,68,.2)'],
                                default     => ['rgba(240,236,228,.4)','rgba(255,255,255,.05)','rgba(255,255,255,.1)'],
                            };
                        @endphp
                        <span style="display:inline-block;padding:4px 10px;border-radius:20px;font-size:.7rem;font-weight:600;color:{{ $sc[0] }};background:{{ $sc[1] }};border:1px solid {{ $sc[2] }}">
                            {{ $store->statusLabel() }}
                        </span>
                    </td>
                    <td style="font-size:.75rem;color:rgba(240,236,228,.35)">
                        {{ $store->created_at->format('d M Y') }}<br>
                        <span style="font-size:.68rem">{{ $store->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-2">
                            @if($store->status === 'pending')
                            <form action="{{ route('admin.stores.verify', $store) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit"
                                        style="width:100%;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);border-radius:7px;padding:6px 12px;font-size:.75rem;font-weight:600;color:#4ade80;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;gap:5px;justify-content:center">
                                    <i class="bi bi-check2-circle"></i> Verifikasi
                                </button>
                            </form>
                            @endif

                            @if($store->status === 'active')
                            <form action="{{ route('admin.stores.suspend', $store) }}" method="POST"
                                  onsubmit="return confirm('Tangguhkan toko {{ $store->name }}?')">
                                @csrf @method('PUT')
                                <button type="submit"
                                        style="width:100%;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:7px;padding:6px 12px;font-size:.75rem;font-weight:600;color:#f87171;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;gap:5px;justify-content:center">
                                    <i class="bi bi-slash-circle"></i> Tangguhkan
                                </button>
                            </form>
                            @endif

                            @if($store->status === 'suspended')
                            <form action="{{ route('admin.stores.verify', $store) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit"
                                        style="width:100%;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);border-radius:7px;padding:6px 12px;font-size:.75rem;font-weight:600;color:#4ade80;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;gap:5px;justify-content:center">
                                    <i class="bi bi-arrow-counterclockwise"></i> Aktifkan
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('stores.show', $store->slug) }}" target="_blank"
                               style="display:flex;align-items:center;justify-content:center;gap:5px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:7px;padding:6px 12px;font-size:.75rem;color:rgba(240,236,228,.5);text-decoration:none">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:56px;color:rgba(240,236,228,.25)">
                        <i class="bi bi-shop" style="font-size:2.5rem;display:block;margin-bottom:12px;opacity:.3"></i>
                        <div style="font-size:.95rem;font-weight:500">Belum ada toko terdaftar</div>
                        <div style="font-size:.8rem;margin-top:4px;color:rgba(240,236,228,.18)">Toko akan muncul di sini saat seller mendaftar</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid rgba(255,255,255,.06)">
        {{ $stores->withQueryString()->links() }}
    </div>
</div>

@endsection
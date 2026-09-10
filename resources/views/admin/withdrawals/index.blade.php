@extends('layouts.admin')
@section('title', 'Pencairan Dana')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:#f0ece4;margin-bottom:4px">
            Pencairan Dana
        </h4>
        <p style="font-size:.82rem;color:rgba(240,236,228,.35);margin:0">
            Proses permintaan withdraw dari seller
        </p>
    </div>
</div>

{{-- STATS --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon" style="color:#facc15"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-card-label">Menunggu Diproses</div>
            <div class="stat-card-value" style="color:#facc15">{{ \App\Models\Withdrawal::where('status','pending')->count() }}</div>
            <div class="stat-card-sub">
                Total: Rp {{ number_format(\App\Models\Withdrawal::where('status','pending')->sum('amount'),0,',','.') }}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon" style="color:#4ade80"><i class="bi bi-check-circle"></i></div>
            <div class="stat-card-label">Sudah Diproses</div>
            <div class="stat-card-value" style="color:#4ade80">{{ \App\Models\Withdrawal::where('status','processed')->count() }}</div>
            <div class="stat-card-sub">
                Total: Rp {{ number_format(\App\Models\Withdrawal::where('status','processed')->sum('amount'),0,',','.') }}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon" style="color:#f87171"><i class="bi bi-x-circle"></i></div>
            <div class="stat-card-label">Ditolak</div>
            <div class="stat-card-value" style="color:#f87171">{{ \App\Models\Withdrawal::where('status','rejected')->count() }}</div>
        </div>
    </div>
</div>

{{-- TABLE --}}
<div class="adm-card">
    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Seller / Toko</th>
                    <th>Jumlah</th>
                    <th>Rekening Tujuan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $withdrawal)
                <tr>
                    <td>
                        <div style="font-weight:600;color:#f0ece4;font-size:.88rem">{{ $withdrawal->store->name }}</div>
                        <div style="font-size:.72rem;color:rgba(240,236,228,.35)">{{ $withdrawal->store->user->name }}</div>
                    </td>
                    <td>
                        <div style="font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:700;color:#c9a96e">
                            Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.85rem;color:#f0ece4">{{ $withdrawal->bank_name }}</div>
                        <div style="font-size:.82rem;color:rgba(240,236,228,.6);font-weight:600;letter-spacing:.5px">
                            {{ $withdrawal->bank_account_number }}
                        </div>
                        <div style="font-size:.72rem;color:rgba(240,236,228,.35)">a.n. {{ $withdrawal->bank_account_name }}</div>
                    </td>
                    <td style="font-size:.78rem;color:rgba(240,236,228,.4)">
                        {{ $withdrawal->created_at->format('d M Y') }}<br>
                        {{ $withdrawal->created_at->format('H:i') }}
                    </td>
                    <td>
                        @php
                            $wc = match($withdrawal->status) {
                                'pending'   => ['#facc15','rgba(234,179,8,.1)','rgba(234,179,8,.2)'],
                                'processed' => ['#4ade80','rgba(34,197,94,.1)','rgba(34,197,94,.2)'],
                                'rejected'  => ['#f87171','rgba(239,68,68,.1)','rgba(239,68,68,.2)'],
                                default     => ['rgba(240,236,228,.4)','rgba(255,255,255,.05)','rgba(255,255,255,.1)'],
                            };
                        @endphp
                        <span style="display:inline-block;padding:4px 10px;border-radius:20px;font-size:.7rem;font-weight:600;color:{{ $wc[0] }};background:{{ $wc[1] }};border:1px solid {{ $wc[2] }}">
                            {{ $withdrawal->statusLabel() }}
                        </span>
                        @if($withdrawal->processed_at)
                        <div style="font-size:.68rem;color:rgba(240,236,228,.3);margin-top:3px">
                            {{ $withdrawal->processed_at->format('d M Y H:i') }}
                        </div>
                        @endif
                    </td>
                    <td>
                        @if($withdrawal->status === 'pending')
                        <button type="button" class="adm-btn-sm-success mb-1"
                                onclick="openProcessModal({{ $withdrawal->id }}, '{{ $withdrawal->store->name }}', '{{ number_format($withdrawal->amount,0,',','.') }}', '{{ $withdrawal->bank_name }}', '{{ $withdrawal->bank_account_number }}', '{{ $withdrawal->bank_account_name }}')">
                            <i class="bi bi-check2-circle me-1"></i>Proses
                        </button>
                        <br>
                        <button type="button" class="adm-btn-sm-danger"
                                onclick="openRejectModal({{ $withdrawal->id }}, '{{ $withdrawal->store->name }}')">
                            <i class="bi bi-x-circle me-1"></i>Tolak
                        </button>
                        @else
                        <span style="font-size:.75rem;color:rgba(240,236,228,.3)">
                            @if($withdrawal->notes) {{ $withdrawal->notes }} @else — @endif
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:rgba(240,236,228,.25)">
                        <i class="bi bi-cash-stack" style="font-size:2rem;display:block;margin-bottom:10px"></i>
                        Belum ada permintaan pencairan dana
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid rgba(255,255,255,.06)">
        {{ $withdrawals->links() }}
    </div>
</div>

{{-- MODAL PROSES --}}
<div id="processModal" class="wd-modal-backdrop" style="display:none">
    <div class="wd-modal">
        <div class="wd-modal-head">
            <h6 class="wd-modal-title">✅ Konfirmasi Transfer</h6>
            <button onclick="document.getElementById('processModal').style.display='none'" class="wd-modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="wd-modal-body">
            <div class="wd-info-box">
                <div class="wd-info-row">
                    <span>Toko</span>
                    <strong id="pm-store"></strong>
                </div>
                <div class="wd-info-row">
                    <span>Jumlah Transfer</span>
                    <strong id="pm-amount" style="color:#c9a96e;font-size:1.1rem"></strong>
                </div>
                <div class="wd-info-row">
                    <span>Bank</span>
                    <strong id="pm-bank"></strong>
                </div>
                <div class="wd-info-row">
                    <span>No. Rekening</span>
                    <strong id="pm-account" style="letter-spacing:.5px"></strong>
                </div>
                <div class="wd-info-row">
                    <span>Atas Nama</span>
                    <strong id="pm-name"></strong>
                </div>
            </div>
            <div style="background:rgba(234,179,8,.08);border:1px solid rgba(234,179,8,.2);border-radius:9px;padding:12px 16px;font-size:.8rem;color:#facc15;margin-top:14px">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Pastikan kamu sudah melakukan transfer ke rekening di atas sebelum mengklik "Sudah Transfer".
            </div>
            <form id="processForm" method="POST" style="margin-top:14px">
                @csrf @method('PUT')
                <input type="hidden" name="action" value="processed">
                <div style="margin-bottom:12px">
                    <label style="display:block;font-size:.72rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(240,236,228,.4);margin-bottom:7px">Catatan (Opsional)</label>
                    <input type="text" name="notes" class="adm-input" placeholder="Contoh: Transfer BCA tanggal 23 Mei 2025" style="padding:9px 14px">
                </div>
                <button type="submit" class="wd-btn-confirm">
                    <i class="bi bi-check2-circle me-2"></i>Sudah Transfer — Tandai Selesai
                </button>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TOLAK --}}
<div id="rejectModal" class="wd-modal-backdrop" style="display:none">
    <div class="wd-modal">
        <div class="wd-modal-head">
            <h6 class="wd-modal-title">❌ Tolak Pencairan</h6>
            <button onclick="document.getElementById('rejectModal').style.display='none'" class="wd-modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="wd-modal-body">
            <p style="font-size:.85rem;color:rgba(240,236,228,.6);margin-bottom:16px">
                Pencairan dari toko <strong id="rm-store" style="color:#f0ece4"></strong> akan ditolak dan
                <strong style="color:#f0ece4">saldo dikembalikan</strong> ke seller.
            </p>
            <form id="rejectForm" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="action" value="rejected">
                <div style="margin-bottom:14px">
                    <label style="display:block;font-size:.72rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:rgba(240,236,228,.4);margin-bottom:7px">Alasan Penolakan *</label>
                    <textarea name="notes" class="adm-input" rows="3" required
                              placeholder="Contoh: Nomor rekening tidak valid / Data tidak sesuai"
                              style="padding:10px 14px;resize:none"></textarea>
                </div>
                <button type="submit" class="wd-btn-reject">
                    <i class="bi bi-x-circle me-2"></i>Tolak & Kembalikan Saldo
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
@push('styles')
<style>
.adm-btn-sm-success{display:inline-flex;align-items:center;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);border-radius:7px;padding:6px 12px;font-size:.75rem;font-weight:600;color:#4ade80;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s}
.adm-btn-sm-success:hover{background:rgba(34,197,94,.2)}
.adm-btn-sm-danger{display:inline-flex;align-items:center;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:7px;padding:6px 12px;font-size:.75rem;font-weight:600;color:#f87171;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s}
.adm-btn-sm-danger:hover{background:rgba(239,68,68,.2)}

/* Modal */
.wd-modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);z-index:9999;display:flex;align-items:center;justify-content:center}
.wd-modal{background:#1a1a1b;border:1px solid rgba(255,255,255,.1);border-radius:16px;width:100%;max-width:460px;box-shadow:0 24px 60px rgba(0,0,0,.5)}
.wd-modal-head{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid rgba(255,255,255,.07)}
.wd-modal-title{font-size:.95rem;font-weight:600;color:#f0ece4;margin:0}
.wd-modal-close{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:7px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;color:rgba(240,236,228,.5);cursor:pointer;font-size:.8rem}
.wd-modal-close:hover{background:rgba(255,255,255,.12);color:#f0ece4}
.wd-modal-body{padding:20px 22px}
.wd-info-box{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:10px;padding:14px 16px}
.wd-info-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.84rem}
.wd-info-row:last-child{border-bottom:none}
.wd-info-row span{color:rgba(240,236,228,.4)}
.wd-info-row strong{color:#f0ece4}
.wd-btn-confirm{width:100%;background:#c9a96e;border:none;border-radius:9px;padding:13px;font-size:.88rem;font-weight:700;color:#0e0e0f;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;transition:background .2s}
.wd-btn-confirm:hover{background:#b8913d}
.wd-btn-reject{width:100%;background:#ef4444;border:none;border-radius:9px;padding:13px;font-size:.88rem;font-weight:700;color:white;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;transition:background .2s}
.wd-btn-reject:hover{background:#dc2626}
</style>
@endpush
@push('scripts')
<script>
function openProcessModal(id, store, amount, bank, account, name) {
    document.getElementById('pm-store').textContent   = store;
    document.getElementById('pm-amount').textContent  = 'Rp ' + amount;
    document.getElementById('pm-bank').textContent    = bank;
    document.getElementById('pm-account').textContent = account;
    document.getElementById('pm-name').textContent    = name;
    document.getElementById('processForm').action = `/admin/withdrawals/${id}/process`;
    document.getElementById('processModal').style.display = 'flex';
}

function openRejectModal(id, store) {
    document.getElementById('rm-store').textContent = store;
    document.getElementById('rejectForm').action = `/admin/withdrawals/${id}/process`;
    document.getElementById('rejectModal').style.display = 'flex';
}

['processModal','rejectModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('processModal').style.display = 'none';
        document.getElementById('rejectModal').style.display  = 'none';
    }
});
</script>
@endpush
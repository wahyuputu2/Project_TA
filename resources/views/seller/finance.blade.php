@extends('layouts.seller')
@section('title', 'Keuangan')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-weight:700;margin-bottom:4px">Keuangan Toko</h4>
        <p style="font-size:.82rem;color:var(--muted);margin:0">Kelola saldo & pencairan dana toko {{ $store->name }}</p>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="sf-stat" style="border-color:#bbf7d0">
            <div class="sf-stat-icon" style="background:#f0fdf4;color:#16a34a">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <div class="sf-stat-label">Saldo Tersedia</div>
                <div class="sf-stat-value" style="color:#16a34a">
                    Rp {{ number_format($store->balance, 0, ',', '.') }}
                </div>
                <div class="sf-stat-sub">Siap dicairkan</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sf-stat" style="border-color:#fde68a">
            <div class="sf-stat-icon" style="background:#fffbeb;color:#d97706">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="sf-stat-label">Pending Cair</div>
                <div class="sf-stat-value" style="color:#d97706">
                    Rp {{ number_format($pendingBalance, 0, ',', '.') }}
                </div>
                <div class="sf-stat-sub">Setelah order selesai</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sf-stat" style="border-color:#bfdbfe">
            <div class="sf-stat-icon" style="background:#eff6ff;color:#2563eb">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
                <div class="sf-stat-label">Total Pendapatan</div>
                <div class="sf-stat-value" style="color:#2563eb">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
                <div class="sf-stat-sub">Komisi {{ $store->commission_rate }}% platform</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- KIRI: Tarik Dana + Riwayat Withdraw --}}
    <div class="col-lg-5">

        {{-- FORM TARIK DANA --}}
        <div class="sf-card mb-4">
            <div class="sf-card-head">💸 Tarik Dana</div>
            <div class="sf-card-body">
                @if($store->balance >= 50000)
                <form action="{{ route('seller.withdraw') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="sf-label">Jumlah Penarikan</label>
                        <div style="position:relative">
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:.78rem;color:var(--muted)">Rp</span>
                            <input type="number" name="amount" class="sf-input"
                                   placeholder="Minimum Rp 50.000"
                                   min="50000" max="{{ $store->balance }}"
                                   style="padding-left:36px" required>
                        </div>
                        <p style="font-size:.72rem;color:var(--muted);margin-top:5px">
                            Saldo tersedia: <strong style="color:#16a34a">Rp {{ number_format($store->balance,0,',','.') }}</strong>
                        </p>
                    </div>

                    {{-- Info rekening --}}
                    <div style="background:#f8f8f6;border:1px solid var(--border);border-radius:8px;padding:14px;margin-bottom:16px">
                        <div style="font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">Dana akan ditransfer ke</div>
                        <div style="display:flex;flex-direction:column;gap:6px">
                            <div style="display:flex;justify-content:space-between;font-size:.82rem">
                                <span style="color:var(--muted)">Bank</span>
                                <strong>{{ $store->bank_name ?? '—' }}</strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:.82rem">
                                <span style="color:var(--muted)">No. Rekening</span>
                                <strong style="letter-spacing:.5px">{{ $store->bank_account_number ?? '—' }}</strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:.82rem">
                                <span style="color:var(--muted)">Atas Nama</span>
                                <strong>{{ $store->bank_account_name ?? '—' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:.78rem;color:#92400e">
                        <i class="bi bi-info-circle me-1"></i>
                        Pencairan diproses admin dalam <strong>1–3 hari kerja</strong>.
                    </div>

                    <button type="submit" class="sf-submit-btn"
                            onclick="return confirm('Tarik dana Rp ' + document.querySelector('input[name=amount]').value.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '?')">
                        <i class="bi bi-bank me-2"></i>Ajukan Penarikan
                    </button>
                </form>
                @else
                <div style="text-align:center;padding:24px;color:var(--muted)">
                    <i class="bi bi-wallet2" style="font-size:2rem;display:block;margin-bottom:10px;opacity:.3"></i>
                    <div style="font-size:.88rem;font-weight:500;margin-bottom:5px">Saldo Tidak Cukup</div>
                    <div style="font-size:.78rem">Minimum penarikan Rp 50.000</div>
                </div>
                @endif
            </div>
        </div>

        {{-- RIWAYAT WITHDRAW --}}
        <div class="sf-card">
            <div class="sf-card-head">📋 Riwayat Pencairan</div>
            <div style="padding:0">
                @forelse($withdrawals as $wd)
                <div style="padding:14px 18px;border-bottom:1px solid var(--border)">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start">
                        <div>
                            <div style="font-size:.88rem;font-weight:700;color:var(--primary)">
                                Rp {{ number_format($wd->amount, 0, ',', '.') }}
                            </div>
                            <div style="font-size:.72rem;color:var(--muted);margin-top:2px">
                                {{ $wd->bank_name }} · {{ $wd->bank_account_number }}
                            </div>
                            <div style="font-size:.7rem;color:var(--faint);margin-top:2px">
                                {{ $wd->created_at->format('d M Y, H:i') }}
                            </div>
                            @if($wd->notes)
                            <div style="font-size:.72rem;color:var(--muted);margin-top:4px;font-style:italic">
                                {{ $wd->notes }}
                            </div>
                            @endif
                        </div>
                        @php
                            $wc = match($wd->status) {
                                'pending'   => ['#d97706','#fffbeb','#fde68a'],
                                'processed' => ['#16a34a','#f0fdf4','#bbf7d0'],
                                'rejected'  => ['#dc2626','#fef2f2','#fecaca'],
                                default     => ['#6b7280','#f9fafb','#e5e7eb'],
                            };
                        @endphp
                        <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:600;color:{{ $wc[0] }};background:{{ $wc[1] }};border:1px solid {{ $wc[2] }};white-space:nowrap">
                            {{ $wd->statusLabel() }}
                        </span>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:32px;color:var(--faint);font-size:.85rem">
                    Belum ada riwayat pencairan
                </div>
                @endforelse
            </div>
            @if($withdrawals->hasPages())
            <div style="padding:12px 18px;border-top:1px solid var(--border)">
                {{ $withdrawals->links() }}
            </div>
            @endif
        </div>

    </div>

    {{-- KANAN: Riwayat Komisi --}}
    <div class="col-lg-7">
        <div class="sf-card">
            <div class="sf-card-head">
                📊 Riwayat Komisi
                <span style="margin-left:auto;font-size:.72rem;font-weight:400;color:var(--muted)">
                    Komisi platform {{ $store->commission_rate }}% per transaksi
                </span>
            </div>
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead style="background:#f8f8f6">
                        <tr>
                            <th style="padding:10px 16px;font-size:.68rem;color:var(--muted);font-weight:600;text-align:left">No. Order</th>
                            <th style="padding:10px 16px;font-size:.68rem;color:var(--muted);font-weight:600;text-align:right">Total Order</th>
                            <th style="padding:10px 16px;font-size:.68rem;color:var(--muted);font-weight:600;text-align:right">Komisi</th>
                            <th style="padding:10px 16px;font-size:.68rem;color:var(--muted);font-weight:600;text-align:right">Kamu Dapat</th>
                            <th style="padding:10px 16px;font-size:.68rem;color:var(--muted);font-weight:600;text-align:left">Status</th>
                            <th style="padding:10px 16px;font-size:.68rem;color:var(--muted);font-weight:600;text-align:left">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $commission)
                        <tr style="border-top:1px solid var(--border)">
                            <td style="padding:12px 16px;font-size:.82rem;font-weight:600;color:var(--primary)">
                                {{ $commission->order?->order_number ?? '—' }}
                            </td>
                            <td style="padding:12px 16px;font-size:.82rem;color:var(--muted);text-align:right">
                                Rp {{ number_format($commission->order_amount, 0, ',', '.') }}
                            </td>
                            <td style="padding:12px 16px;font-size:.82rem;color:#dc2626;text-align:right">
                                -Rp {{ number_format($commission->commission_amount, 0, ',', '.') }}
                                <div style="font-size:.65rem;color:var(--faint)">{{ $commission->commission_rate }}%</div>
                            </td>
                            <td style="padding:12px 16px;font-size:.88rem;font-weight:700;color:#16a34a;text-align:right">
                                Rp {{ number_format($commission->seller_amount, 0, ',', '.') }}
                            </td>
                            <td style="padding:12px 16px">
                                @php
                                    $cc = match($commission->status) {
                                        'pending'  => ['#d97706','#fffbeb','#fde68a'],
                                        'settled'  => ['#16a34a','#f0fdf4','#bbf7d0'],
                                        'refunded' => ['#dc2626','#fef2f2','#fecaca'],
                                        default    => ['#6b7280','#f9fafb','#e5e7eb'],
                                    };
                                @endphp
                                <span style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:.68rem;font-weight:600;color:{{ $cc[0] }};background:{{ $cc[1] }};border:1px solid {{ $cc[2] }}">
                                    {{ ucfirst($commission->status) }}
                                </span>
                            </td>
                            <td style="padding:12px 16px;font-size:.75rem;color:var(--muted)">
                                {{ $commission->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px;color:var(--faint);font-size:.85rem">
                                <i class="bi bi-graph-up" style="font-size:2rem;display:block;margin-bottom:10px;opacity:.3"></i>
                                Belum ada transaksi komisi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($commissions->hasPages())
            <div style="padding:14px 18px;border-top:1px solid var(--border)">
                {{ $commissions->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

@endsection
@push('styles')
<style>
.sf-stat { background:white;border:1.5px solid var(--border);border-radius:10px;padding:18px 20px;display:flex;align-items:center;gap:16px }
.sf-stat-icon { width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0 }
.sf-stat-label { font-size:.72rem;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);margin-bottom:5px }
.sf-stat-value { font-size:1.3rem;font-weight:700;color:var(--primary);line-height:1 }
.sf-stat-sub { font-size:.7rem;color:var(--faint);margin-top:4px }
.sf-card { background:white;border:1.5px solid var(--border);border-radius:10px;overflow:hidden }
.sf-card-head { padding:13px 18px;background:#f8f8f6;border-bottom:1px solid var(--border);font-size:.88rem;font-weight:600;color:var(--primary);display:flex;align-items:center }
.sf-card-body { padding:18px }
.sf-label { display:block;font-size:.72rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:7px }
.sf-input { width:100%;background:#fafafa;border:1.5px solid var(--border);border-radius:8px;padding:10px 14px;font-size:.88rem;color:var(--primary);font-family:'Jost',sans-serif;outline:none;transition:border-color .2s }
.sf-input:focus { border-color:var(--primary) }
.sf-submit-btn { width:100%;background:var(--primary);border:none;border-radius:9px;padding:12px;font-size:.88rem;font-weight:700;color:white;cursor:pointer;font-family:'Jost',sans-serif;transition:background .2s;display:flex;align-items:center;justify-content:center }
.sf-submit-btn:hover { background:var(--accent) }
</style>
@endpush
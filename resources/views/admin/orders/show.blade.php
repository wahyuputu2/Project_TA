@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:#f0ece4;margin-bottom:4px">
            {{ $order->order_number }}
        </h4>
        <p style="font-size:.82rem;color:rgba(240,236,228,.35);margin:0">
            Dibuat {{ $order->created_at->format('d M Y, H:i') }} ·
            @php [$clr] = match($order->status) {
                'pending_payment' => ['#facc15'], 'paid' => ['#60a5fa'],
                'processing' => ['#c9a96e'],      'shipped' => ['#a78bfa'],
                'delivered'  => ['#4ade80'],       'cancelled' => ['#f87171'],
                default => ['rgba(240,236,228,.4)'],
            }; @endphp
            <span style="color:{{ $clr }};font-weight:600">{{ $order->statusLabel() }}</span>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.index') }}" class="adm-btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <button type="button" class="adm-btn-danger"
                onclick="document.getElementById('deleteModal').style.display='flex'">
            <i class="bi bi-trash3 me-1"></i>Hapus
        </button>
    </div>
</div>

{{-- Modal hapus --}}
<div id="deleteModal" class="ord-modal-backdrop" style="display:none">
    <div class="ord-modal">
        <div class="ord-modal-icon">🗑️</div>
        <h5 class="ord-modal-title">Hapus Pesanan?</h5>
        <p class="ord-modal-sub">
            Pesanan <strong style="color:#f0ece4">{{ $order->order_number }}</strong> akan dihapus permanen
            beserta semua log pembayaran dan ulasan terkait.
        </p>
        <div class="ord-modal-actions">
            <button type="button" class="ord-modal-no"
                    onclick="document.getElementById('deleteModal').style.display='none'">
                Batal
            </button>
            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="flex:1">
                @csrf @method('DELETE')
                <button type="submit" class="ord-modal-yes">
                    <i class="bi bi-trash3 me-2"></i>Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- ══ KIRI ══ --}}
    <div class="col-lg-8">

        {{-- TIMELINE STATUS --}}
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <i class="bi bi-activity"></i> Timeline Pesanan
            </div>
            <div class="adm-card-body">
                <div class="ord-timeline">
                    @php
                        $steps = [
                            ['pending_payment', 'Menunggu Pembayaran', 'bi-clock', $order->created_at],
                            ['paid',       'Pembayaran Diterima', 'bi-check-circle', $order->paid_at],
                            ['processing', 'Sedang Dikemas',      'bi-box-seam',     null],
                            ['shipped',    'Dikirim',             'bi-truck',        $order->shipped_at],
                            ['delivered',  'Selesai',             'bi-bag-check',    $order->delivered_at],
                        ];
                        $statusOrder = ['pending_payment','paid','processing','shipped','delivered','cancelled','refunded'];
                        $currentIdx  = array_search($order->status, $statusOrder);
                    @endphp
                    @foreach($steps as $i => [$stepStatus, $stepLabel, $stepIcon, $stepTime])
                    @php
                        $stepIdx = array_search($stepStatus, $statusOrder);
                        $isDone  = $currentIdx >= $stepIdx && !in_array($order->status, ['cancelled','refunded']);
                        $isCurr  = $order->status === $stepStatus;
                    @endphp
                    <div class="ord-timeline-step {{ $isDone ? 'done' : '' }} {{ $isCurr ? 'current' : '' }}">
                        <div class="ord-tl-dot">
                            <i class="bi {{ $stepIcon }}"></i>
                        </div>
                        <div class="ord-tl-content">
                            <div class="ord-tl-label">{{ $stepLabel }}</div>
                            @if($stepTime)
                            <div class="ord-tl-time">{{ $stepTime->format('d M Y, H:i') }}</div>
                            @elseif($isCurr)
                            <div class="ord-tl-time" style="color:#c9a96e">Sedang dalam proses...</div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    @if($order->status === 'cancelled')
                    <div class="ord-timeline-step done" style="--tl-color:#f87171">
                        <div class="ord-tl-dot"><i class="bi bi-x-circle"></i></div>
                        <div class="ord-tl-content">
                            <div class="ord-tl-label">Pesanan Dibatalkan</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- UPDATE STATUS --}}
        @if(!in_array($order->status, ['delivered','cancelled','refunded']))
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <i class="bi bi-pencil-square"></i> Update Status Pesanan
            </div>
            <div class="adm-card-body">
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" id="statusForm">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="ord-label">Ubah Status</label>
                            <select name="status" class="adm-select" id="statusSelect" onchange="toggleResi(this.value)">
                                @php
                                    $nextStatuses = match($order->status) {
                                        'pending_payment' => ['paid'=>'✅ Pembayaran Diterima','cancelled'=>'❌ Batalkan'],
                                        'paid'       => ['processing'=>'📦 Mulai Kemas','cancelled'=>'❌ Batalkan'],
                                        'processing' => ['shipped'=>'🚚 Tandai Dikirim','cancelled'=>'❌ Batalkan'],
                                        'shipped'    => ['delivered'=>'✅ Tandai Selesai'],
                                        default      => [],
                                    };
                                @endphp
                                <option value="">— Pilih Status Baru —</option>
                                @foreach($nextStatuses as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5" id="resiField" style="display:none">
                            <label class="ord-label">No. Resi <span style="color:#f87171">*</span></label>
                            <input type="text" name="tracking_number" class="adm-input"
                                   placeholder="Contoh: JNE123456789ID"
                                   value="{{ $order->tracking_number }}" style="padding:9px 14px">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="adm-btn-primary w-100" style="padding:10px;justify-content:center">
                                <i class="bi bi-check2"></i> Simpan
                            </button>
                        </div>
                    </div>

                    {{-- Konfirmasi batalkan --}}
                    <div id="cancelWarning" style="display:none;margin-top:12px;padding:12px 16px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:9px;font-size:.82rem;color:#f87171">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Membatalkan pesanan akan melepas lock produk dan mengembalikan stok ke <strong>Tersedia</strong>.
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- ITEM PESANAN --}}
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <i class="bi bi-bag"></i> Item Pesanan
            </div>
            <div class="adm-card-body" style="padding:0">
                @foreach($order->items as $item)
                <div style="display:flex;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid rgba(255,255,255,.05)">
                    @if($item->product && $item->product->mainImage())
                    <img src="{{ Storage::url($item->product->mainImage()->image_path) }}"
                         style="width:60px;height:80px;object-fit:cover;border-radius:8px;border:1px solid rgba(255,255,255,.08)">
                    @else
                    <div style="width:60px;height:80px;background:rgba(255,255,255,.04);border-radius:8px;display:flex;align-items:center;justify-content:center;color:rgba(240,236,228,.2)">
                        <i class="bi bi-image"></i>
                    </div>
                    @endif
                    <div style="flex:1">
                        <div style="font-size:.9rem;font-weight:600;color:#f0ece4">{{ $item->product_name }}</div>
                        <div style="font-size:.75rem;color:rgba(240,236,228,.4);margin-top:3px">
                            Size {{ $item->product_size ?? '-' }} · {{ $item->product_condition }}
                        </div>
                        @if($item->product)
                        <div style="margin-top:6px">
                            <span style="font-size:.7rem;padding:2px 8px;border-radius:20px;background:rgba(255,255,255,.05);color:rgba(240,236,228,.35)">
                                Status produk: {{ $item->product->statusLabel() }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:700;color:#f0ece4">
                        Rp {{ number_format($item->price, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach

                {{-- Summary --}}
                <div style="padding:16px 20px">
                    <div style="display:flex;justify-content:space-between;font-size:.85rem;color:rgba(240,236,228,.45);margin-bottom:6px">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.85rem;color:rgba(240,236,228,.45);margin-bottom:12px">
                        <span>Ongkos Kirim ({{ strtoupper($order->courier) }} {{ $order->courier_service }})</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:700;color:#f0ece4;padding-top:12px;border-top:1px solid rgba(255,255,255,.07)">
                        <span>Total</span>
                        <span style="font-family:'Cormorant Garamond',serif;font-size:1.2rem;color:#c9a96e">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAYMENT LOGS --}}
        @if($order->paymentLogs->count() > 0)
        <div class="adm-card">
            <div class="adm-card-header">
                <i class="bi bi-receipt"></i> Log Pembayaran
            </div>
            <div class="adm-card-body" style="padding:0">
                @foreach($order->paymentLogs as $log)
                <div style="padding:14px 20px;border-bottom:1px solid rgba(255,255,255,.05);font-size:.82rem">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <span style="color:#f0ece4;font-weight:500">{{ $log->transaction_id ?? '-' }}</span>
                            <span style="color:rgba(240,236,228,.35);margin-left:10px">{{ $log->payment_type }}</span>
                        </div>
                        <span style="color:{{ $log->transaction_status === 'settlement' ? '#4ade80' : '#facc15' }};font-weight:600">
                            {{ $log->transaction_status }}
                        </span>
                    </div>
                    <div style="color:rgba(240,236,228,.3);margin-top:3px">{{ $log->created_at->format('d M Y H:i') }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- ══ KANAN ══ --}}
    <div class="col-lg-4">

        {{-- INFO PEMBELI --}}
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <i class="bi bi-person-circle"></i> Info Pembeli
            </div>
            <div class="adm-card-body">
                <div class="ord-info-row">
                    <span class="ord-info-lbl">Nama</span>
                    <span class="ord-info-val">{{ $order->user->name }}</span>
                </div>
                <div class="ord-info-row">
                    <span class="ord-info-lbl">Email</span>
                    <span class="ord-info-val">{{ $order->user->email }}</span>
                </div>
                <div class="ord-info-row">
                    <span class="ord-info-lbl">No. HP</span>
                    <span class="ord-info-val">{{ $order->shipping_phone }}</span>
                </div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->shipping_phone) }}?text=Halo+{{ urlencode($order->user->name) }},+terkait+pesanan+{{ $order->order_number }}"
                   target="_blank" class="adm-btn-primary w-100 mt-3" style="justify-content:center;background:#25d366;padding:10px">
                    <i class="bi bi-whatsapp me-2"></i>Chat via WhatsApp
                </a>
            </div>
        </div>

        {{-- ALAMAT PENGIRIMAN --}}
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <i class="bi bi-geo-alt"></i> Alamat Pengiriman
            </div>
            <div class="adm-card-body">
                <div class="ord-info-row">
                    <span class="ord-info-lbl">Penerima</span>
                    <span class="ord-info-val">{{ $order->shipping_name }}</span>
                </div>
                <div class="ord-info-row">
                    <span class="ord-info-lbl">Alamat</span>
                    <span class="ord-info-val">{{ $order->shipping_address }}</span>
                </div>
                <div class="ord-info-row">
                    <span class="ord-info-lbl">Kota</span>
                    <span class="ord-info-val">{{ $order->shipping_city }}, {{ $order->shipping_province }}</span>
                </div>
                <div class="ord-info-row">
                    <span class="ord-info-lbl">Kode Pos</span>
                    <span class="ord-info-val">{{ $order->shipping_postal_code }}</span>
                </div>
                <div class="ord-info-row">
                    <span class="ord-info-lbl">Kurir</span>
                    <span class="ord-info-val">{{ strtoupper($order->courier) }} {{ $order->courier_service }}</span>
                </div>
            </div>
        </div>

        {{-- RESI --}}
        <div class="adm-card mb-4">
            <div class="adm-card-header">
                <i class="bi bi-truck"></i> Nomor Resi
            </div>
            <div class="adm-card-body">
                @if($order->tracking_number)
                <div style="background:rgba(201,169,110,.08);border:1px solid rgba(201,169,110,.2);border-radius:9px;padding:14px;text-align:center;margin-bottom:12px">
                    <div style="font-size:.7rem;color:rgba(240,236,228,.35);text-transform:uppercase;letter-spacing:1px;margin-bottom:5px">Nomor Resi</div>
                    <div style="font-size:1rem;font-weight:700;color:#c9a96e;letter-spacing:1px">{{ $order->tracking_number }}</div>
                    <div style="font-size:.72rem;color:rgba(240,236,228,.3);margin-top:4px">{{ strtoupper($order->courier) }}</div>
                </div>
                @endif

                {{-- Form update resi saja --}}
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="{{ $order->status }}">
                    <label class="ord-label">Update No. Resi</label>
                    <input type="text" name="tracking_number" class="adm-input mb-2"
                           placeholder="Masukkan nomor resi..." value="{{ $order->tracking_number }}"
                           style="padding:9px 14px">
                    <button type="submit" class="adm-btn-primary w-100" style="justify-content:center;padding:10px">
                        <i class="bi bi-save me-1"></i>Simpan Resi
                    </button>
                </form>
            </div>
        </div>

        {{-- CATATAN --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <i class="bi bi-chat-text"></i> Catatan Pesanan
            </div>
            <div class="adm-card-body">
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="{{ $order->status }}">
                    <textarea name="notes" class="adm-input" rows="3"
                              placeholder="Tambahkan catatan internal..."
                              style="resize:vertical;padding:10px 14px;font-size:.85rem">{{ $order->notes }}</textarea>
                    <button type="submit" class="adm-btn-secondary w-100 mt-2" style="justify-content:center;padding:9px">
                        <i class="bi bi-save me-1"></i>Simpan Catatan
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.adm-btn-danger{display:inline-flex;align-items:center;gap:7px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:9px;padding:9px 16px;font-size:.85rem;color:#f87171;cursor:pointer;font-family:'Outfit',sans-serif;text-decoration:none;transition:all .2s}
.adm-btn-danger:hover{background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.35);color:#f87171}
.ord-modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center}
.ord-modal{background:#1a1a1b;border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:32px 28px;max-width:400px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.5)}
.ord-modal-icon{font-size:2.5rem;margin-bottom:16px}
.ord-modal-title{font-size:1.1rem;font-weight:700;color:#f0ece4;margin-bottom:10px}
.ord-modal-sub{font-size:.85rem;color:rgba(240,236,228,.45);line-height:1.6;margin-bottom:24px}
.ord-modal-actions{display:flex;gap:10px}
.ord-modal-no{flex:1;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:11px;font-size:.88rem;color:rgba(240,236,228,.6);cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s}
.ord-modal-no:hover{background:rgba(255,255,255,.1);color:#f0ece4}
.ord-modal-yes{width:100%;background:#ef4444;border:none;border-radius:9px;padding:11px;font-size:.88rem;font-weight:600;color:white;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;transition:background .2s}
.ord-modal-yes:hover{background:#dc2626}
.ord-label { display:block;font-size:.68rem;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:7px }

/* Timeline */
.ord-timeline { display:flex;flex-direction:column;gap:0 }
.ord-timeline-step { display:flex;gap:14px;align-items:flex-start;position:relative;padding-bottom:20px }
.ord-timeline-step:last-child { padding-bottom:0 }
.ord-timeline-step::before { content:'';position:absolute;left:15px;top:32px;bottom:0;width:1px;background:rgba(255,255,255,.07) }
.ord-timeline-step:last-child::before { display:none }
.ord-tl-dot { width:32px;height:32px;border-radius:50%;border:1.5px solid rgba(255,255,255,.1);background:rgba(255,255,255,.03);display:flex;align-items:center;justify-content:center;font-size:.85rem;color:rgba(240,236,228,.2);flex-shrink:0;z-index:1 }
.ord-timeline-step.done .ord-tl-dot { border-color:var(--tl-color,#4ade80);color:var(--tl-color,#4ade80);background:rgba(74,222,128,.08) }
.ord-timeline-step.current .ord-tl-dot { border-color:#c9a96e;color:#c9a96e;background:rgba(201,169,110,.1) }
.ord-tl-content { padding-top:5px }
.ord-tl-label { font-size:.88rem;color:rgba(240,236,228,.4);font-weight:400 }
.ord-timeline-step.done .ord-tl-label { color:#f0ece4;font-weight:500 }
.ord-timeline-step.current .ord-tl-label { color:#c9a96e;font-weight:600 }
.ord-tl-time { font-size:.72rem;color:rgba(240,236,228,.3);margin-top:3px }

/* Info rows */
.ord-info-row { display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04) }
.ord-info-row:last-child { border-bottom:none }
.ord-info-lbl { font-size:.72rem;color:rgba(240,236,228,.3);text-transform:uppercase;letter-spacing:.8px;flex-shrink:0;padding-top:2px }
.ord-info-val { font-size:.85rem;color:#f0ece4;text-align:right }
</style>
@endpush

@push('scripts')
<script>
function toggleResi(val) {
    document.getElementById('resiField').style.display    = val === 'shipped' ? 'block' : 'none';
    document.getElementById('cancelWarning').style.display = val === 'cancelled' ? 'block' : 'none';
}
</script>
@endpush
@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')

<div class="container py-5" style="max-width:900px">

    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <a href="{{ route('orders.index') }}" class="ord-back-link">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Pesanan
            </a>
            <h3 class="ord-page-title mt-2">{{ $order->order_number }}</h3>
            <p class="ord-page-sub">{{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        @php
            $ss = match($order->status) {
                'pending_payment' => ['#facc15','rgba(234,179,8,.1)','rgba(234,179,8,.25)'],
                'paid'       => ['#60a5fa','rgba(59,130,246,.1)','rgba(59,130,246,.25)'],
                'processing' => ['#c9a96e','rgba(201,169,110,.1)','rgba(201,169,110,.25)'],
                'shipped'    => ['#a78bfa','rgba(139,92,246,.1)','rgba(139,92,246,.25)'],
                'delivered'  => ['#4ade80','rgba(34,197,94,.1)','rgba(34,197,94,.25)'],
                'cancelled'  => ['#f87171','rgba(239,68,68,.1)','rgba(239,68,68,.25)'],
                default      => ['rgba(240,236,228,.4)','rgba(255,255,255,.05)','rgba(255,255,255,.1)'],
            };
        @endphp
        <span class="ord-status-badge" style="color:{{ $ss[0] }};background:{{ $ss[1] }};border-color:{{ $ss[2] }};font-size:.88rem;padding:8px 18px">
            {{ $order->statusLabel() }}
        </span>
    </div>

    {{-- BATALKAN --}}
    @if($order->status === 'pending_payment')
    <div class="ord-cancel-wrap mb-4">
        <div class="ord-cancel-info">
            <i class="bi bi-info-circle me-2"></i>
            Pesanan hanya bisa dibatalkan selama masih menunggu pembayaran.
        </div>
        <button type="button" class="ord-cancel-btn"
                onclick="document.getElementById('cancelModal').style.display='flex'">
            <i class="bi bi-x-circle me-2"></i>Batalkan Pesanan
        </button>
    </div>
    <div id="cancelModal" class="ord-modal-backdrop" style="display:none">
        <div class="ord-modal">
            <div class="ord-modal-icon">⚠️</div>
            <h5 class="ord-modal-title">Batalkan Pesanan?</h5>
            <p class="ord-modal-sub">
                Pesanan <strong style="color:#f0ece4">{{ $order->order_number }}</strong> akan dibatalkan
                dan produk dikembalikan ke katalog.
            </p>
            <div class="ord-modal-actions">
                <button type="button" class="ord-modal-no"
                        onclick="document.getElementById('cancelModal').style.display='none'">
                    Tidak, Kembali
                </button>
                <form action="{{ route('orders.cancel', $order) }}" method="POST" style="flex:1">
                    @csrf
                    <button type="submit" class="ord-modal-yes">
                        <i class="bi bi-x-circle me-2"></i>Ya, Batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- PAY ALERT --}}
    @if($order->status === 'pending_payment' && $order->payment_url)
    <div class="ord-pay-alert mb-4">
        <div style="display:flex;align-items:center;gap:12px">
            <i class="bi bi-exclamation-circle-fill" style="color:#facc15;font-size:1.3rem"></i>
            <div>
                <div style="font-size:.92rem;font-weight:600;color:#f0ece4">Selesaikan Pembayaran</div>
                <div style="font-size:.78rem;color:rgba(240,236,228,.4);margin-top:2px">Segera bayar sebelum pesanan otomatis dibatalkan</div>
            </div>
        </div>
        <a href="{{ $order->payment_url }}" target="_blank" class="ord-pay-btn">
            <i class="bi bi-credit-card me-2"></i>Bayar Sekarang
        </a>
    </div>
    @endif

    {{-- TRACKING --}}
    @if($order->tracking_number && in_array($order->status, ['shipped','delivered']))
    <div class="ord-tracking-card mb-4">
        <div class="ord-tracking-top">
            <div>
                <div class="ord-tracking-label">Nomor Resi</div>
                <div class="ord-tracking-number">{{ $order->tracking_number }}</div>
                <div class="ord-tracking-courier">{{ strtoupper($order->courier) }} · {{ $order->courier_service }}</div>
            </div>
            <button onclick="copyResi('{{ $order->tracking_number }}')" class="ord-copy-btn">
                <i class="bi bi-copy me-1"></i>Salin
            </button>
        </div>
        @if($order->shipped_at)
        <div class="ord-tracking-time">
            <i class="bi bi-clock me-1"></i>Dikirim pada {{ $order->shipped_at->format('d M Y, H:i') }}
        </div>
        @endif
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">

            {{-- TIMELINE --}}
            <div class="ord-section-card mb-4">
                <div class="ord-section-head">
                    <i class="bi bi-activity me-2" style="color:#c9a96e"></i>Status Pesanan
                </div>
                <div class="ord-section-body">
                    <div class="ord-timeline">
                        @php
                            $tlSteps = [
                                ['pending_payment','Pesanan Dibuat','bi-receipt',$order->created_at],
                                ['paid','Pembayaran Dikonfirmasi','bi-check-circle-fill',$order->paid_at],
                                ['processing','Sedang Dikemas','bi-box-seam',null],
                                ['shipped','Paket Dikirim','bi-truck',$order->shipped_at],
                                ['delivered','Pesanan Diterima','bi-bag-check-fill',$order->delivered_at],
                            ];
                            $statusOrder = ['pending_payment','paid','processing','shipped','delivered'];
                            $currentIdx  = array_search($order->status, $statusOrder);
                        @endphp
                        @foreach($tlSteps as $si => [$ss2, $sl, $sicon, $stime])
                        @php
                            $sidx   = array_search($ss2, $statusOrder);
                            $isDone = $currentIdx !== false && $currentIdx >= $sidx && !in_array($order->status,['cancelled','refunded']);
                            $isCurr = $order->status === $ss2;
                        @endphp
                        <div class="ord-tl-row {{ $isDone?'done':'' }} {{ $isCurr?'current':'' }}">
                            <div class="ord-tl-icon-wrap">
                                <div class="ord-tl-icon"><i class="bi {{ $sicon }}"></i></div>
                                @if(!$loop->last)<div class="ord-tl-line"></div>@endif
                            </div>
                            <div class="ord-tl-body">
                                <div class="ord-tl-title">{{ $sl }}</div>
                                @if($stime)
                                    <div class="ord-tl-date">{{ $stime->format('d M Y, H:i') }}</div>
                                @elseif($isCurr)
                                    <div class="ord-tl-date" style="color:#c9a96e">Dalam proses...</div>
                                @else
                                    <div class="ord-tl-date">Menunggu</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        @if($order->status === 'cancelled')
                        <div class="ord-tl-row done" style="--tl-clr:#f87171;--tl-bg:rgba(239,68,68,.08)">
                            <div class="ord-tl-icon-wrap">
                                <div class="ord-tl-icon"><i class="bi bi-x-circle-fill"></i></div>
                            </div>
                            <div class="ord-tl-body">
                                <div class="ord-tl-title">Pesanan Dibatalkan</div>
                                <div class="ord-tl-date">{{ $order->updated_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="ord-section-card">
                <div class="ord-section-head">
                    <i class="bi bi-bag me-2" style="color:#c9a96e"></i>Item yang Dipesan
                </div>
                <div style="padding:0">
                    @foreach($order->items as $item)
                    <div class="ord-detail-item">
                        <div class="ord-detail-img">
                            @if($item->product && $item->product->mainImage())
                                <img src="{{ Storage::url($item->product->mainImage()->image_path) }}" alt="{{ $item->product_name }}">
                            @else
                                <div class="ord-item-no-img"><i class="bi bi-image"></i></div>
                            @endif
                        </div>
                        <div class="ord-detail-info">
                            <div class="ord-detail-name">{{ $item->product_name }}</div>
                            <div class="ord-detail-meta">Size {{ $item->product_size ?? '-' }} · {{ $item->product_condition }}</div>
                            @if($item->product)
                            <a href="{{ route('catalog.show', $item->product->slug) }}" class="ord-detail-link">
                                Lihat Produk <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            @endif
                        </div>
                        <div class="ord-detail-price">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="ord-cost-summary">
                    <div class="ord-cost-row">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="ord-cost-row">
                        <span>Ongkos Kirim ({{ strtoupper($order->courier ?? '') }} {{ $order->courier_service }})</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="ord-cost-total">
                        <span>Total Pembayaran</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-5">

            {{-- ALAMAT --}}
            <div class="ord-section-card mb-4">
                <div class="ord-section-head">
                    <i class="bi bi-geo-alt me-2" style="color:#c9a96e"></i>Alamat Pengiriman
                </div>
                <div class="ord-section-body">
                    <div class="ord-addr-name">{{ $order->shipping_name }}</div>
                    <div class="ord-addr-phone">{{ $order->shipping_phone }}</div>
                    <div class="ord-addr-full">
                        {{ $order->shipping_address }},<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_province }}<br>
                        {{ $order->shipping_postal_code }}
                    </div>
                </div>
            </div>

            {{-- PEMBAYARAN --}}
            <div class="ord-section-card mb-4">
                <div class="ord-section-head">
                    <i class="bi bi-credit-card me-2" style="color:#c9a96e"></i>Info Pembayaran
                </div>
                <div class="ord-section-body">
                    <div class="ord-info-row2">
                        <span>Metode</span>
                        <span>{{ $order->payment_method ?? 'Belum dibayar' }}</span>
                    </div>
                    <div class="ord-info-row2">
                        <span>Status</span>
                        <span style="color:{{ $order->paid_at ? '#4ade80' : '#facc15' }}">
                            {{ $order->paid_at ? 'Lunas · '.$order->paid_at->format('d M Y') : 'Belum dibayar' }}
                        </span>
                    </div>
                    @if($order->payment_channel)
                    <div class="ord-info-row2">
                        <span>Channel</span>
                        <span>{{ strtoupper($order->payment_channel) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- BANTUAN --}}
            <div class="ord-help-card">
                <div class="ord-help-title">Butuh Bantuan?</div>
                <div class="ord-help-sub">Hubungi admin jika ada pertanyaan tentang pesanan ini</div>
                <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Halo+admin,+saya+ingin+tanya+tentang+pesanan+{{ $order->order_number }}"
                   target="_blank" class="ord-help-wa">
                    <i class="bi bi-whatsapp me-2"></i>Chat Admin via WhatsApp
                </a>
            </div>
        </div>
    </div>

    {{-- ULASAN --}}
    @if($order->status === 'delivered')
    <div id="review" class="mt-4">
        <div class="ord-section-card">
            <div class="ord-section-head">
                <i class="bi bi-star me-2" style="color:#c9a96e"></i>Ulasan Produk
            </div>
            <div class="ord-section-body">
                @foreach($order->items as $item)
                @php $existingReview = $item->product?->reviews()->where('order_id',$order->id)->where('user_id',Auth::id())->first(); @endphp
                <div class="{{ !$loop->last ? 'mb-4 pb-4' : '' }}" style="{{ !$loop->last ? 'border-bottom:1px solid rgba(255,255,255,.06)' : '' }}">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if($item->product && $item->product->mainImage())
                        <img src="{{ Storage::url($item->product->mainImage()->image_path) }}"
                             style="width:48px;height:60px;object-fit:cover;border-radius:7px;border:1px solid rgba(255,255,255,.08)">
                        @endif
                        <div>
                            <div style="font-size:.88rem;font-weight:600;color:#f0ece4">{{ $item->product_name }}</div>
                            <div style="font-size:.75rem;color:rgba(240,236,228,.35)">Size {{ $item->product_size }}</div>
                        </div>
                    </div>
                    @if($existingReview)
                    <div style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:9px;padding:14px">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            @for($s=1;$s<=5;$s++)
                            <i class="bi bi-star{{ $s<=$existingReview->rating?'-fill':'' }}" style="color:#c9a96e;font-size:.9rem"></i>
                            @endfor
                            <span style="font-size:.75rem;color:rgba(240,236,228,.35);margin-left:4px">{{ $existingReview->created_at->format('d M Y') }}</span>
                        </div>
                        <p style="font-size:.85rem;color:rgba(240,236,228,.6);margin:0">{{ $existingReview->comment }}</p>
                        @if($existingReview->photos)
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            @foreach($existingReview->photos as $photo)
                            <img src="{{ Storage::url($photo) }}" style="width:64px;height:64px;object-fit:cover;border-radius:7px;border:1px solid rgba(255,255,255,.08)">
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @else
                    <form action="{{ route('orders.review', $order) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                        <div class="mb-3">
                            <div style="font-size:.7rem;color:rgba(240,236,228,.35);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px">Rating</div>
                            <div class="star-picker" id="stars_{{ $item->id }}">
                                @for($s=1;$s<=5;$s++)
                                <label class="star-label" for="star_{{ $item->id }}_{{ $s }}">
                                    <input type="radio" name="rating" id="star_{{ $item->id }}_{{ $s }}" value="{{ $s }}" @checked($s===5) style="display:none">
                                    <i class="bi bi-star-fill" style="font-size:1.4rem;color:rgba(240,236,228,.15);cursor:pointer;transition:color .15s"></i>
                                </label>
                                @endfor
                            </div>
                        </div>
                        <div class="mb-3">
                            <div style="font-size:.7rem;color:rgba(240,236,228,.35);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px">Komentar</div>
                            <textarea name="comment" rows="3" required class="ord-review-textarea"
                                      placeholder="Bagaimana kondisi produk? Sesuai deskripsi?"></textarea>
                        </div>
                        <div class="mb-3">
                            <div style="font-size:.7rem;color:rgba(240,236,228,.35);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px">Foto (Opsional)</div>
                            <input type="file" name="photos[]" multiple accept="image/*" class="ord-review-file">
                        </div>
                        <button type="submit" class="ord-review-submit">
                            <i class="bi bi-star me-2"></i>Kirim Ulasan
                        </button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
@push('styles')
<style>
.ord-page-title{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:#f0ece4;margin:0 0 4px}
.ord-page-sub{font-size:.82rem;color:rgba(240,236,228,.35);margin:0}
.ord-back-link{font-size:.82rem;color:rgba(240,236,228,.4);text-decoration:none;display:inline-flex;align-items:center;transition:color .2s}
.ord-back-link:hover{color:#c9a96e}
.ord-status-badge{display:inline-flex;align-items:center;padding:5px 12px;border-radius:20px;font-size:.78rem;font-weight:600;border:1px solid}
.ord-cancel-wrap{display:flex;justify-content:space-between;align-items:center;padding:12px 18px;background:rgba(239,68,68,.05);border:1px solid rgba(239,68,68,.15);border-radius:12px;flex-wrap:wrap;gap:10px}
.ord-cancel-info{font-size:.8rem;color:rgba(240,236,228,.3);display:flex;align-items:center}
.ord-cancel-btn{display:inline-flex;align-items:center;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);border-radius:8px;padding:8px 18px;font-size:.82rem;font-weight:600;color:#f87171;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s;white-space:nowrap}
.ord-cancel-btn:hover{background:rgba(239,68,68,.18);border-color:rgba(239,68,68,.4)}
.ord-pay-alert{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:rgba(234,179,8,.06);border:1px solid rgba(234,179,8,.2);border-radius:12px;flex-wrap:wrap;gap:12px}
.ord-pay-btn{display:inline-flex;align-items:center;background:#facc15;border:none;border-radius:8px;padding:10px 20px;font-size:.85rem;font-weight:700;color:#0e0e0f;text-decoration:none;transition:background .2s;white-space:nowrap}
.ord-pay-btn:hover{background:#eab308;color:#0e0e0f}
.ord-tracking-card{background:rgba(201,169,110,.06);border:1px solid rgba(201,169,110,.2);border-radius:12px;padding:18px 20px}
.ord-tracking-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.ord-tracking-label{font-size:.68rem;letter-spacing:1.5px;text-transform:uppercase;color:rgba(240,236,228,.35);margin-bottom:5px}
.ord-tracking-number{font-size:1.3rem;font-weight:700;color:#c9a96e;letter-spacing:1px;font-family:'Cormorant Garamond',serif}
.ord-tracking-courier{font-size:.75rem;color:rgba(240,236,228,.35);margin-top:3px}
.ord-tracking-time{font-size:.75rem;color:rgba(240,236,228,.3);margin-top:10px;padding-top:10px;border-top:1px solid rgba(201,169,110,.15)}
.ord-copy-btn{background:rgba(201,169,110,.1);border:1px solid rgba(201,169,110,.2);border-radius:7px;padding:7px 14px;font-size:.78rem;color:#c9a96e;cursor:pointer;font-family:'Outfit',sans-serif;display:flex;align-items:center;transition:all .15s}
.ord-copy-btn:hover{background:rgba(201,169,110,.2)}
.ord-section-card{background:#151515;border:1px solid rgba(255,255,255,.07);border-radius:14px;overflow:hidden}
.ord-section-head{padding:14px 20px;background:rgba(255,255,255,.02);border-bottom:1px solid rgba(255,255,255,.05);font-size:.88rem;font-weight:600;color:#f0ece4;display:flex;align-items:center}
.ord-section-body{padding:18px 20px}
.ord-timeline{display:flex;flex-direction:column}
.ord-tl-row{display:flex;gap:14px}
.ord-tl-icon-wrap{display:flex;flex-direction:column;align-items:center;flex-shrink:0}
.ord-tl-icon{width:34px;height:34px;border-radius:50%;border:1.5px solid rgba(255,255,255,.1);background:rgba(255,255,255,.03);display:flex;align-items:center;justify-content:center;font-size:.9rem;color:rgba(240,236,228,.2)}
.ord-tl-row.done .ord-tl-icon{border-color:var(--tl-clr,#c9a96e);color:var(--tl-clr,#c9a96e);background:var(--tl-bg,rgba(201,169,110,.08))}
.ord-tl-row.current .ord-tl-icon{border-color:#c9a96e;color:#c9a96e;background:rgba(201,169,110,.1)}
.ord-tl-line{width:1.5px;flex:1;background:rgba(255,255,255,.06);margin:4px 0;min-height:20px}
.ord-tl-row.done .ord-tl-line{background:rgba(201,169,110,.2)}
.ord-tl-body{padding:6px 0 20px}
.ord-tl-title{font-size:.88rem;color:rgba(240,236,228,.35)}
.ord-tl-row.done .ord-tl-title,.ord-tl-row.current .ord-tl-title{color:#f0ece4;font-weight:500}
.ord-tl-date{font-size:.72rem;color:rgba(240,236,228,.25);margin-top:3px}
.ord-detail-item{display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid rgba(255,255,255,.04)}
.ord-detail-item:last-child{border-bottom:none}
.ord-detail-img{width:56px;height:74px;border-radius:8px;overflow:hidden;flex-shrink:0;border:1px solid rgba(255,255,255,.07)}
.ord-detail-img img{width:100%;height:100%;object-fit:cover}
.ord-item-no-img{width:100%;height:100%;background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;color:rgba(240,236,228,.2)}
.ord-detail-info{flex:1}
.ord-detail-name{font-size:.9rem;font-weight:500;color:#f0ece4}
.ord-detail-meta{font-size:.75rem;color:rgba(240,236,228,.35);margin-top:3px}
.ord-detail-link{font-size:.75rem;color:#c9a96e;text-decoration:none;margin-top:5px;display:inline-flex;align-items:center}
.ord-detail-price{font-size:.92rem;font-weight:700;color:#f0ece4;flex-shrink:0}
.ord-cost-summary{padding:14px 20px;border-top:1px solid rgba(255,255,255,.05);background:rgba(255,255,255,.02)}
.ord-cost-row{display:flex;justify-content:space-between;font-size:.82rem;color:rgba(240,236,228,.4);margin-bottom:6px}
.ord-cost-total{display:flex;justify-content:space-between;font-size:.95rem;font-weight:700;color:#f0ece4;padding-top:10px;border-top:1px solid rgba(255,255,255,.07);margin-top:6px}
.ord-cost-total span:last-child{color:#c9a96e;font-family:'Cormorant Garamond',serif;font-size:1.1rem}
.ord-addr-name{font-size:.92rem;font-weight:600;color:#f0ece4;margin-bottom:3px}
.ord-addr-phone{font-size:.82rem;color:rgba(240,236,228,.4);margin-bottom:10px}
.ord-addr-full{font-size:.85rem;color:rgba(240,236,228,.55);line-height:1.7}
.ord-info-row2{display:flex;justify-content:space-between;font-size:.85rem;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.ord-info-row2:last-child{border-bottom:none}
.ord-info-row2 span:first-child{color:rgba(240,236,228,.35)}
.ord-info-row2 span:last-child{color:#f0ece4;font-weight:500}
.ord-help-card{background:rgba(201,169,110,.05);border:1px solid rgba(201,169,110,.15);border-radius:12px;padding:18px 20px;text-align:center}
.ord-help-title{font-size:.92rem;font-weight:600;color:#f0ece4;margin-bottom:5px}
.ord-help-sub{font-size:.78rem;color:rgba(240,236,228,.35);margin-bottom:14px}
.ord-help-wa{display:inline-flex;align-items:center;background:#25d366;border:none;border-radius:9px;padding:10px 20px;font-size:.85rem;font-weight:600;color:white;text-decoration:none;transition:background .2s}
.ord-help-wa:hover{background:#1da851;color:white}
.ord-review-textarea{width:100%;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:11px 14px;font-size:.88rem;color:#f0ece4;font-family:'Outfit',sans-serif;outline:none;resize:vertical;min-height:90px;transition:border-color .2s}
.ord-review-textarea:focus{border-color:#c9a96e}
.ord-review-textarea::placeholder{color:rgba(240,236,228,.2)}
.ord-review-file{width:100%;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:9px 14px;font-size:.82rem;color:rgba(240,236,228,.5);cursor:pointer}
.ord-review-submit{display:inline-flex;align-items:center;background:#c9a96e;border:none;border-radius:9px;padding:11px 24px;font-size:.88rem;font-weight:600;color:#0e0e0f;cursor:pointer;font-family:'Outfit',sans-serif;transition:background .2s}
.ord-review-submit:hover{background:#b8913d}
.star-picker{display:flex;gap:4px}
.star-label i{transition:color .15s}
/* Modal */
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
</style>
@endpush
@push('scripts')
<script>
function copyResi(resi) {
    navigator.clipboard.writeText(resi).then(() => {
        const btn = event.target.closest('button');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Tersalin!';
        btn.style.cssText = 'background:rgba(74,222,128,.15);border-color:rgba(74,222,128,.3);color:#4ade80';
        setTimeout(() => { btn.innerHTML = orig; btn.style.cssText = ''; }, 2000);
    });
}

// Close modal on backdrop click
document.getElementById('cancelModal')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('cancelModal') && (document.getElementById('cancelModal').style.display = 'none');
    }
});

// Star rating
document.querySelectorAll('.star-picker').forEach(picker => {
    const labels = picker.querySelectorAll('.star-label');
    const stars  = picker.querySelectorAll('.star-label i');
    labels.forEach((lbl, idx) => {
        lbl.addEventListener('mouseenter', () => {
            stars.forEach((s,si) => s.style.color = si <= idx ? '#c9a96e' : 'rgba(240,236,228,.15)');
        });
        lbl.addEventListener('click', () => {
            stars.forEach((s,si) => s.style.color = si <= idx ? '#c9a96e' : 'rgba(240,236,228,.15)');
        });
    });
    picker.addEventListener('mouseleave', () => {
        const checked = picker.querySelector('input:checked');
        const ci = checked ? parseInt(checked.value) - 1 : -1;
        stars.forEach((s,si) => s.style.color = si <= ci ? '#c9a96e' : 'rgba(240,236,228,.15)');
    });
    const checked = picker.querySelector('input:checked');
    if (checked) {
        const idx = parseInt(checked.value) - 1;
        stars.forEach((s,si) => s.style.color = si <= idx ? '#c9a96e' : 'rgba(240,236,228,.15)');
    }
});
</script>
@endpush
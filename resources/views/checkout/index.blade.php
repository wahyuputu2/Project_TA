@extends('layouts.app')
@section('title','Checkout')
@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-7">
            <h4 class="fw-bold mb-4">📦 Data Pengiriman</h4>
            <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h6 class="fw-bold mb-3">Alamat Pengiriman</h6>
                    <div class="mb-3">
                        <label class="form-label small">Nama Penerima</label>
                        <input type="text" name="shipping_name" class="form-control" value="{{ old('shipping_name', $user->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">No. HP/WhatsApp</label>
                        <input type="text" name="shipping_phone" class="form-control" value="{{ old('shipping_phone', $user->phone) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Alamat Lengkap</label>
                        <textarea name="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address', $user->address) }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Provinsi</label>
                            <select name="shipping_province_id" id="provinceSelect" class="form-select" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $prov)
                                <option value="{{ $prov['province_id'] }}">{{ $prov['province'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="shipping_province" id="provinceText">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Kota/Kabupaten</label>
                            <select name="shipping_city_id" id="citySelect" class="form-select" required disabled>
                                <option value="">-- Pilih Kota --</option>
                            </select>
                            <input type="hidden" name="shipping_city" id="cityText">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Kode Pos</label>
                            <input type="text" name="shipping_postal_code" class="form-control" value="{{ old('shipping_postal_code') }}" required>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h6 class="fw-bold mb-3">🚚 Pilih Kurir</h6>
                    <div class="row g-2 mb-3">
                        @foreach(['jne','jnt','pos','sicepat'] as $courier)
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="courier_name" id="c_{{ $courier }}" value="{{ $courier }}">
                            <label class="btn btn-outline-secondary w-100" for="c_{{ $courier }}">{{ strtoupper($courier) }}</label>
                        </div>
                        @endforeach
                    </div>
                    <div id="shippingOptions" class="d-none">
                        <label class="form-label small fw-semibold">Pilih Layanan</label>
                        <div id="serviceList"></div>
                    </div>
                    <input type="hidden" name="courier" id="selectedCourier">
                    <input type="hidden" name="courier_service" id="selectedService">
                    <input type="hidden" name="shipping_cost" id="shippingCost" value="0">
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg w-100" disabled>
                    Lanjut ke Pembayaran <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm p-4 sticky-top" style="top:80px">
                <h6 class="fw-bold mb-3">Ringkasan Pesanan</h6>
                @foreach($items as $item)
                @php $p = $item->product; @endphp
                <div class="d-flex gap-2 mb-3">
                    @if($p->mainImage())
                    <img src="{{ Storage::url($p->mainImage()->image_path) }}" class="rounded" style="width:60px;height:60px;object-fit:cover">
                    @endif
                    <div>
                        <div class="small fw-semibold">{{ $p->name }}</div>
                        <div class="small text-muted">Size {{ $p->size }}</div>
                        <div class="small fw-bold">Rp {{ number_format($p->price,0,',','.') }}</div>
                    </div>
                </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between small mb-1">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($items->sum(fn($c)=>$c->product->price),0,',','.') }}</span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span>Ongkos Kirim</span>
                    <span id="ongkirDisplay">-</span>
                </div>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span id="totalDisplay">-</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const subtotal = {{ $items->sum(fn($c)=>$c->product->price) }};

document.getElementById('provinceSelect').addEventListener('change', function() {
    const sel = this.options[this.selectedIndex];
    document.getElementById('provinceText').value = sel.text;
    const citySelect = document.getElementById('citySelect');
    citySelect.disabled = true;
    citySelect.innerHTML = '<option>Memuat...</option>';
    fetch('{{ route("checkout.cities") }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({province_id: this.value})
    }).then(r=>r.json()).then(cities => {
        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        cities.forEach(c => citySelect.innerHTML += `<option value="${c.city_id}" data-postal="${c.postal_code}">${c.type} ${c.city_name}</option>`);
        citySelect.disabled = false;
    });
});

document.getElementById('citySelect').addEventListener('change', function() {
    const sel = this.options[this.selectedIndex];
    document.getElementById('cityText').value = sel.text;
    const postalInput = document.querySelector('[name=shipping_postal_code]');
    if (sel.dataset.postal) postalInput.value = sel.dataset.postal;
    recalcShipping();
});

document.querySelectorAll('[name=courier_name]').forEach(r => r.addEventListener('change', recalcShipping));

function recalcShipping() {
    const cityId = document.getElementById('citySelect').value;
    const courier = document.querySelector('[name=courier_name]:checked')?.value;
    if (!cityId || !courier) return;
    fetch('{{ route("checkout.shipping") }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({city_id: cityId, courier})
    }).then(r=>r.json()).then(services => {
        const list = document.getElementById('serviceList');
        list.innerHTML = '';
        services.forEach((s,i) => {
            list.innerHTML += `
            <div class="form-check border rounded p-2 mb-2">
                <input class="form-check-input" type="radio" name="service_option" id="srv${i}" value="${s.cost}|${s.service}|${courier}">
                <label class="form-check-label w-100" for="srv${i}">
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">${s.service}</span>
                        <span class="text-primary fw-bold">Rp ${s.cost.toLocaleString('id')}</span>
                    </div>
                    <small class="text-muted">${s.description} · Estimasi ${s.etd} hari</small>
                </label>
            </div>`;
        });
        document.getElementById('shippingOptions').classList.remove('d-none');
        document.querySelectorAll('[name=service_option]').forEach(r => r.addEventListener('change', function() {
            const [cost, service, cour] = this.value.split('|');
            document.getElementById('shippingCost').value = cost;
            document.getElementById('selectedCourier').value = cour.toUpperCase();
            document.getElementById('selectedService').value = service;
            document.getElementById('ongkirDisplay').textContent = 'Rp '+parseInt(cost).toLocaleString('id');
            document.getElementById('totalDisplay').textContent = 'Rp '+(subtotal+parseInt(cost)).toLocaleString('id');
            document.getElementById('submitBtn').disabled = false;
        }));
    });
}
</script>
@endpush
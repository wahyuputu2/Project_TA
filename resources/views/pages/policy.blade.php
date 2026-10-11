@extends('layouts.app')
@section('title', 'Kebijakan Toko')
@section('content')

<div class="container py-5" style="max-width:780px">

    <div class="text-center mb-5">
        <div class="faq-eyebrow">Informasi Penting</div>
        <h2 class="faq-title">Kebijakan Toko</h2>
        <p class="faq-sub">Harap baca kebijakan kami sebelum berbelanja untuk pengalaman yang menyenangkan</p>
    </div>

    <div class="pol-grid">

        <div class="pol-card">
            <div class="pol-card-icon" style="background:rgba(74,222,128,.08);border-color:rgba(74,222,128,.2);color:#4ade80">
                <i class="bi bi-droplet-fill"></i>
            </div>
            <h5 class="pol-card-title">Standar Kebersihan</h5>
            <div class="pol-card-body">
                <p>Setiap produk yang kami jual telah melalui proses:</p>
                <ul>
                    <li>✅ Dicuci bersih dengan deterjen berkualitas</li>
                    <li>✅ Disetrika hingga rapi</li>
                    <li>✅ Dikemas dengan plastik bersih</li>
                    <li>✅ Disimpan di tempat kering dan bersih</li>
                </ul>
                <p>Kami berkomitmen bahwa setiap item yang kamu terima dalam kondisi bersih, wangi, dan siap pakai.</p>
            </div>
        </div>

        <div class="pol-card">
            <div class="pol-card-icon" style="background:rgba(201,169,110,.08);border-color:rgba(201,169,110,.2);color:#c9a96e">
                <i class="bi bi-camera-fill"></i>
            </div>
            <h5 class="pol-card-title">Kejujuran Produk</h5>
            <div class="pol-card-body">
                <p>Kami berkomitmen untuk selalu jujur dalam mendeskripsikan produk:</p>
                <ul>
                    <li>📸 Semua foto adalah foto asli barang</li>
                    <li>📏 Ukuran dicantumkan dalam cm (P×L)</li>
                    <li>⚠️ Cacat/minus selalu difoto dan dijelaskan</li>
                    <li>🏷️ Rating kondisi diberikan secara objektif</li>
                </ul>
                <p>Tidak ada produk yang kami sembunyikan kekurangannya.</p>
            </div>
        </div>

        <div class="pol-card">
            <div class="pol-card-icon" style="background:rgba(96,165,250,.08);border-color:rgba(96,165,250,.2);color:#60a5fa">
                <i class="bi bi-arrow-return-left"></i>
            </div>
            <h5 class="pol-card-title">Kebijakan Pengembalian</h5>
            <div class="pol-card-body">
                <p><strong style="color:#4ade80">Retur DITERIMA jika:</strong></p>
                <ul>
                    <li>Barang tidak sesuai foto/deskripsi</li>
                    <li>Ada kerusakan yang tidak diinformasikan</li>
                    <li>Salah kirim barang</li>
                </ul>
                <p><strong style="color:#f87171">Retur TIDAK BERLAKU jika:</strong></p>
                <ul>
                    <li>Tidak cocok / berubah pikiran</li>
                    <li>Tidak membaca deskripsi & ukuran sebelum beli</li>
                    <li>Kerusakan akibat pemakaian setelah diterima</li>
                </ul>
            </div>
        </div>

        <div class="pol-card">
            <div class="pol-card-icon" style="background:rgba(167,139,250,.08);border-color:rgba(167,139,250,.2);color:#a78bfa">
                <i class="bi bi-clock-history"></i>
            </div>
            <h5 class="pol-card-title">Prosedur Retur</h5>
            <div class="pol-card-body">
                <ol style="padding-left:18px">
                    <li>Hubungi admin via WhatsApp <strong>maksimal 1×24 jam</strong> setelah barang diterima.</li>
                    <li>Kirimkan foto barang bermasalah beserta nomor pesanan.</li>
                    <li>Tim kami akan memverifikasi dalam <strong>1 hari kerja</strong>.</li>
                    <li>Jika disetujui, kirimkan barang kembali dengan ongkir ditanggung kami.</li>
                    <li>Pengembalian dana diproses dalam <strong>3-5 hari kerja</strong>.</li>
                </ol>
            </div>
        </div>

        <div class="pol-card">
            <div class="pol-card-icon" style="background:rgba(234,179,8,.08);border-color:rgba(234,179,8,.2);color:#facc15">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h5 class="pol-card-title">Keamanan Transaksi</h5>
            <div class="pol-card-body">
                <p>Semua transaksi diproses melalui <strong>Midtrans</strong>, payment gateway terpercaya di Indonesia.</p>
                <ul>
                    <li>🔒 SSL encrypted 256-bit</li>
                    <li>🏦 Virtual Account bank lokal</li>
                    <li>📱 E-wallet & QRIS tersedia</li>
                    <li>💳 Kartu kredit/debit aman</li>
                </ul>
                <p>Kami tidak pernah menyimpan data kartu atau rekening kamu.</p>
            </div>
        </div>

        <div class="pol-card">
            <div class="pol-card-icon" style="background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.2);color:#f87171">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <h5 class="pol-card-title">Pengiriman & Packing</h5>
            <div class="pol-card-body">
                <p>Standar packaging kami:</p>
                <ul>
                    <li>📦 Dikemas dalam plastik OPP/poly bag</li>
                    <li>🗃️ Dibungkus bubble wrap untuk perlindungan</li>
                    <li>📮 Dimasukkan dalam box/karton tebal</li>
                    <li>🔖 Label pengiriman dicetak jelas</li>
                </ul>
                <p>Proses packing dilakukan <strong>1×24 jam</strong> setelah pembayaran dikonfirmasi.</p>
            </div>
        </div>

    </div>

    {{-- DISCLAIMER --}}
    <div class="pol-disclaimer">
        <i class="bi bi-info-circle-fill" style="color:#b45309;font-size:1.25rem;flex-shrink:0;margin-top:2px"></i>
        <div>
            <div style="font-size:.9rem;font-weight:700;color:#78350f;margin-bottom:6px">⚠️ Catatan Penting</div>
            <div style="font-size:.85rem;color:#92400e;line-height:1.8">
                Dengan melakukan pembelian di ThriftStore, kamu dianggap telah membaca dan menyetujui semua kebijakan di atas.
                Kebijakan ini dapat berubah sewaktu-waktu. Jika ada pertanyaan, jangan ragu untuk menghubungi kami.
            </div>
        </div>
    </div>

    {{-- CONTACT --}}
    <div class="faq-contact mt-4">
        <div class="faq-contact-icon">📋</div>
        <h5 class="faq-contact-title">Ada yang Ingin Ditanyakan?</h5>
        <p class="faq-contact-sub">Tim kami siap membantu setiap hari 08.00 – 21.00 WIB</p>
        <a href="https://wa.me/{{ config('services.whatsapp.number') }}"
           target="_blank" class="faq-wa-btn">
            <i class="bi bi-whatsapp me-2"></i>Hubungi Admin
        </a>
    </div>

</div>
@endsection
@push('styles')
<style>
/* ── HEADER ── */
.faq-eyebrow {
    display: inline-block;
    background: rgba(59,111,232,.1);
    border: 1px solid rgba(59,111,232,.25);
    color: #3b6fe8;
    font-size: .72rem;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 5px 16px;
    border-radius: 50px;
    margin-bottom: 14px;
}
.faq-title {
    font-family: 'Merriweather', serif;
    font-size: 2rem;
    font-weight: 700;
    color: #0f2044;
    margin-bottom: 10px;
}
.faq-sub {
    font-size: .88rem;
    color: #64748b;
    max-width: 500px;
    margin: 0 auto;
}

/* ── POLICY GRID ── */
.pol-grid {
    display: grid;
    grid-template-columns: repeat(2,1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media(max-width:640px) { .pol-grid { grid-template-columns: 1fr; } }

/* ── POLICY CARD ── */
.pol-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 24px;
    transition: border-color .2s, box-shadow .2s;
}
.pol-card:hover {
    border-color: #3b6fe8;
    box-shadow: 0 4px 16px rgba(59,111,232,.08);
}
.pol-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    border: 1px solid;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 16px;
}
.pol-card-title {
    font-size: .95rem;
    font-weight: 700;
    color: #0f2044;
    margin-bottom: 12px;
}
.pol-card-body {
    font-size: .83rem;
    color: #475569;
    line-height: 1.8;
}
.pol-card-body p { margin-bottom: 8px; }
.pol-card-body p:last-child { margin-bottom: 0; }
.pol-card-body ul,
.pol-card-body ol {
    padding-left: 0;
    list-style: none;
    margin-bottom: 10px;
}
.pol-card-body li {
    padding: 3px 0;
    color: #475569;
    display: flex;
    align-items: flex-start;
    gap: 6px;
}
.pol-card-body strong { color: #0f2044; font-weight: 600; }

/* ── DISCLAIMER ── */
.pol-disclaimer {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: #fef3c7;
    border: 2px solid #f59e0b;
    border-radius: 12px;
    padding: 20px 22px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(245,158,11,.12);
}
.pol-disclaimer-text {
    font-size: .85rem;
    color: #78350f;
    line-height: 1.8;
    font-weight: 500;
}

/* ── CONTACT BOX ── */
.faq-contact {
    text-align: center;
    background: #f0f7ff;
    border: 1.5px solid rgba(59,111,232,.2);
    border-radius: 16px;
    padding: 40px 24px;
}
.faq-contact-icon { font-size: 2.5rem; margin-bottom: 14px; }
.faq-contact-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f2044;
    margin-bottom: 8px;
}
.faq-contact-sub {
    font-size: .85rem;
    color: #64748b;
    margin-bottom: 20px;
}
.faq-wa-btn {
    display: inline-flex;
    align-items: center;
    background: #25d366;
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    font-size: .9rem;
    font-weight: 600;
    color: white;
    text-decoration: none;
    transition: background .2s;
}
.faq-wa-btn:hover { background: #1da851; color: white; }
</style>
@endpush
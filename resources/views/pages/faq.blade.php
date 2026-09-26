@extends('layouts.app')
@section('title', 'FAQ')
@section('content')

<div class="container py-5" style="max-width:780px">

    <div class="text-center mb-5">
        <div class="faq-eyebrow">Pusat Bantuan</div>
        <h2 class="faq-title">Pertanyaan yang Sering Ditanyakan</h2>
        <p class="faq-sub">Temukan jawaban atas pertanyaan umum seputar belanja di ThriftStore</p>
    </div>

    @php
    $faqs = [
        'Produk & Kondisi' => [
            [
                'q' => 'Apa yang dimaksud dengan kondisi Like New, 9/10, 8/10, dst?',
                'a' => 'Kami menggunakan sistem rating kondisi untuk menggambarkan keadaan barang secara jujur:
                        <ul style="margin-top:8px;padding-left:20px">
                            <li><strong style="color:#4ade80">Like New</strong> — Seperti baru, tidak ada tanda pemakaian, mungkin hanya dicoba sekali.</li>
                            <li><strong style="color:#c9a96e">9/10</strong> — Hampir sempurna, ada tanda pemakaian sangat minor yang hampir tidak terlihat.</li>
                            <li><strong style="color:#60a5fa">8/10</strong> — Kondisi baik, ada sedikit tanda pemakaian normal.</li>
                            <li><strong style="color:#a78bfa">7/10</strong> — Cukup baik, ada beberapa tanda pemakaian yang terlihat.</li>
                            <li><strong style="color:#f87171">6/10</strong> — Masih layak pakai, ada kekurangan yang sudah dijelaskan di deskripsi.</li>
                        </ul>'
            ],
            [
                'q' => 'Apakah foto produk adalah foto asli barang?',
                'a' => 'Ya, 100%. Semua foto yang kami unggah adalah foto asli dari barang yang akan kamu terima. Kami memfoto dari berbagai sudut (depan, belakang, detail merek, dan detail minus jika ada) untuk transparansi penuh.'
            ],
            [
                'q' => 'Apakah baju sudah dicuci/dilaundry sebelum dijual?',
                'a' => 'Ya! Semua produk sudah melalui proses cuci dan setrika sebelum difoto dan dijual. Kami memastikan setiap item bersih dan siap pakai saat kamu terima.'
            ],
            [
                'q' => 'Apa itu "Detail Minus"?',
                'a' => 'Detail Minus adalah foto khusus yang kami ambil untuk menunjukkan kekurangan atau cacat kecil pada barang, seperti noda kecil, jahitan yang sedikit terbuka, atau warna yang sedikit pudar. Ini adalah bentuk kejujuran kami agar tidak ada kekecewaan saat barang sampai.'
            ],
            [
                'q' => 'Bagaimana cara membaca ukuran P (Panjang) dan L (Lebar)?',
                'a' => 'Ukuran P adalah panjang baju diukur dari ujung bahu hingga bawah baju. Ukuran L adalah lebar baju diukur dari ketiak kiri ke ketiak kanan (satu sisi). Semua ukuran dalam sentimeter (cm). Kami sarankan untuk mengukur baju yang sudah kamu miliki dan membandingkannya.'
            ],
        ],
        'Pembelian & Pembayaran' => [
            [
                'q' => 'Mengapa barang yang saya lihat tiba-tiba berubah jadi "Dipesan"?',
                'a' => 'Semua produk kami bersifat 1-of-1 (stok hanya 1). Ketika seseorang menambahkan produk ke keranjang, barang tersebut otomatis terkunci selama 15 menit untuk mencegah pembelian ganda. Jika tidak diselesaikan dalam 15 menit, barang akan kembali tersedia.'
            ],
            [
                'q' => 'Metode pembayaran apa saja yang tersedia?',
                'a' => 'Kami menerima berbagai metode pembayaran melalui Midtrans:
                        <ul style="margin-top:8px;padding-left:20px">
                            <li>Transfer Bank (Virtual Account BCA, BNI, BRI, Mandiri, Permata)</li>
                            <li>E-Wallet (GoPay, OVO, ShopeePay, DANA)</li>
                            <li>QRIS (scan dari aplikasi apapun)</li>
                            <li>Kartu Kredit/Debit Visa & Mastercard</li>
                            <li>Alfamart / Indomaret</li>
                        </ul>'
            ],
            [
                'q' => 'Berapa lama batas waktu pembayaran?',
                'a' => 'Batas waktu pembayaran adalah 24 jam sejak pesanan dibuat. Jika tidak dibayar dalam waktu tersebut, pesanan akan otomatis dibatalkan dan produk kembali ke katalog.'
            ],
            [
                'q' => 'Apakah harga sudah termasuk ongkos kirim?',
                'a' => 'Belum. Harga yang tertera hanya untuk produk saja. Ongkos kirim dihitung secara otomatis saat checkout berdasarkan berat barang dan lokasi pengiriman kamu, menggunakan API RajaOngkir.'
            ],
        ],
        'Pengiriman' => [
            [
                'q' => 'Ekspedisi apa saja yang tersedia?',
                'a' => 'Kami bekerja sama dengan JNE, J&T Express, SiCepat, dan POS Indonesia. Kamu bisa memilih ekspedisi dan layanan yang sesuai saat checkout.'
            ],
            [
                'q' => 'Berapa lama proses packing setelah pembayaran dikonfirmasi?',
                'a' => 'Kami memproses dan mengemas pesanan dalam 1x24 jam kerja setelah pembayaran dikonfirmasi. Pesanan yang masuk setelah jam 15.00 WIB akan diproses keesokan harinya.'
            ],
            [
                'q' => 'Bagaimana cara melacak pesanan saya?',
                'a' => 'Setelah pesanan dikirim, nomor resi akan muncul di halaman "Pesanan Saya". Kamu bisa menggunakan nomor resi tersebut untuk melacak pengiriman di website ekspedisi terkait.'
            ],
        ],
        'Pengembalian & Garansi' => [
            [
                'q' => 'Apakah bisa retur atau tukar barang?',
                'a' => 'Kami menerima retur hanya jika:
                        <ul style="margin-top:8px;padding-left:20px">
                            <li>Barang yang diterima tidak sesuai dengan foto/deskripsi yang kami unggah.</li>
                            <li>Ada kerusakan yang tidak kami informasikan sebelumnya.</li>
                            <li>Barang salah kirim (bukan produk yang dipesan).</li>
                        </ul>
                        Retur <strong>tidak berlaku</strong> jika alasan pengembalian adalah karena tidak cocok atau berubah pikiran, karena kami sudah menyediakan informasi lengkap termasuk ukuran detail dan foto dari berbagai sudut.'
            ],
            [
                'q' => 'Bagaimana cara mengajukan retur?',
                'a' => 'Hubungi kami via WhatsApp dalam 1x24 jam setelah barang diterima. Sertakan foto barang yang bermasalah dan nomor pesanan kamu. Tim kami akan merespons dalam 1 hari kerja.'
            ],
        ],
    ];
    @endphp

    @foreach($faqs as $section => $items)
    <div class="faq-section">
        <div class="faq-section-title">{{ $section }}</div>
        @foreach($items as $i => $item)
        <div class="faq-item" id="faq-{{ $loop->parent->index }}-{{ $i }}">
            <button class="faq-question" onclick="toggleFaq('faq-{{ $loop->parent->index }}-{{ $i }}')">
                <span>{{ $item['q'] }}</span>
                <span class="faq-chevron"><i class="bi bi-chevron-down"></i></span>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-inner">{!! $item['a'] !!}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    {{-- STILL HAVE QUESTIONS --}}
    <div class="faq-contact">
        <div class="faq-contact-icon">💬</div>
        <h5 class="faq-contact-title">Masih Ada Pertanyaan?</h5>
        <p class="faq-contact-sub">Tim kami siap membantu kamu setiap hari dari jam 08.00 – 21.00 WIB</p>
        <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Halo+admin+ThriftStore,+saya+ingin+bertanya..."
           target="_blank" class="faq-wa-btn">
            <i class="bi bi-whatsapp me-2"></i>Chat Admin via WhatsApp
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

/* ── SECTION TITLE ── */
.faq-section { margin-bottom: 32px; }
.faq-section-title {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #3b6fe8;
    padding: 0 0 12px;
    margin-bottom: 8px;
    border-bottom: 1px solid #e2e8f0;
}

/* ── FAQ ITEM ── */
.faq-item {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 8px;
    background: #ffffff;
    transition: border-color .2s, box-shadow .2s;
}
.faq-item:hover { border-color: #3b6fe8; box-shadow: 0 2px 12px rgba(59,111,232,.08); }
.faq-item.open { border-color: #3b6fe8; }

/* ── QUESTION BUTTON ── */
.faq-question {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: none;
    border: none;
    color: #0f2044;
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    text-align: left;
    font-family: 'Plus Jakarta Sans', sans-serif;
    transition: background .15s;
}
.faq-question:hover { background: #f8fafc; }
.faq-item.open .faq-question { color: #3b6fe8; background: #f0f7ff; }

/* ── CHEVRON ── */
.faq-chevron {
    flex-shrink: 0;
    color: #94a3b8;
    transition: transform .3s;
}
.faq-item.open .faq-chevron { transform: rotate(180deg); color: #3b6fe8; }

/* ── ANSWER ── */
.faq-answer { max-height: 0; overflow: hidden; transition: max-height .35s ease; }
.faq-item.open .faq-answer { max-height: 600px; }
.faq-answer-inner {
    padding: 0 20px 18px;
    font-size: .87rem;
    color: #334155;
    line-height: 1.85;
    border-top: 1px solid #e2e8f0;
    background: #fafbfc;
}
.faq-answer-inner ul { padding-left: 0; list-style: none; margin-bottom: 10px; }
.faq-answer-inner li {
    padding: 4px 0;
    color: #475569;
    display: flex;
    align-items: flex-start;
    gap: 6px;
}
.faq-answer-inner li::before {
    content: '•';
    color: #3b6fe8;
    font-weight: 700;
    flex-shrink: 0;
    margin-top: 1px;
}
.faq-answer-inner strong { color: #0f2044; font-weight: 600; }
.faq-answer-inner p { margin-bottom: 8px; color: #475569; }
.faq-answer-inner p:last-child { margin-bottom: 0; }

/* ── CONTACT BOX ── */
.faq-contact {
    text-align: center;
    background: #f0f7ff;
    border: 1.5px solid rgba(59,111,232,.2);
    border-radius: 16px;
    padding: 40px 24px;
    margin-top: 16px;
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
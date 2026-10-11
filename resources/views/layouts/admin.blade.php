<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Dashboard') — ThriftStore Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
    :root {
        --sidebar-w  : 240px;
        --primary    : #1a1a1a;
        --bg         : #f8f8f6;
        --bg2        : #ffffff;
        --bg3        : #f2f2ef;
        --border     : #e8e8e8;
        --border2    : #d8d8d8;
        --text       : #1a1a1a;
        --muted      : #777777;
        --faint      : #aaaaaa;
        --gold       : #d4a843;
        --gold-dim   : rgba(212,168,67,.12);
        --gold-bdr   : rgba(212,168,67,.35);
        --gold-dark  : #b8882e;
        --radius     : 10px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Jost', sans-serif;
        background: var(--bg);
        color: var(--text);
        display: flex;
        min-height: 100vh;
        font-size: 14px;
        -webkit-font-smoothing: antialiased;
    }

    ::-webkit-scrollbar { width: 4px; height: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(26,26,26,.15); border-radius: 10px; }

    /* ── SIDEBAR ── */
    .adm-sidebar {
        width: var(--sidebar-w);
        min-height: 100vh;
        background: var(--primary);
        border-right: 1px solid rgba(255,255,255,.05);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0; left: 0;
        z-index: 100;
    }
    .adm-sidebar-brand {
        padding: 20px 18px 16px;
        border-bottom: 1px solid rgba(255,255,255,.1);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .adm-brand-logo {
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: #ffffff;
        white-space: nowrap;
    }
    .adm-brand-logo span { color: var(--gold); }
    .adm-brand-badge {
        background: var(--gold-dim);
        border: 1px solid var(--gold-bdr);
        color: var(--gold);
        font-size: .58rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        letter-spacing: .8px;
        text-transform: uppercase;
        margin-left: auto;
    }

    /* ── NAV ── */
    .adm-nav { padding: 10px; flex: 1; overflow-y: auto; }
    .adm-nav-section {
        font-size: .6rem;
        font-weight: 600;
        letter-spacing: 1.8px;
        text-transform: uppercase;
        color: rgba(255,255,255,.25);
        padding: 10px 8px 5px;
        margin-top: 4px;
    }
    .adm-nav-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border-radius: 8px;
        color: rgba(255,255,255,.6);
        text-decoration: none;
        font-size: .82rem;
        font-weight: 400;
        transition: all .18s;
        margin-bottom: 2px;
        white-space: nowrap;
        position: relative;
    }
    .adm-nav-link:hover { background: rgba(255,255,255,.08); color: #ffffff; }
    .adm-nav-link.active { background: rgba(212,168,67,.15); color: var(--gold); font-weight: 500; }
    .adm-nav-link.active i { color: var(--gold); }
    .adm-nav-link i { font-size: .95rem; width: 18px; text-align: center; flex-shrink: 0; }
    .adm-nav-badge {
        margin-left: auto;
        background: var(--gold);
        color: #000;
        font-size: .58rem;
        font-weight: 800;
        min-width: 16px;
        height: 16px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
    }
    .adm-nav-badge.red { background: #ef4444; color: white; }
    .adm-nav-divider { height: 1px; background: rgba(255,255,255,.1); margin: 8px 8px; }

    /* ── SIDEBAR FOOTER ── */
    .adm-sidebar-footer { padding: 12px; border-top: 1px solid rgba(255,255,255,.1); }
    .adm-user-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 9px;
    }
    .adm-user-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--gold-dim);
        border: 1.5px solid var(--gold-bdr);
        color: var(--gold);
        font-size: .75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .adm-user-name {
        font-size: .78rem;
        font-weight: 600;
        color: #ffffff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .adm-user-role { font-size: .65rem; color: rgba(255,255,255,.35); }
    .adm-logout-btn {
        background: none;
        border: none;
        color: rgba(255,255,255,.35);
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        transition: all .15s;
        margin-left: auto;
        flex-shrink: 0;
    }
    .adm-logout-btn:hover { background: rgba(239,68,68,.1); color: #f87171; }

    /* ── MAIN ── */
    .adm-main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

    /* ── TOPBAR ── */
    .adm-topbar {
        background: #ffffff;
        border-bottom: 1px solid var(--border);
        padding: 0 24px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 50;
    }
    .adm-page-title { font-size: .92rem; font-weight: 600; color: var(--text); }
    .adm-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .72rem;
        color: var(--faint);
    }
    .adm-breadcrumb a { color: var(--muted); text-decoration: none; }
    .adm-breadcrumb a:hover { color: var(--gold); }
    .adm-topbar-right { display: flex; align-items: center; gap: 10px; }
    .adm-topbar-badge {
        display: flex;
        align-items: center;
        gap: 7px;
        background: var(--gold-dim);
        border: 1px solid var(--gold-bdr);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: .75rem;
        color: var(--gold);
        font-weight: 500;
    }
    .adm-topbar-avatar {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: var(--gold);
        color: #fff;
        font-size: .68rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .adm-topbar-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        background: var(--bg3);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        cursor: pointer;
        transition: all .15s;
        text-decoration: none;
    }
    .adm-topbar-icon:hover { background: var(--border); color: var(--text); }

    /* ── CONTENT ── */
    .adm-content { padding: 24px; flex: 1; }

    /* ── FLASH ── */
    .adm-flash-ok {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        border-radius: 9px;
        padding: 11px 16px;
        font-size: .84rem;
        margin-bottom: 20px;
    }
    .adm-flash-err {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        border-radius: 9px;
        padding: 11px 16px;
        font-size: .84rem;
        margin-bottom: 20px;
    }

    /* ── CARDS ── */
    .adm-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .adm-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .85rem;
        font-weight: 600;
        color: var(--text);
        background: var(--bg3);
    }
    .adm-card-header i { color: var(--gold); }
    .adm-card-body { padding: 20px; }

    /* ── STAT CARDS ── */
    .adm-stat {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        transition: border-color .2s, box-shadow .2s;
        position: relative;
        overflow: hidden;
    }
    /* Semua stat card pakai garis gold di atas */
    .adm-stat::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: var(--gold);
    }
    .adm-stat:hover { border-color: var(--gold-bdr); box-shadow: 0 4px 16px rgba(212,168,67,.1); }
    .adm-stat-label {
        font-size: .7rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--muted);
        margin-bottom: 10px;
    }
    .adm-stat-value {
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
        margin-bottom: 6px;
        font-family: 'Playfair Display', serif;
    }
    .adm-stat-sub { font-size: .72rem; color: var(--faint); }
    .adm-stat-icon {
        position: absolute;
        right: 16px;
        top: 16px;
        font-size: 1.6rem;
        opacity: .08;
        color: var(--gold);
    }

    /* ── TABLES ── */
    .adm-table { width: 100%; border-collapse: collapse; }
    .adm-table th {
        font-size: .65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--faint);
        padding: 10px 16px;
        text-align: left;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
        background: var(--bg3);
    }
    .adm-table td {
        padding: 12px 16px;
        font-size: .83rem;
        color: var(--muted);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .adm-table tr:hover td { background: var(--bg3); }
    .adm-table tr:last-child td { border-bottom: none; }

    /* ── BUTTONS ── */
    .adm-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
        text-decoration: none;
        border: 1px solid transparent;
        font-family: 'Jost', sans-serif;
        white-space: nowrap;
    }
    .adm-btn-primary { background: var(--gold); color: #fff; border-color: var(--gold); }
    .adm-btn-primary:hover { background: var(--gold-dark); color: #fff; border-color: var(--gold-dark); }
    .adm-btn-secondary { background: var(--bg3); color: var(--muted); border-color: var(--border); }
    .adm-btn-secondary:hover { background: var(--border); color: var(--text); }
    .adm-btn-success { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
    .adm-btn-success:hover { background: #dcfce7; }
    .adm-btn-danger { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    .adm-btn-danger:hover { background: #fee2e2; }
    .adm-btn-sm { padding: 5px 10px; font-size: .72rem; }

    /* ── BADGES ── */
    .adm-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: .68rem;
        font-weight: 600;
        border: 1px solid;
    }
    .adm-badge-gold   { color: #92620a;    background: #fef9c3;                       border-color: #fde68a; }
    .adm-badge-green  { color: #166534;    background: #f0fdf4;                       border-color: #bbf7d0; }
    .adm-badge-yellow { color: #854d0e;    background: #fffbeb;                       border-color: #fde68a; }
    .adm-badge-blue   { color: #1e40af;    background: #eff6ff;                       border-color: #bfdbfe; }
    .adm-badge-red    { color: #991b1b;    background: #fef2f2;                       border-color: #fecaca; }
    .adm-badge-purple { color: #6b21a8;    background: #faf5ff;                       border-color: #e9d5ff; }
    .adm-badge-gray   { color: var(--muted); background: var(--bg3);                  border-color: var(--border); }
    .adm-badge-muted  { color: var(--muted); background: var(--bg3);                  border-color: var(--border); }

    /* ── INPUTS ── */
    .adm-input {
        width: 100%;
        background: #ffffff;
        border: 1px solid var(--border2);
        border-radius: 8px;
        padding: 9px 14px;
        font-size: .84rem;
        color: var(--text);
        font-family: 'Jost', sans-serif;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .adm-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-dim); }
    .adm-input::placeholder { color: var(--faint); }
    .adm-select {
        width: 100%;
        background: #ffffff;
        border: 1px solid var(--border2);
        border-radius: 8px;
        padding: 9px 14px;
        font-size: .84rem;
        color: var(--text);
        font-family: 'Jost', sans-serif;
        outline: none;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='rgba(26,26,26,0.4)' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px;
    }
    .adm-select:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-dim); }
    .adm-select option { background: #ffffff; color: var(--text); }

    /* ── PAGINATION ── */
    .pagination .page-link {
        background: var(--bg2) !important;
        border-color: var(--border) !important;
        color: var(--muted) !important;
        border-radius: 7px !important;
        margin: 0 2px;
        font-size: .78rem;
    }
    .pagination .page-item.active .page-link {
        background: var(--gold) !important;
        border-color: var(--gold) !important;
        color: #fff !important;
        font-weight: 700;
    }
    .pagination .page-link:hover {
        background: var(--gold-dim) !important;
        color: var(--gold-dark) !important;
        border-color: var(--gold-bdr) !important;
    }

    /* ══════════════════════════════════════════════════════════
       LIGHT-THEME COMPATIBILITY LAYER
       Semua view admin menggunakan warna dark-mode (#f0ece4 / rgba(240,236,228,...))
       CSS berikut memetakan warna-warna tersebut ke palet light-theme yang tepat,
       sehingga seluruh halaman (produk, kategori, brand, pesanan, ulasan, toko,
       pencairan dana) tampil dengan kontras yang baik di background putih.
    ══════════════════════════════════════════════════════════ */

    /* Judul halaman (h4) yang menggunakan warna dark-mode */
    [style*="color:#f0ece4"] { color: var(--text) !important; }

    /* Teks placeholder / subtitle yang menggunakan rgba dark-mode */
    [style*="color:rgba(240,236,228,.35)"],
    [style*="color:rgba(240,236,228,.3)"],
    [style*="color:rgba(240,236,228,.25)"],
    [style*="color:rgba(240,236,228,.18)"] { color: var(--muted) !important; }

    [style*="color:rgba(240,236,228,.45)"],
    [style*="color:rgba(240,236,228,.4)"],
    [style*="color:rgba(240,236,228,.5)"],
    [style*="color:rgba(240,236,228,.6)"],
    [style*="color:rgba(240,236,228,.65)"] { color: #555 !important; }

    /* Border separator dark-mode */
    [style*="border-top:1px solid rgba(255,255,255,.06)"],
    [style*="border-bottom:1px solid rgba(255,255,255,.05)"],
    [style*="border-bottom:1px solid rgba(255,255,255,.04)"],
    [style*="border-bottom:1px solid rgba(255,255,255,.07)"] {
        border-color: var(--border) !important;
    }

    /* Empty state rows */
    [style*="color:rgba(240,236,228,.25)"],
    td[style*="color:rgba(240,236,228,.25)"] { color: var(--faint) !important; }

    /* Filter label warna dark-mode */
    label[style*="color:rgba(240,236,228,.35)"] { color: var(--muted) !important; }

    /* Gambar placeholder dark-mode */
    div[style*="background:rgba(255,255,255,.04)"],
    div[style*="background:rgba(255,255,255,.05)"] {
        background: var(--bg3) !important;
        border: 1px solid var(--border) !important;
    }

    /* Link preview produk dark-mode */
    a[style*="background:rgba(255,255,255,.05)"] {
        background: var(--bg3) !important;
        border-color: var(--border) !important;
        color: var(--muted) !important;
    }
    a[style*="background:rgba(255,255,255,.05)"]:hover {
        background: var(--border) !important;
        color: var(--text) !important;
    }

    /* Pesanan: tombol "lihat toko" store link dark-mode */
    a[style*="color:rgba(240,236,228,.5)"] { color: var(--muted) !important; }

    /* Stat cards (digunakan di ulasan, toko, pencairan) */
    .stat-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: var(--gold);
    }
    .stat-card-icon {
        font-size: 1.6rem;
        color: var(--gold);
        margin-bottom: 10px;
        opacity: 0.8;
    }
    .stat-card-label {
        font-size: .7rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--muted);
        margin-bottom: 6px;
    }
    .stat-card-value {
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
        margin-bottom: 4px;
        font-family: 'Playfair Display', serif;
    }
    .stat-card-sub { font-size: .72rem; color: var(--faint); }

    /* Review cards: override warna dark-mode */
    .rv-card {
        background: var(--bg2) !important;
        border: 1px solid var(--border) !important;
    }
    .rv-card:hover { border-color: var(--gold-bdr) !important; }
    .rv-card-pending { border-color: #fde68a !important; }
    .rv-card-head {
        background: var(--bg3) !important;
        border-bottom-color: var(--border) !important;
    }
    .rv-user-name { color: var(--text) !important; }
    .rv-user-date { color: var(--faint) !important; }
    .rv-avatar {
        background: var(--gold-dim) !important;
        border-color: var(--gold-bdr) !important;
        color: var(--gold) !important;
    }
    .rv-product { border-bottom-color: var(--border) !important; }
    .rv-comment {
        color: var(--text) !important;
        border-bottom-color: var(--border) !important;
    }
    .rv-photos { border-bottom-color: var(--border) !important; }
    .rv-actions {
        background: var(--bg3) !important;
        border-top: 1px solid var(--border);
    }
    .rv-btn-approve {
        background: #f0fdf4 !important;
        border-color: #bbf7d0 !important;
        color: #166534 !important;
    }
    .rv-btn-approve:hover { background: #dcfce7 !important; }
    .rv-btn-hide {
        background: var(--bg3) !important;
        border-color: var(--border) !important;
        color: var(--muted) !important;
    }
    .rv-btn-hide:hover { background: var(--border) !important; color: var(--text) !important; }
    .rv-btn-delete {
        background: #fef2f2 !important;
        border-color: #fecaca !important;
        color: #991b1b !important;
    }
    .rv-btn-delete:hover { background: #fee2e2 !important; }
    .rv-label { color: var(--muted) !important; }

    /* Empty state dark reviews */
    div[style*="background:#151515"],
    .rv-card[style*="background:#151515"] {
        background: var(--bg2) !important;
        border-color: var(--border) !important;
    }

    /* Rating count text di review */
    span[style*="color:rgba(240,236,228,.4)"] { color: var(--muted) !important; }

    /* Pesanan: mode pengawasan notice biru — tetap tapi koreksi teks gelap */
    div[style*="background:rgba(96,165,250,.06)"] { background: #eff6ff !important; border-color: #bfdbfe !important; }
    div[style*="background:rgba(96,165,250,.06)"] div[style*="color:#60a5fa"] { color: #1e40af !important; }
    div[style*="background:rgba(96,165,250,.06)"] div[style*="color:rgba(240,236,228,.45)"] { color: #374151 !important; }
    div[style*="background:rgba(96,165,250,.06)"] strong[style*="color:rgba(240,236,228,.7)"] { color: #111827 !important; }
    span[style*="background:rgba(96,165,250,.08)"] {
        background: #eff6ff !important;
        border-color: #bfdbfe !important;
        color: #1e40af !important;
    }

    /* Pesanan: lock info resi */
    div[style*="background:rgba(255,255,255,.03)"][style*="border:1px solid rgba(255,255,255,.06)"] {
        background: var(--bg3) !important;
        border-color: var(--border) !important;
        color: var(--muted) !important;
    }

    /* Pesanan & toko: foto produk placeholder */
    div[style*="background:rgba(255,255,255,.03)"] { background: var(--bg3) !important; }

    /* Catatan pesanan display box */
    div[style*="white-space:pre-line"] {
        background: var(--bg3) !important;
        border-color: var(--border) !important;
        color: var(--text) !important;
    }

    /* Order index: tabs */
    .ord-tabs { display:flex;gap:4px;flex-wrap:wrap }
    .ord-tab {
        display:inline-flex;align-items:center;gap:7px;padding:8px 16px;
        border-radius:8px;font-size:.82rem;color:var(--muted);text-decoration:none;
        border:1px solid var(--border);background:transparent;transition:all .18s;
    }
    .ord-tab:hover { color:var(--text);border-color:var(--border2); }
    .ord-tab.active { background:var(--gold-dim);border-color:var(--gold-bdr);color:var(--gold-dark);font-weight:500; }
    .ord-tab-count { background:var(--bg3);color:inherit;font-size:.65rem;font-weight:700;padding:1px 6px;border-radius:10px; }
    .ord-tab.active .ord-tab-count { background:rgba(212,168,67,.15); }
    .ord-label { display:block;font-size:.68rem;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted);margin-bottom:7px; }

    /* Pesanan: timeline */
    .ord-timeline-step::before { background: var(--border) !important; }
    .ord-tl-dot { border-color:var(--border) !important; background:var(--bg3) !important; color:var(--faint) !important; }
    .ord-timeline-step.done .ord-tl-dot { border-color:var(--tl-color,#16a34a) !important; color:var(--tl-color,#16a34a) !important; background:#f0fdf4 !important; }
    .ord-timeline-step.current .ord-tl-dot { border-color:var(--gold) !important; color:var(--gold-dark) !important; background:var(--gold-dim) !important; }
    .ord-tl-label { color:var(--faint) !important; }
    .ord-timeline-step.done .ord-tl-label { color:var(--text) !important; }
    .ord-timeline-step.current .ord-tl-label { color:var(--gold-dark) !important; }
    .ord-tl-time { color:var(--faint) !important; }

    /* Pesanan: info rows kanan */
    .ord-info-row { border-bottom-color: var(--border) !important; }
    .ord-info-lbl { color:var(--muted) !important; }
    .ord-info-val { color:var(--text) !important; }

    /* Toko: border pagination dark */
    div[style*="border-top:1px solid rgba(255,255,255,.06)"] {
        border-top-color: var(--border) !important;
    }

    /* Produk: badge toko dark */
    span[style*="background:rgba(201,169,110,.1)"][style*="color:#c9a96e"] {
        background: var(--gold-dim) !important;
        border-color: var(--gold-bdr) !important;
        color: var(--gold-dark) !important;
    }

    /* Kondisi / status produk dark chips */
    span[style*="color:#4ade80"][style*="background:rgba(34,197,94,.1)"] {
        background: #f0fdf4 !important; color: #166534 !important;
    }
    span[style*="color:#facc15"][style*="background:rgba(234,179,8,.1)"] {
        background: #fffbeb !important; color: #854d0e !important;
    }
    span[style*="color:#f87171"][style*="background:rgba(239,68,68,.1)"] {
        background: #fef2f2 !important; color: #991b1b !important;
    }
    span[style*="color:#a78bfa"][style*="background:rgba(139,92,246,.1)"] {
        background: #faf5ff !important; color: #6b21a8 !important;
    }
    span[style*="color:#60a5fa"][style*="background:rgba(59,130,246,.1)"] {
        background: #eff6ff !important; color: #1e40af !important;
    }

    /* Pesanan index: status labels */
    span[style*="color:#4ade80"] { color: #166534 !important; }
    span[style*="color:#facc15"] { color: #854d0e !important; }

    /* Hapus modal dark-mode — biarkan tetap dark karena itu overlay */
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="adm-sidebar">
    <div class="adm-sidebar-brand">
        <div class="adm-brand-logo">Frog<span>Market</span></div>
        <span class="adm-brand-badge">Admin</span>
    </div>

    <nav class="adm-nav">
        <div class="adm-nav-section">Utama</div>
        <a href="{{ route('admin.dashboard') }}"
           class="adm-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="adm-nav-section">Katalog</div>
        <a href="{{ route('admin.products.index') }}"
           class="adm-nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
            <i class="bi bi-tags-fill"></i> Produk
            @php $totalProds = \App\Models\Product::count(); @endphp
            <span class="adm-nav-badge gold">{{ $totalProds }}</span>
        </a>
        <a href="{{ route('admin.categories.index') }}"
           class="adm-nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap-fill"></i> Kategori
        </a>
        <a href="{{ route('admin.brands.index') }}"
           class="adm-nav-link {{ request()->routeIs('admin.brands*') ? 'active' : '' }}">
            <i class="bi bi-bookmark-star-fill"></i> Brand
        </a>

        <div class="adm-nav-section">Transaksi</div>
        <a href="{{ route('admin.orders.index') }}"
           class="adm-nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
            <i class="bi bi-bag-check-fill"></i> Pesanan
            @php $newOrders = \App\Models\Order::where('status','paid')->count(); @endphp
            @if($newOrders > 0)
            <span class="adm-nav-badge red">{{ $newOrders }}</span>
            @endif
        </a>

        <div class="adm-nav-section">Marketplace</div>
        <a href="{{ route('admin.stores.index') }}"
           class="adm-nav-link {{ request()->routeIs('admin.stores*') ? 'active' : '' }}">
            <i class="bi bi-shop-window"></i> Kelola Toko
            @php $pendingStores = \App\Models\Store::where('status','pending')->count(); @endphp
            @if($pendingStores > 0)
            <span class="adm-nav-badge red">{{ $pendingStores }}</span>
            @endif
        </a>
        <a href="{{ route('admin.withdrawals.index') }}"
           class="adm-nav-link {{ request()->routeIs('admin.withdrawals*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Pencairan Dana
            @php $pendingWd = \App\Models\Withdrawal::where('status','pending')->count(); @endphp
            @if($pendingWd > 0)
            <span class="adm-nav-badge red">{{ $pendingWd }}</span>
            @endif
        </a>

        <div class="adm-nav-section">Analitik</div>
        <a href="{{ route('admin.reports') }}"
           class="adm-nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i> Laporan
        </a>

        <div class="adm-nav-divider"></div>
        <a href="{{ route('home') }}" target="_blank" class="adm-nav-link">
            <i class="bi bi-box-arrow-up-right"></i> Lihat Toko
        </a>
    </nav>

    <div class="adm-sidebar-footer">
        <div class="adm-user-card">
            <div class="adm-user-avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
            <div style="min-width:0">
                <div class="adm-user-name">{{ Auth::user()->name }}</div>
                <div class="adm-user-role">{{ ucfirst(Auth::user()->role) }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="adm-logout-btn" title="Logout">
                    <i class="bi bi-box-arrow-right" style="font-size:.9rem"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div class="adm-main">
    <div class="adm-topbar">
        <div class="adm-topbar-left">
            <div>
                <div class="adm-page-title">@yield('title','Dashboard')</div>
                <div class="adm-breadcrumb">
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                    <i class="bi bi-chevron-right" style="font-size:.55rem"></i>
                    <span>@yield('title','Dashboard')</span>
                </div>
            </div>
        </div>
        <div class="adm-topbar-right">
            <a href="{{ route('home') }}" target="_blank" class="adm-topbar-icon" title="Lihat Toko">
                <i class="bi bi-eye" style="font-size:.85rem"></i>
            </a>
            <div class="adm-topbar-badge">
                <div class="adm-topbar-avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                {{ Auth::user()->name }}
            </div>
        </div>
    </div>

    <div class="adm-content">
        @if(session('success'))
        <div class="adm-flash-ok"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="adm-flash-err"><i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
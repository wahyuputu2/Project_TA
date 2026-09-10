@extends('layouts.admin')
@section('title','Laporan Penjualan')
@section('content')
<h4 class="fw-bold mb-4">📊 Laporan Penjualan</h4>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-white shadow-sm">
            <div class="text-muted small">Total Pendapatan</div>
            <div class="h4 fw-bold text-success mt-1">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-white shadow-sm">
            <div class="text-muted small">Total Pesanan</div>
            <div class="h4 fw-bold text-primary mt-1">{{ $totalOrders }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-white shadow-sm">
            <div class="text-muted small">Menunggu Pembayaran</div>
            <div class="h4 fw-bold text-warning mt-1">{{ $pendingOrders }}</div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="card border-0 shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between mb-3">
        <h6 class="fw-bold mb-0">Grafik Penjualan</h6>
        <form method="GET">
            <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="7" {{ $period==7?'selected':'' }}>7 Hari</option>
                <option value="30" {{ $period==30?'selected':'' }}>30 Hari</option>
                <option value="90" {{ $period==90?'selected':'' }}>90 Hari</option>
            </select>
        </form>
    </div>
    <canvas id="salesChart" height="80"></canvas>
</div>

<!-- Top Products -->
<div class="card border-0 shadow-sm p-4">
    <h6 class="fw-bold mb-3">🏆 Produk Terlaris</h6>
    <table class="table table-sm">
        <thead><tr><th>#</th><th>Produk</th><th>Terjual</th></tr></thead>
        <tbody>
        @foreach($topProducts as $i => $product)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $product->name }}</td>
            <td><span class="badge bg-success">{{ $product->sold_count }}</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($salesData->pluck('date')) !!},
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: {!! json_encode($salesData->pluck('revenue')) !!},
            borderColor: '#1a1a2e',
            backgroundColor: 'rgba(26,26,46,.1)',
            tension: 0.4, fill: true,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>
@endpush
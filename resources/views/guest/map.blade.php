@extends('layouts.guest')

@section('title', 'Peta Sebaran Sampah')
@section('meta_description', 'Lihat peta interaktif sebaran titik sampah liar secara real-time di seluruh wilayah.')

@section('content')
<section id="map-page" class="py-8">
    <div class="max-w-[1280px] mx-auto px-6">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="font-delight font-semibold text-3xl text-ink tracking-tight mb-2" style="letter-spacing: -1.5px">Peta sebaran titik sampah</h1>
            <p class="text-body">Pantau lokasi laporan sampah secara real-time. Klik marker untuk melihat detail.</p>
        </div>

        {{-- Legend & Filters --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-5">
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-error"></span>
                    <span class="text-sm text-body">Belum ditangani</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-warning"></span>
                    <span class="text-sm text-body">Sedang diproses</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-primary"></span>
                    <span class="text-sm text-body">Selesai</span>
                </div>
            </div>

            <div class="flex gap-2">
                <button class="btn-ghost text-sm filter-btn active" data-filter="all" id="filter-all" onclick="filterMarkers('all')">Semua</button>
                <button class="btn-ghost text-sm filter-btn" data-filter="active" id="filter-active" onclick="filterMarkers('active')">Belum Selesai</button>
                <button class="btn-ghost text-sm filter-btn" data-filter="process" id="filter-process" onclick="filterMarkers('process')">Diproses</button>
                <button class="btn-ghost text-sm filter-btn" data-filter="done" id="filter-done" onclick="filterMarkers('done')">Selesai</button>
            </div>
        </div>

        {{-- Map --}}
        <div id="full-map" class="w-full h-[600px] rounded-xl shadow-card-lg overflow-hidden border border-hairline"></div>

        {{-- Info --}}
        <div class="mt-6 p-4 bg-canvas rounded-lg border border-hairline">
            <div class="flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 text-info mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium text-ink mb-1">Tentang peta ini</p>
                    <p class="text-sm text-body">Peta menampilkan seluruh titik laporan sampah liar yang telah masuk ke sistem. Data diperbarui secara real-time setiap ada laporan baru.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .filter-btn.active {
        background-color: #dcfce7;
        color: #16a34a;
    }
</style>
@endpush

@push('scripts')
<script>
let map;
let allMarkers = [];
const reports = @json($reports);

document.addEventListener('DOMContentLoaded', function() {
    map = L.map('full-map').setView([-6.32, 107.30], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    renderMarkers(reports);
});

function getStatusColor(status) {
    switch(status) {
        case 'Ditutup':
        case 'Selesai':
            return '#16a34a';
        case 'Ditugaskan':
        case 'Dalam Perjalanan':
        case 'Sedang Dibersihkan':
        case 'Menunggu Konfirmasi':
            return '#d97706';
        default:
            return '#dc2626';
    }
}

function getStatusGroup(status) {
    switch(status) {
        case 'Ditutup':
        case 'Selesai':
            return 'done';
        case 'Ditugaskan':
        case 'Dalam Perjalanan':
        case 'Sedang Dibersihkan':
        case 'Menunggu Konfirmasi':
            return 'process';
        default:
            return 'active';
    }
}

function getStatusLabel(status) {
    const labels = {
        'Menunggu':           'Menunggu Verifikasi',
        'Terverifikasi':      'Terverifikasi',
        'Ditolak':            'Ditolak',
        'Ditugaskan':         'Ditugaskan',
        'Dalam Perjalanan':   'Petugas Dalam Perjalanan',
        'Sedang Dibersihkan': 'Sedang Dibersihkan',
        'Selesai':            'Selesai',
        'Menunggu Konfirmasi':'Menunggu Konfirmasi',
        'Ditutup':            'Ditutup',
    };
    return labels[status] || status;
}

function renderMarkers(data) {
    allMarkers.forEach(m => map.removeLayer(m.marker));
    allMarkers = [];

    data.forEach(function(report) {
        const color = getStatusColor(report.status);
        const group = getStatusGroup(report.status);
        const categoryName = report.kategori ? report.kategori.nama : '-';

        const marker = L.circleMarker([report.lintang, report.bujur], {
            radius: 9,
            fillColor: color,
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.85,
        }).addTo(map);

        const dilaporkanPada = report.dilaporkan_pada
            ? new Date(report.dilaporkan_pada).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
            : '-';

        marker.bindPopup(`
            <div style="font-family: 'Plus Jakarta Sans', sans-serif; min-width: 200px;">
                <p style="font-weight:600; font-size:14px; color:#111827; margin-bottom:6px;">${report.judul}</p>
                <p style="font-size:12px; color:#6b7280; margin-bottom:2px;"><strong>Kode:</strong> ${report.kode_laporan}</p>
                <p style="font-size:12px; color:#6b7280; margin-bottom:2px;"><strong>Kategori:</strong> ${categoryName}</p>
                <p style="font-size:12px; color:#6b7280; margin-bottom:2px;"><strong>Status:</strong> ${getStatusLabel(report.status)}</p>
                <p style="font-size:12px; color:#6b7280; margin-bottom:2px;"><strong>Tanggal:</strong> ${dilaporkanPada}</p>
                ${report.alamat ? `<p style="font-size:12px; color:#6b7280; margin-top:4px;"><strong>Alamat:</strong> ${report.alamat}</p>` : ''}
            </div>
        `);

        allMarkers.push({ marker, group });
    });
}

function filterMarkers(filter) {
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

    allMarkers.forEach(({ marker, group }) => {
        if (filter === 'all' || group === filter) {
            marker.addTo(map);
        } else {
            map.removeLayer(marker);
        }
    });
}
</script>
@endpush

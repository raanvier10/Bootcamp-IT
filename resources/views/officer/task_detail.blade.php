@extends('layouts.officer')
@section('title', 'Eksekusi Tugas')
@section('page_title', 'Eksekusi Tugas')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 animate-fade-in-up">

    {{-- Back Button & Header --}}
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('officer.tasks') }}" class="w-10 h-10 rounded-full bg-canvas border border-hairline flex items-center justify-center text-mute hover:text-ink hover:bg-canvas-soft-2 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h2 class="text-xl font-bold text-ink">Kembali ke Daftar Tugas</h2>
        </div>
        <span class="{{ $tugas->kelas_badge_status }} px-3 py-1.5 rounded-md font-bold uppercase tracking-wider text-xs shadow-sm">
            {{ $tugas->label_status }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Info & Map --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Map & Base Info --}}
            <div class="bg-canvas border border-hairline rounded-[24px] shadow-sm overflow-hidden">
                <div id="map-container" class="w-full h-64 bg-canvas-soft relative z-10"></div>
                <div class="p-6 relative z-20">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                        <div>
                            <p class="text-xs font-bold text-mute uppercase tracking-wider mb-1">{{ $tugas->kode_laporan }}</p>
                            <h3 class="text-2xl font-black text-ink tracking-tight leading-tight">{{ $tugas->judul }}</h3>
                        </div>
                        <div class="shrink-0 flex items-center gap-2">
                            @if($tugas->prioritas == 'Mendesak')
                                <span class="bg-error text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1"><i data-lucide="flame" class="w-4 h-4"></i> Mendesak</span>
                            @elseif($tugas->prioritas == 'Tinggi')
                                <span class="bg-warning text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1"><i data-lucide="flame" class="w-4 h-4"></i> Tinggi</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-canvas-soft-2 p-4 rounded-xl border border-hairline mb-6">
                        <div class="flex items-start gap-3">
                            <i data-lucide="map-pin" class="w-5 h-5 text-primary shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-[10px] font-bold text-mute uppercase tracking-wider mb-0.5">Alamat / Patokan</p>
                                <p class="text-sm font-medium text-ink">{{ $tugas->alamat }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i data-lucide="user" class="w-5 h-5 text-primary shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-[10px] font-bold text-mute uppercase tracking-wider mb-0.5">Pelapor</p>
                                <p class="text-sm font-medium text-ink">{{ $tugas->user->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 sm:col-span-2 mt-2">
                            <i data-lucide="file-text" class="w-5 h-5 text-primary shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-[10px] font-bold text-mute uppercase tracking-wider mb-0.5">Deskripsi Laporan</p>
                                <p class="text-sm text-body leading-relaxed">{{ $tugas->deskripsi }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center gap-4 mt-2">
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $tugas->lintang }},{{ $tugas->bujur }}" target="_blank" class="w-full py-4 flex items-center justify-center gap-2 font-black text-base border-2 border-primary/20 text-primary bg-primary-soft/50 rounded-2xl hover:bg-primary hover:text-white transition-all shadow-sm">
                            <i data-lucide="navigation" class="w-6 h-6"></i> Buka Navigasi Peta (Google Maps)
                        </a>
                    </div>
                </div>
            </div>

            {{-- Photos (Before and After if exists) --}}
            <div class="bg-canvas border border-hairline rounded-[24px] shadow-sm p-6">
                <h4 class="text-base font-bold text-ink mb-4">Bukti Foto</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Sebelum --}}
                    <div>
                        <p class="text-xs font-bold text-mute uppercase tracking-wider mb-2">Sebelum (Dari Pelapor)</p>
                        @if($tugas->gambarSebelum->count() > 0)
                            <div class="aspect-[4/3] rounded-xl overflow-hidden bg-canvas-soft border border-hairline shadow-inner">
                                <img src="{{ asset('storage/' . $tugas->gambarSebelum->first()->jalur_gambar) }}" alt="Sebelum" class="w-full h-full object-contain bg-black/5 hover:scale-105 transition-transform duration-500 cursor-pointer" onclick="openLightbox(this.src)">
                            </div>
                        @else
                            <div class="aspect-[4/3] rounded-xl bg-canvas-soft border border-hairline border-dashed flex flex-col items-center justify-center text-mute">
                                <i data-lucide="image-off" class="w-8 h-8 mb-2 opacity-50"></i>
                                <span class="text-sm">Foto tidak tersedia</span>
                            </div>
                        @endif
                    </div>

                    {{-- Sesudah --}}
                    <div>
                        <p class="text-xs font-bold text-mute uppercase tracking-wider mb-2">Sesudah (Hasil Eksekusi)</p>
                        @if($tugas->gambarSesudah->count() > 0)
                            <div class="aspect-[4/3] rounded-xl overflow-hidden bg-canvas-soft border border-hairline shadow-inner">
                                <img src="{{ asset('storage/' . $tugas->gambarSesudah->first()->jalur_gambar) }}" alt="Sesudah" class="w-full h-full object-contain bg-black/5 hover:scale-105 transition-transform duration-500 cursor-pointer" onclick="openLightbox(this.src)">
                            </div>
                        @else
                            <div class="aspect-[4/3] rounded-xl bg-canvas-soft border border-hairline border-dashed flex flex-col items-center justify-center text-mute">
                                <i data-lucide="clock" class="w-8 h-8 mb-2 opacity-50"></i>
                                <span class="text-sm">Belum ada foto sesudah</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Execution Form --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-canvas border border-hairline rounded-[24px] shadow-card-md p-6 sticky top-24">
                <h4 class="text-lg font-black text-ink mb-2">Panel Eksekusi</h4>
                <p class="text-sm text-mute font-medium mb-6 pb-6 border-b border-hairline">Ikuti tahapan di bawah ini sesuai urutan kerja di lapangan.</p>

                <form action="{{ route('officer.tasks.update', $tugas->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Tahap 1: Menuju Lokasi --}}
                    <div class="relative pl-8">
                        <div class="absolute left-3 top-0 bottom-[-24px] w-0.5 bg-hairline"></div>
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full flex items-center justify-center {{ in_array($tugas->status, ['Dalam Perjalanan', 'Sedang Dibersihkan', 'Selesai', 'Ditutup']) ? 'bg-primary text-white ring-4 ring-primary-soft' : 'bg-canvas-soft border-2 border-hairline text-mute' }}">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </div>
                        
                        <h5 class="text-sm font-bold text-ink mb-1">Berangkat ke Lokasi</h5>
                        @if($tugas->status == 'Ditugaskan' || $tugas->status == 'Terverifikasi')
                            <p class="text-xs text-mute mb-3">Klik ini saat Anda mulai berangkat agar pelapor tahu.</p>
                            <button type="submit" name="action" value="menuju_lokasi" class="w-full btn-primary py-3.5 text-base font-black shadow-lg shadow-primary/30 flex items-center justify-center gap-2 rounded-xl">
                                <i data-lucide="navigation" class="w-5 h-5"></i> Mulai Perjalanan
                            </button>
                        @else
                            <p class="text-xs text-primary font-bold mt-1">Selesai ✓</p>
                        @endif
                    </div>

                    {{-- Tahap 2: Mulai Pembersihan --}}
                    <div class="relative pl-8">
                        <div class="absolute left-3 top-0 bottom-[-24px] w-0.5 bg-hairline"></div>
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full flex items-center justify-center {{ in_array($tugas->status, ['Sedang Dibersihkan', 'Selesai', 'Ditutup']) ? 'bg-primary text-white ring-4 ring-primary-soft' : 'bg-canvas-soft border-2 border-hairline text-mute' }}">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </div>
                        
                        <h5 class="text-sm font-bold text-ink mb-1">Mulai Eksekusi</h5>
                        @if($tugas->status == 'Dalam Perjalanan')
                            <p class="text-xs text-mute mb-3">Tiba di lokasi? Tekan ini saat mulai membersihkan sampah.</p>
                            <button type="submit" name="action" value="mulai_pembersihan" class="w-full bg-warning hover:bg-[#d97706] text-white font-black py-3.5 rounded-xl transition-colors shadow-lg shadow-warning/30 flex items-center justify-center gap-2 text-base">
                                <i data-lucide="loader" class="w-5 h-5"></i> Mulai Pembersihan
                            </button>
                        @elseif(in_array($tugas->status, ['Sedang Dibersihkan', 'Selesai', 'Ditutup']))
                            <p class="text-xs text-primary font-bold mt-1">Selesai ✓</p>
                        @else
                            <p class="text-xs text-mute/50 mt-1">Menunggu tahap sebelumnya</p>
                        @endif
                    </div>

                    {{-- Tahap 3: Selesaikan Tugas --}}
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full flex items-center justify-center {{ in_array($tugas->status, ['Selesai', 'Ditutup']) ? 'bg-success text-white ring-4 ring-success-soft' : 'bg-canvas-soft border-2 border-hairline text-mute' }}">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </div>
                        
                        <h5 class="text-sm font-bold text-ink mb-2">Penutupan Tugas</h5>
                        
                        @if($tugas->status == 'Sedang Dibersihkan')
                            <div class="bg-canvas-soft-2 p-4 rounded-xl border border-hairline mb-4 animate-fade-in space-y-4">
                                {{-- Input Foto Sesudah (Eco-Cam) --}}
                                <div>
                                    <label class="block text-xs font-bold text-ink mb-1.5">Foto Sesudah (Eco-Cam) <span class="text-error">*</span></label>
                                    
                                    <div id="inline-preview-container" class="relative w-full aspect-[4/3] bg-canvas-soft rounded-xl overflow-hidden shadow-inner mt-2 flex flex-col items-center justify-center border-2 border-dashed border-hairline group">
                                        <canvas id="photo-canvas" class="hidden w-full h-full object-contain bg-black"></canvas>
                                        
                                        <div id="inline-placeholder" class="text-center p-6">
                                            <div class="w-12 h-12 bg-success-soft rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                                <i data-lucide="camera" class="w-6 h-6 text-success"></i>
                                            </div>
                                            <p class="text-xs font-semibold text-ink">Ambil Bukti Foto</p>
                                            <p class="text-[10px] text-mute mt-1 mb-3">Dilengkapi Watermark GPS & Waktu otomatis</p>
                                            <button type="button" id="btn-open-camera" class="px-4 py-2 bg-success text-white text-[10px] font-bold rounded-lg shadow-lg shadow-success/30 flex items-center justify-center gap-1.5 hover:bg-success/90 mx-auto">
                                                <i data-lucide="camera" class="w-3.5 h-3.5"></i> Buka Kamera
                                            </button>
                                        </div>

                                        <button type="button" id="btn-retake-inline" class="hidden absolute bottom-3 right-3 bg-black/60 backdrop-blur-md rounded-full px-3 py-1.5 text-white text-[10px] font-semibold hover:bg-black/80 flex items-center gap-1.5 transition-all shadow-lg border border-white/10">
                                            <i data-lucide="refresh-cw" class="w-3 h-3"></i> Foto Ulang
                                        </button>
                                    </div>
                                    
                                    <input type="file" name="foto_sesudah" id="foto_sesudah" class="hidden" required>
                                    @error('foto_sesudah')<p class="text-error text-[10px] mt-1">{{ $message }}</p>@enderror
                                </div>

                                {{-- Catatan Penanganan --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-xs font-bold text-ink">Catatan Eksekusi <span class="text-mute font-normal">(Opsional)</span></label>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        <button type="button" onclick="setNote('Lokasi telah bersih 100%')" class="px-2.5 py-1.5 bg-canvas border border-hairline rounded-lg text-[10px] font-bold text-mute hover:text-primary hover:border-primary hover:bg-primary-soft transition-colors active:scale-95">Bersih 100%</button>
                                        <button type="button" onclick="setNote('Sampah telah diangkut ke TPA')" class="px-2.5 py-1.5 bg-canvas border border-hairline rounded-lg text-[10px] font-bold text-mute hover:text-primary hover:border-primary hover:bg-primary-soft transition-colors active:scale-95">Diangkut ke TPA</button>
                                        <button type="button" onclick="setNote('Tugas selesai tanpa kendala')" class="px-2.5 py-1.5 bg-canvas border border-hairline rounded-lg text-[10px] font-bold text-mute hover:text-primary hover:border-primary hover:bg-primary-soft transition-colors active:scale-95">Selesai Normal</button>
                                    </div>
                                    <textarea id="catatan_input" name="catatan_penanganan" rows="2" class="w-full bg-canvas border border-hairline rounded-xl text-sm p-3 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none" placeholder="Pilih cepat di atas, atau ketik jika ada masalah..."></textarea>
                                    @error('catatan_penanganan')<p class="text-error text-[10px] mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            
                            <button type="submit" name="action" value="selesaikan_tugas" class="w-full bg-success hover:bg-[#047857] text-white font-bold py-3 rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                                <i data-lucide="check-circle" class="w-5 h-5"></i> Tandai Selesai
                            </button>

                        @elseif(in_array($tugas->status, ['Selesai', 'Ditutup']))
                            <div class="mt-3 bg-success-soft border border-success/20 p-4 rounded-xl flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-success text-white rounded-full flex items-center justify-center mb-2 shadow-sm">
                                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                                </div>
                                <h4 class="font-bold text-success">Tugas Selesai</h4>
                                <p class="text-xs text-success/80 mt-1 font-medium">Terima kasih atas kerja keras Anda menjaga kebersihan kota.</p>
                            </div>
                        @else
                            <p class="text-xs text-mute/50 mt-1">Form akan terbuka setelah tahap sebelumnya selesai.</p>
                        @endif
                    </div>
                </form>
            </div>

            @if($tugas->ulasan)
            <div class="bg-canvas border border-hairline rounded-[24px] shadow-card-md p-6">
                <h4 class="text-lg font-black text-ink mb-4">Ulasan Pelapor</h4>
                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i data-lucide="star" class="w-5 h-5 {{ $i <= $tugas->ulasan->rating ? 'text-warning fill-warning' : 'text-hairline' }}"></i>
                    @endfor
                    <span class="text-sm font-bold text-ink ml-2">{{ $tugas->ulasan->rating }}/5</span>
                </div>
                @if($tugas->ulasan->komentar)
                    <p class="text-sm text-body bg-canvas-soft p-3 rounded-xl border border-hairline mt-2">{{ $tugas->ulasan->komentar }}</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Full Screen Camera Modal --}}
<div id="camera-modal" class="fixed inset-0 z-[9999] bg-black hidden flex-col">
    <div class="relative flex-1 w-full h-full">
        <video id="camera-stream" class="w-full h-full object-cover" autoplay playsinline></video>
        
        <div class="absolute top-0 left-0 right-0 p-4 pt-6 md:pt-4 flex justify-between items-center bg-gradient-to-b from-black/70 to-transparent z-10">
            <p class="text-white font-bold text-sm tracking-wide flex items-center gap-2">
                <span class="w-2 h-2 bg-success rounded-full animate-pulse"></span> Eco-Cam Petugas
            </p>
            <button type="button" id="btn-close-camera" class="w-10 h-10 rounded-full bg-black/40 flex items-center justify-center text-white backdrop-blur-sm hover:bg-black/60 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div id="camera-overlay" class="absolute bottom-28 left-0 right-0 p-5 text-white text-xs drop-shadow-lg z-10">
            <div class="bg-black/30 backdrop-blur-sm p-3 rounded-xl border border-white/10 inline-block">
                <p class="font-bold text-sm">Petugas: {{ auth()->user()->nama ?? auth()->user()->name }}</p>
                <p id="overlay-location" class="opacity-90 mt-0.5 font-medium">Mencari lokasi GPS...</p>
                <p id="overlay-time" class="opacity-80 mt-0.5 font-medium"></p>
            </div>
        </div>
        
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 hidden z-10" id="camera-loading">
            <div class="bg-black/50 backdrop-blur-md p-4 rounded-2xl flex flex-col items-center gap-3">
                <i data-lucide="loader" class="w-8 h-8 text-white animate-spin"></i>
                <p class="text-white text-xs font-semibold">Mengakses Kamera...</p>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black/90 to-transparent flex items-center justify-center pb-6 z-10">
            <button type="button" id="btn-capture" class="hidden w-16 h-16 bg-transparent border-4 border-white rounded-full flex items-center justify-center hover:scale-105 transition-transform active:scale-95 group">
                <div class="w-12 h-12 bg-white rounded-full group-active:bg-gray-300 transition-colors"></div>
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Styling leaflet z-index to stay below sticky headers/menus */
    .leaflet-pane { z-index: 10 !important; }
    .leaflet-top, .leaflet-bottom { z-index: 10 !important; }
</style>
@endpush

@push('scripts')
<script>
    // Inisialisasi Peta Leaflet
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $tugas->lintang }};
        const lng = {{ $tugas->bujur }};
        
        const map = L.map('map-container', {
            zoomControl: false // kita hilangkan default zoom untuk custom
        }).setView([lat, lng], 16);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 20
        }).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Custom Marker
        const markerIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `
                <div class="relative flex items-center justify-center w-8 h-8">
                    <div class="absolute inset-0 bg-primary opacity-20 rounded-full animate-ping"></div>
                    <div class="relative z-10 w-6 h-6 bg-primary rounded-full border-2 border-white shadow-md flex items-center justify-center">
                        <div class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                </div>
            `,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        L.marker([lat, lng], {icon: markerIcon}).addTo(map)
            .bindPopup("<b class='font-jakarta'>Lokasi Sampah</b><br/>{{ $tugas->alamat }}").openPopup();
    });

    // Setup Eco-Cam Variables
    let videoStream = null;
    let currentLat = '0.00';
    let currentLng = '0.00';

    const video = document.getElementById('camera-stream');
    const canvas = document.getElementById('photo-canvas');
    const btnCapture = document.getElementById('btn-capture');
    const cameraOverlay = document.getElementById('camera-overlay');
    const overlayLocation = document.getElementById('overlay-location');
    const overlayTime = document.getElementById('overlay-time');
    const loading = document.getElementById('camera-loading');

    const modal = document.getElementById('camera-modal');
    const btnOpenCamera = document.getElementById('btn-open-camera');
    const btnCloseCamera = document.getElementById('btn-close-camera');
    const btnRetakeInline = document.getElementById('btn-retake-inline');
    const inlinePlaceholder = document.getElementById('inline-placeholder');

    if(btnOpenCamera) btnOpenCamera.addEventListener('click', openCameraModal);
    if(btnRetakeInline) btnRetakeInline.addEventListener('click', openCameraModal);
    if(btnCloseCamera) btnCloseCamera.addEventListener('click', closeCameraModal);

    // Get location continuously for watermark
    function updateLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                currentLat = pos.coords.latitude.toFixed(6);
                currentLng = pos.coords.longitude.toFixed(6);
                if(overlayLocation) overlayLocation.innerText = `Lat: ${currentLat}, Lng: ${currentLng}`;
            }, function() {
                if(overlayLocation) overlayLocation.innerText = "GPS tidak aktif";
            });
        }
    }
    
    updateLocation();
    setInterval(updateLocation, 10000); // update every 10s
    
    // Live time for overlay
    setInterval(() => {
        if(overlayTime) overlayTime.innerText = new Date().toLocaleString('id-ID');
    }, 1000);

    function openCameraModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        if(window.lucide) window.lucide.createIcons();
        startCamera();
    }

    function closeCameraModal() {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
    }

    async function startCamera() {
        loading.classList.remove('hidden');
        btnCapture.classList.add('hidden');
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: "environment", width: { ideal: 1920 }, height: { ideal: 1080 } } 
            });
            video.srcObject = videoStream;
            video.onloadedmetadata = () => {
                loading.classList.add('hidden');
                btnCapture.classList.remove('hidden');
            };
        } catch (err) {
            loading.classList.add('hidden');
            alert("Akses kamera ditolak. Mohon izinkan akses kamera di browser Anda.");
            closeCameraModal();
        }
    }

    if(btnCapture) {
        btnCapture.addEventListener('click', function() {
            if (!videoStream) return;
            
            // Limit resolution to prevent large files
            const MAX_SIZE = 1280;
            let targetWidth = video.videoWidth;
            let targetHeight = video.videoHeight;
            
            if (targetWidth > targetHeight) {
                if (targetWidth > MAX_SIZE) {
                    targetHeight = Math.round(targetHeight * (MAX_SIZE / targetWidth));
                    targetWidth = MAX_SIZE;
                }
            } else {
                if (targetHeight > MAX_SIZE) {
                    targetWidth = Math.round(targetWidth * (MAX_SIZE / targetHeight));
                    targetHeight = MAX_SIZE;
                }
            }
            
            canvas.width = targetWidth;
            canvas.height = targetHeight;
            const ctx = canvas.getContext('2d');
            
            // Draw image
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Draw Watermark
            const isPortrait = canvas.height > canvas.width;
            const baseSize = isPortrait ? canvas.width : canvas.height;
            const panelHeight = Math.floor(baseSize * 0.18);
            const titleSize = Math.max(Math.floor(baseSize * 0.035), 18);
            const textSize = Math.max(Math.floor(baseSize * 0.025), 12);
            const margin = Math.max(Math.floor(baseSize * 0.04), 15);
            
            ctx.fillStyle = "rgba(0, 0, 0, 0.65)";
            ctx.fillRect(0, canvas.height - panelHeight, canvas.width, panelHeight);
            
            ctx.fillStyle = "#10b981"; // Success color for officer
            ctx.font = `bold ${titleSize}px 'Plus Jakarta Sans', Arial`;
            ctx.textAlign = "left";
            ctx.fillText("TrashReport Eco-Cam (Bukti Bersih)", margin, canvas.height - panelHeight + titleSize + (margin/2));
            
            ctx.fillStyle = "white";
            ctx.font = `500 ${textSize}px 'Plus Jakarta Sans', Arial`;
            ctx.fillText("Petugas: {{ auth()->user()->nama ?? auth()->user()->name }}", margin, canvas.height - panelHeight + titleSize + textSize + margin);
            
            const locText = overlayLocation.innerText;
            ctx.fillText("Lokasi: " + locText, margin, canvas.height - margin);
            
            const timeText = new Date().toLocaleString('id-ID');
            ctx.textAlign = "right";
            ctx.fillText(timeText, canvas.width - margin, canvas.height - margin);
            
            // Create file and append to input
            canvas.toBlob((blob) => {
                const file = new File([blob], "eco-cam-selesai-" + Date.now() + ".jpg", { type: "image/jpeg" });
                const container = new DataTransfer();
                container.items.add(file);
                document.getElementById('foto_sesudah').files = container.files;
                
                // Show canvas in form
                canvas.classList.remove('hidden');
                inlinePlaceholder.classList.add('hidden');
                btnRetakeInline.classList.remove('hidden');
                
                closeCameraModal();
            }, 'image/jpeg', 0.85);
        });
    }

    // Quick Text Setter
    function setNote(text) {
        document.getElementById('catatan_input').value = text;
    }
</script>
@endpush
@endsection

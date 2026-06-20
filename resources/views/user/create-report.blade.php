@extends('layouts.user')
@section('title', 'Buat Laporan')
@section('page_title', 'Buat Laporan Baru')
@section('content')
<div class="w-full">
    <div class="bg-canvas rounded-xl shadow-card-lg border border-hairline overflow-hidden">
        <div class="p-6 border-b border-hairline">
            <h2 class="text-lg font-semibold text-ink tracking-tight">Eco-Cam — Laporkan Sampah</h2>
            <p class="text-sm text-body mt-1">Ambil foto sampah, pilih kategori, dan kirim laporan Anda.</p>
        </div>
        <form method="POST" action="{{ route('user.report.store') }}" enctype="multipart/form-data" class="p-6 space-y-6" id="create-report-form">
            @csrf
            {{-- Photo Upload (Eco-Cam) --}}
            <div>
                <label class="form-label">Foto Sampah (Eco-Cam)</label>
                
                {{-- Inline Preview Area --}}
                <div id="inline-preview-container" class="relative w-full aspect-[3/4] md:h-96 bg-canvas-soft rounded-xl overflow-hidden shadow-inner mt-2 flex flex-col items-center justify-center border-2 border-dashed border-hairline group">
                    <canvas id="photo-canvas" class="hidden w-full h-full object-contain"></canvas>
                    
                    <div id="inline-placeholder" class="text-center p-6">
                        <div class="w-16 h-16 bg-primary-soft rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i data-lucide="camera" class="w-8 h-8 text-primary"></i>
                        </div>
                        <p class="text-sm font-semibold text-ink">Belum ada foto</p>
                        <p class="text-xs text-mute mt-1 mb-5">Ambil foto langsung di lokasi kejadian</p>
                        <button type="button" id="btn-open-camera" class="btn-primary-sm shadow-lg shadow-primary/30">
                            <i data-lucide="camera" class="w-4 h-4"></i> Buka Kamera Pintar
                        </button>
                    </div>

                    <button type="button" id="btn-retake-inline" class="hidden absolute bottom-4 right-4 bg-black/60 backdrop-blur-md rounded-full px-4 py-2 text-white text-xs font-semibold hover:bg-black/80 flex items-center gap-2 transition-all shadow-lg border border-white/10">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Foto Ulang
                    </button>
                </div>
                
                <input type="file" name="photo" id="photo" class="hidden">
                @error('photo')<p class="form-error">{{ $message }}</p>@enderror
                <p class="text-[11px] text-mute mt-2 font-medium"><i data-lucide="shield-check" class="w-3.5 h-3.5 inline mr-1 text-success"></i>Dilindungi dengan sistem anti-hoax (Watermark GPS & Waktu otomatis).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="title" class="form-label">Judul Laporan</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Contoh: Tumpukan sampah di pinggir jalan" class="form-input" required>
                    @error('title')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="category_id" class="form-label">Kategori Sampah</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="district_id" class="form-label">Wilayah</label>
                <select name="district_id" id="district_id" class="form-select" required>
                    <option value="">Pilih wilayah</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist->id }}" {{ old('district_id') == $dist->id ? 'selected' : '' }}>{{ $dist->nama }}</option>
                    @endforeach
                </select>
                @error('district_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="form-label">Deskripsi Kondisi</label>
                <textarea name="description" id="description" placeholder="Jelaskan kondisi lokasi secara rinci..." class="form-textarea" rows="4" required>{{ old('description') }}</textarea>
                <div class="flex justify-between items-center mt-1">
                    <p class="text-xs text-mute transition-colors" id="desc-helper">Minimal 20, maksimal 500 karakter.</p>
                    <p class="text-xs font-bold text-mute transition-colors" id="char-counter">0/500 Karakter</p>
                </div>
                @error('description')<p class="form-error mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- GPS --}}
            <div>
                <label class="form-label">Lokasi GPS</label>
                <div id="location-map" class="w-full h-48 rounded-xl border border-hairline mb-3"></div>
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" placeholder="Latitude" class="form-input" readonly required>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" placeholder="Longitude" class="form-input" readonly required>
                </div>
                <input type="hidden" name="address" id="address" value="{{ old('address', '-') }}">
                <button type="button" onclick="getLocation()" class="btn-secondary-sm mt-3" id="btn-get-location">
                    <i data-lucide="crosshair" class="w-4 h-4"></i>Ambil Lokasi Saat Ini
                </button>
                @error('latitude')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-hairline mt-4">
                <a href="{{ route('user.dashboard') }}" onclick="return confirm('Apakah Anda yakin ingin membatalkan? Data laporan yang sudah diisi akan hilang.')" class="btn-secondary w-full sm:w-1/3 justify-center text-center py-3.5 font-bold order-last sm:order-first">Batal</a>
                <button type="submit" class="btn-primary w-full sm:w-2/3 justify-center py-3.5 text-base font-bold shadow-lg shadow-primary/40" id="submit-report-btn">
                    <i data-lucide="send" class="w-5 h-5"></i>Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Full Screen Camera Modal --}}
<div id="camera-modal" class="fixed inset-0 z-[9999] bg-black hidden flex-col">
    <div class="relative flex-1 w-full h-full">
        <video id="camera-stream" class="w-full h-full object-cover" autoplay playsinline></video>
        
        {{-- Top Bar --}}
        <div class="absolute top-0 left-0 right-0 p-4 pt-6 md:pt-4 flex justify-between items-center bg-gradient-to-b from-black/70 to-transparent z-10">
            <p class="text-white font-bold text-sm tracking-wide flex items-center gap-2">
                <span class="w-2 h-2 bg-error rounded-full animate-pulse"></span> Eco-Cam Aktif
            </p>
            <button type="button" id="btn-close-camera" class="w-10 h-10 rounded-full bg-black/40 flex items-center justify-center text-white backdrop-blur-sm hover:bg-black/60 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Overlay info for live view --}}
        <div id="camera-overlay" class="absolute bottom-28 left-0 right-0 p-5 text-white text-xs drop-shadow-lg z-10">
            <div class="bg-black/30 backdrop-blur-sm p-3 rounded-xl border border-white/10 inline-block">
                <p class="font-bold text-sm">{{ auth()->user()->nama ?? auth()->user()->name }}</p>
                <p id="overlay-location" class="opacity-90 mt-0.5 font-medium">Mencari lokasi GPS...</p>
                <p id="overlay-time" class="opacity-80 mt-0.5 font-medium"></p>
            </div>
        </div>
        
        {{-- Loading --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 hidden z-10" id="camera-loading">
            <div class="bg-black/50 backdrop-blur-md p-4 rounded-2xl flex flex-col items-center gap-3">
                <i data-lucide="loader" class="w-8 h-8 text-white animate-spin"></i>
                <p class="text-white text-xs font-semibold">Mengakses Kamera...</p>
            </div>
        </div>

        {{-- Shutter Area --}}
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black/90 to-transparent flex items-center justify-center pb-6 z-10">
            <button type="button" id="btn-capture" class="hidden w-16 h-16 bg-transparent border-4 border-white rounded-full flex items-center justify-center hover:scale-105 transition-transform active:scale-95 group">
                <div class="w-12 h-12 bg-white rounded-full group-active:bg-gray-300 transition-colors"></div>
            </button>
        </div>
    </div>
</div>


@push('scripts')
<script>
let locationMap;
let locationMarker;
let videoStream = null;

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
const form = document.getElementById('create-report-form');
const descInput = document.getElementById('description');
const descHelper = document.getElementById('desc-helper');

// Auto clear error state when user types/selects
document.querySelectorAll('input[required], select[required], textarea[required]').forEach(el => {
    el.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('border-error', 'bg-error-soft/20', 'ring-1', 'ring-error');
        }
    });
});

// Character Counter for Description
const charCounter = document.getElementById('char-counter');
descInput.addEventListener('input', function() {
    const text = this.value;
    const charCount = text.length;
    charCounter.innerText = `${charCount}/500 Karakter`;
    
    // Auto-truncate if exceeds
    if (charCount > 500) {
        this.value = text.substring(0, 500);
        charCounter.innerText = `500/500 Karakter`;
        
        charCounter.classList.add('text-error');
        charCounter.classList.remove('text-success', 'text-warning', 'text-mute');
        descHelper.classList.add('text-error');
        descHelper.classList.remove('text-success', 'text-warning', 'text-mute');
        descHelper.innerText = "Maksimal 500 karakter tercapai.";
    } else if (charCount > 0 && charCount < 20) {
        charCounter.classList.add('text-warning');
        charCounter.classList.remove('text-success', 'text-mute', 'text-error');
        descHelper.classList.add('text-warning');
        descHelper.classList.remove('text-success', 'text-mute', 'text-error');
        descHelper.innerText = `Kurang ${20 - charCount} karakter lagi.`;
    } else if (charCount >= 20 && charCount <= 500) {
        charCounter.classList.add('text-success');
        charCounter.classList.remove('text-warning', 'text-mute', 'text-error');
        descHelper.classList.add('text-success');
        descHelper.classList.remove('text-warning', 'text-mute', 'text-error');
        descHelper.innerText = "Jumlah karakter sudah memenuhi syarat.";
    } else {
        charCounter.classList.add('text-mute');
        charCounter.classList.remove('text-success', 'text-warning', 'text-error');
        descHelper.classList.add('text-mute');
        descHelper.classList.remove('text-success', 'text-warning', 'text-error');
        descHelper.innerText = "Minimal 20, maksimal 500 karakter.";
    }
});

form.addEventListener('submit', function(e) {
    let hasPhoto = true;
    let hasEmptyFields = false;
    let hasShortDescription = false;
    
    // Check Photo
    const previewContainer = document.getElementById('inline-preview-container');
    if (!document.getElementById('photo').files.length) {
        previewContainer.classList.add('border-error', 'bg-error-soft/10');
        previewContainer.classList.remove('border-hairline', 'bg-canvas-soft-2');
        hasPhoto = false;
    } else {
        previewContainer.classList.remove('border-error', 'bg-error-soft/10');
        previewContainer.classList.add('border-hairline', 'bg-canvas-soft-2');
    }

    // Check required fields
    const requiredElements = form.querySelectorAll('input[required], select[required], textarea[required]');
    requiredElements.forEach(el => {
        // Skip latitude and longitude as they are auto-filled
        if(el.id === 'latitude' || el.id === 'longitude') return;
        
        const val = el.value.trim();
        let isFieldValid = true;
        
        if (!val) {
            isFieldValid = false;
            hasEmptyFields = true;
        } else if (el.id === 'description') {
            const charCount = val.length;
            if (charCount < 20) {
                isFieldValid = false;
                hasShortDescription = true;
                descHelper.classList.remove('text-warning', 'text-success', 'text-mute');
                descHelper.classList.add('text-error');
                descHelper.innerText = `Deskripsi terlalu singkat (kurang ${20 - charCount} karakter).`;
            }
        }

        if (!isFieldValid) {
            el.classList.add('border-error', 'bg-error-soft/20', 'ring-1', 'ring-error');
        } else {
            el.classList.remove('border-error', 'bg-error-soft/20', 'ring-1', 'ring-error');
        }
    });

    if (hasShortDescription && !hasEmptyFields && hasPhoto) {
        e.preventDefault();
        alert('Deskripsi kondisi terlalu singkat. Mohon jelaskan minimal 20 karakter agar petugas dapat memahami kondisinya dengan baik.');
        descInput.focus();
        return false;
    } else if (!hasPhoto && !hasEmptyFields && !hasShortDescription) {
        e.preventDefault();
        alert('Mohon ambil foto sampah terlebih dahulu menggunakan Kamera Pintar sebelum mengirim laporan.');
        return false;
    } else if (hasEmptyFields || hasShortDescription || !hasPhoto) {
        e.preventDefault();
        alert('Data belum lengkap atau tidak memenuhi syarat! Mohon lengkapi bagian yang berwarna merah.');
        return false;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Init Map
    locationMap = L.map('location-map').setView([-6.32, 107.30], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM' }).addTo(locationMap);
    
    getLocation();
});

// Update Live Time
setInterval(() => {
    if(overlayTime) overlayTime.innerText = new Date().toLocaleString('id-ID');
}, 1000);

// Modal Controls
btnOpenCamera.addEventListener('click', openCameraModal);
btnRetakeInline.addEventListener('click', openCameraModal);
btnCloseCamera.addEventListener('click', closeCameraModal);

function openCameraModal() {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    // Ensure lucide icons in modal are initialized
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

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);
            
            const locText = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;
            document.getElementById('address').value = locText;
            overlayLocation.innerText = locText;
            
            if (locationMarker) locationMap.removeLayer(locationMarker);
            locationMarker = L.marker([lat, lng]).addTo(locationMap);
            locationMap.setView([lat, lng], 16);
        }, function() {
            alert('Gagal mendapatkan lokasi. Pastikan GPS aktif untuk melaporkan.');
            overlayLocation.innerText = "GPS tidak aktif";
        });
    }
}

btnCapture.addEventListener('click', function() {
    if (!videoStream) return;
    
    // Calculate max dimensions to prevent huge file sizes (PHP 2MB limit issue)
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
    
    // Set canvas to calculated resolution
    canvas.width = targetWidth;
    canvas.height = targetHeight;
    const ctx = canvas.getContext('2d');
    
    // Draw video frame (scaled to canvas)
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Dynamic sizes for watermark based on video resolution
    const isPortrait = canvas.height > canvas.width;
    const baseSize = isPortrait ? canvas.width : canvas.height;
    
    const panelHeight = Math.floor(baseSize * 0.18);
    const titleSize = Math.max(Math.floor(baseSize * 0.035), 18);
    const textSize = Math.max(Math.floor(baseSize * 0.025), 12);
    const margin = Math.max(Math.floor(baseSize * 0.04), 15);
    
    // Draw Watermark Panel
    ctx.fillStyle = "rgba(0, 0, 0, 0.65)";
    ctx.fillRect(0, canvas.height - panelHeight, canvas.width, panelHeight);
    
    // Draw Watermark Text
    ctx.fillStyle = "white";
    ctx.font = `bold ${titleSize}px 'Plus Jakarta Sans', Arial`;
    ctx.textAlign = "left";
    ctx.fillText("TrashReport Eco-Cam", margin, canvas.height - panelHeight + titleSize + (margin/2));
    
    ctx.font = `500 ${textSize}px 'Plus Jakarta Sans', Arial`;
    ctx.fillText("Pelapor: {{ auth()->user()->nama ?? auth()->user()->name }}", margin, canvas.height - panelHeight + titleSize + textSize + margin);
    
    const locText = overlayLocation.innerText;
    ctx.fillText("Lokasi: " + locText, margin, canvas.height - margin);
    
    const timeText = new Date().toLocaleString('id-ID');
    ctx.textAlign = "right";
    ctx.fillText(timeText, canvas.width - margin, canvas.height - margin);
    
    // Convert canvas to File object
    canvas.toBlob((blob) => {
        const file = new File([blob], "eco-cam-" + Date.now() + ".jpg", { type: "image/jpeg" });
        const container = new DataTransfer();
        container.items.add(file);
        document.getElementById('photo').files = container.files;
        
        // Update UI
        canvas.classList.remove('hidden');
        inlinePlaceholder.classList.add('hidden');
        btnRetakeInline.classList.remove('hidden');
        
        // Close modal
        closeCameraModal();
    }, 'image/jpeg', 0.85);
});
</script>
@endpush
@endsection

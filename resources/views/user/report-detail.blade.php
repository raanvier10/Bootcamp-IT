@extends('layouts.user')
@section('title', 'Detail Laporan')
@section('page_title', 'Detail Laporan')
@section('content')
<div class="max-w-4xl">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-mute mb-6">
        <a href="{{ route('user.reports') }}" class="hover:text-primary transition-colors">Riwayat Laporan</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-ink font-medium">{{ $report->report_code }}</span>
    </div>

    {{-- Report Header --}}
    <div class="bg-canvas rounded-xl shadow-card-lg border border-hairline p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm text-mute font-medium">{{ $report->report_code }}</span>
                    <span class="{{ $report->kelas_badge_status }}">{{ $report->label_status }}</span>
                </div>
                <h2 class="text-xl font-semibold text-ink tracking-tight">{{ $report->judul }}</h2>
            </div>
            @if($report->status === 'Ditolak')
                <div class="bg-error-soft rounded-lg p-3 max-w-sm">
                    <p class="text-xs font-medium text-error mb-1">Alasan Penolakan:</p>
                    <p class="text-sm text-error">{{ $report->alasan_penolakan ?? '-' }}</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><p class="text-mute mb-1">Kategori</p><p class="text-ink font-medium">{{ $report->kategori->nama ?? '-' }}</p></div>
            <div><p class="text-mute mb-1">Wilayah</p><p class="text-ink font-medium">{{ $report->wilayah->nama ?? '-' }}</p></div>
            <div><p class="text-mute mb-1">Tanggal Lapor</p><p class="text-ink font-medium">{{ $report->dilaporkan_pada->format('d M Y, H:i') }}</p></div>
            <div><p class="text-mute mb-1">Prioritas</p><p class="text-ink font-medium">{{ $report->label_prioritas }}</p></div>
        </div>
    </div>

    {{-- Status Tracker Premium (Vertical for Mobile Perfection) --}}
    @if($report->status !== 'Ditolak')
    <div class="bg-canvas rounded-3xl shadow-card-md border border-hairline p-5 sm:p-8 mb-8 w-full overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-8">
            <h3 class="text-lg font-bold text-ink tracking-tight">Perjalanan Laporan</h3>
            <span class="px-3 py-1 bg-primary-soft text-primary text-xs font-bold rounded-full border border-primary/20 self-start sm:self-auto">Status: {{ $report->label_status }}</span>
        </div>
        
        @php
            $allStatuses = array_keys($statusSteps);
            $currentIdx = array_search($report->status, $allStatuses);
            if ($currentIdx === false) $currentIdx = 0;
        @endphp
        
        <div class="relative w-full pl-2 pb-2">
            @foreach($statusSteps as $key => $label)
                @php
                    $idx = array_search($key, $allStatuses);
                    $isDone = $idx < $currentIdx;
                    $isCurrent = $idx === $currentIdx;
                @endphp
                
                {{-- Step Item --}}
                <div class="relative flex items-start gap-4 {{ !$loop->last ? 'mb-6' : '' }} group">
                    
                    {{-- Connecting Line (Vertical) --}}
                    @if(!$loop->last)
                        <div class="absolute top-8 left-[1.15rem] w-0.5 h-full {{ $isDone ? 'bg-primary' : 'bg-hairline' }} z-0"></div>
                    @endif
                    
                    {{-- Circle --}}
                    <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center shrink-0 border-[3px] transition-all duration-500 {{ $isDone ? 'bg-primary border-primary text-white shadow-md' : ($isCurrent ? 'bg-canvas border-primary text-primary shadow-md scale-110' : 'bg-canvas border-hairline text-mute') }}">
                        @if($isDone)
                            <i data-lucide="check" class="w-5 h-5"></i>
                        @elseif($isCurrent)
                            <div class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></div>
                        @else
                            <span class="text-sm font-bold">{{ $idx + 1 }}</span>
                        @endif
                    </div>
                    
                    {{-- Label --}}
                    <div class="flex-1 pt-2">
                        <span class="block text-sm font-bold {{ $isDone || $isCurrent ? 'text-ink' : 'text-mute' }} leading-tight">{{ $label }}</span>
                        @if($isCurrent)
                            <span class="block text-xs text-primary font-bold mt-1">Posisi Saat Ini</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Description --}}
        <div class="bg-canvas rounded-xl shadow-card-sm border border-hairline p-6">
            <h3 class="text-base font-semibold text-ink mb-3">Deskripsi</h3>
            <p class="text-sm text-body leading-relaxed">{{ $report->description }}</p>
        </div>

        {{-- Location --}}
        <div class="bg-canvas rounded-xl shadow-card-sm border border-hairline p-6">
            <h3 class="text-base font-semibold text-ink mb-3">Lokasi</h3>
            <div id="detail-map" class="relative z-10 w-full h-40 rounded-lg mb-3"></div>
            <p class="text-xs text-mute">{{ $report->address }}</p>
            <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="text-xs text-primary font-medium hover:underline mt-2 inline-flex items-center gap-1">
                <i data-lucide="external-link" class="w-3 h-3"></i>Buka di Google Maps
            </a>
        </div>
    </div>

    {{-- Before/After Photos --}}
    <div class="bg-canvas rounded-xl shadow-card-lg border border-hairline p-6 mb-6">
        <h3 class="text-base font-semibold text-ink mb-4">Foto Bukti</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-medium text-mute mb-2 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-error"></span>Sebelum</p>
                @if($report->beforeImages->count() > 0)
                    <img src="{{ asset('storage/' . $report->beforeImages->first()->image_path) }}" class="w-full h-48 object-cover rounded-lg shadow-card-sm" alt="Before">
                @else
                    <div class="w-full h-48 bg-canvas-soft-2 rounded-lg flex items-center justify-center"><p class="text-sm text-mute">Foto tersedia</p></div>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-mute mb-2 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-success"></span>Sesudah</p>
                @if($report->afterImages->count() > 0)
                    <img src="{{ asset('storage/' . $report->afterImages->first()->image_path) }}" class="w-full h-48 object-cover rounded-lg shadow-card-sm" alt="After">
                @else
                    <div class="w-full h-48 bg-canvas-soft-2 rounded-lg flex items-center justify-center"><p class="text-sm text-mute">Belum tersedia</p></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Confirm & Feedback --}}
    @if($report->status === 'Menunggu Konfirmasi')
    <div class="bg-primary-soft rounded-xl border border-primary/20 p-6 mb-6">
        <h3 class="text-base font-semibold text-primary mb-2">Konfirmasi Penyelesaian</h3>
        <p class="text-sm text-body mb-4">Petugas telah menyelesaikan pembersihan. Apakah lokasi sudah benar-benar bersih?</p>
        <form method="POST" action="{{ route('user.report.confirm', $report->id) }}">
            @csrf
            <button type="submit" class="btn-primary" id="btn-confirm-report"><i data-lucide="check-circle" class="w-4 h-4"></i>Ya, Konfirmasi Selesai</button>
        </form>
    </div>
    @endif

    @if(in_array($report->status, ['Ditutup', 'Selesai']) && !$report->ulasan)
    <div class="bg-canvas rounded-xl shadow-card-lg border border-hairline p-6 mb-6">
        <h3 class="text-base font-semibold text-ink mb-4">Beri Rating & Feedback</h3>
        <form method="POST" action="{{ route('user.report.feedback', $report->id) }}" class="space-y-4" id="feedback-form">
            @csrf
            <div>
                <label class="form-label">Rating</label>
                <div class="flex gap-1" id="star-rating">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" onclick="setRating({{ $i }})" class="p-1 text-hairline hover:text-warning transition-colors star-btn" data-value="{{ $i }}">
                        <i data-lucide="star" class="w-7 h-7"></i>
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-value" value="5" required>
            </div>
            <div>
                <label for="comment" class="form-label">Komentar (opsional)</label>
                <textarea name="comment" id="comment" placeholder="Bagaimana pengalaman Anda?" class="form-textarea" rows="3"></textarea>
            </div>
            <button type="submit" class="btn-primary-sm" id="btn-submit-feedback"><i data-lucide="send" class="w-4 h-4"></i>Kirim Feedback</button>
        </form>
    </div>
    @endif

    @if($report->feedback)
    <div class="bg-canvas rounded-xl shadow-card-sm border border-hairline p-6 mb-6">
        <h3 class="text-base font-semibold text-ink mb-3">Feedback Anda</h3>
        <div class="flex items-center gap-1 mb-2">
            @for($i = 1; $i <= 5; $i++)
                <i data-lucide="star" class="w-5 h-5 {{ $i <= $report->feedback->rating ? 'text-warning fill-warning' : 'text-hairline' }}"></i>
            @endfor
            <span class="text-sm text-mute ml-2">{{ $report->feedback->rating }}/5</span>
        </div>
        @if($report->feedback->comment)
            <p class="text-sm text-body">{{ $report->feedback->comment }}</p>
        @endif
    </div>
    @endif

    {{-- Status History --}}
    <div class="bg-canvas rounded-xl shadow-card-sm border border-hairline p-6">
        <h3 class="text-base font-semibold text-ink mb-4">Riwayat Status</h3>
        <div class="space-y-3">
            @foreach($report->statusHistories as $history)
            <div class="flex items-start gap-3 text-sm">
                <div class="w-2 h-2 rounded-full bg-primary mt-2 flex-shrink-0"></div>
                <div>
                    <p class="font-medium text-ink">{{ $history->status }}</p>
                    @if($history->note)<p class="text-body">{{ $history->note }}</p>@endif
                    <p class="text-xs text-mute mt-0.5">{{ $history->created_at->format('d M Y, H:i') }} — {{ $history->changedByUser->name ?? '-' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Leaflet Map Initialization
    const map = L.map('detail-map', { scrollWheelZoom: false, zoomControl: false }).setView([{{ $report->latitude }}, {{ $report->longitude }}], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map);

    @if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#0D530E',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'font-bold px-6 py-2.5 rounded-xl'
        }
    });
    @endif
});

function setRating(value) {
    document.getElementById('rating-value').value = value;
    document.querySelectorAll('.star-btn').forEach((btn, i) => {
        btn.classList.toggle('text-warning', i < value);
        btn.classList.toggle('text-hairline', i >= value);
    });
}
</script>
@endpush
@endsection

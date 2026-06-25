@extends('layouts.officer')
@section('title', 'Daftar Tugas')
@section('page_title', 'Daftar Tugas')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 animate-fade-in-up">

    {{-- Header Action --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1">
            <h2 class="text-xl font-bold text-ink tracking-tight mb-1">Daftar Penugasan</h2>
            <p class="text-sm text-mute font-medium">Kelola dan eksekusi laporan kebersihan yang ditugaskan ke Anda.</p>
        </div>
    </div>

    {{-- Filters (Dropdown) --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-canvas p-3 rounded-2xl border border-hairline shadow-sm">
        <div class="flex items-center gap-2 pl-1">
            <div class="w-8 h-8 rounded-full bg-primary-soft flex items-center justify-center">
                <i data-lucide="filter" class="w-4 h-4 text-primary"></i>
            </div>
            <span class="text-sm font-bold text-ink">Filter Status</span>
        </div>
        
        <div class="relative w-full sm:w-64">
            <select onchange="window.location.href=this.value" class="w-full bg-canvas-soft border border-hairline text-ink text-sm font-bold rounded-xl block p-3 pr-10 appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-inner cursor-pointer transition-all">
                <option value="{{ route('officer.tasks') }}" {{ !request('status') ? 'selected' : '' }}>Semua Tugas</option>
                <option value="{{ route('officer.tasks', ['status' => 'Baru']) }}" {{ request('status') == 'Baru' ? 'selected' : '' }}>Tugas Baru</option>
                <option value="{{ route('officer.tasks', ['status' => 'Diproses']) }}" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                <option value="{{ route('officer.tasks', ['status' => 'Selesai']) }}" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Tugas Selesai</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                <i data-lucide="chevron-down" class="w-4 h-4 text-mute"></i>
            </div>
        </div>
    </div>

    {{-- Tasks Grid --}}
    @if($tasks->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($tasks as $tugas)
        <a href="{{ route('officer.tasks.show', $tugas->id) }}" class="group flex bg-canvas rounded-2xl border border-hairline overflow-hidden hover:shadow-card-md hover:border-primary/40 transition-all duration-300">
            
            {{-- Thumbnail Area (Left) --}}
            <div class="relative w-32 sm:w-40 shrink-0 bg-canvas-soft overflow-hidden">
                @if($tugas->gambarSebelum && $tugas->gambarSebelum->count() > 0)
                    <img src="{{ Storage::url($tugas->gambarSebelum->first()->jalur_gambar) }}" alt="Foto Sampah" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-mute bg-canvas-soft-2">
                        <i data-lucide="image" class="w-8 h-8 opacity-30 mb-1"></i>
                    </div>
                @endif
                
                {{-- Status Badge (Floating Top Left) --}}
                <div class="absolute top-2 left-2">
                    <span class="{{ $tugas->kelas_badge_status }} scale-75 origin-top-left shadow-sm backdrop-blur-md">{{ $tugas->label_status }}</span>
                </div>
            </div>

            {{-- Content Area (Right) --}}
            <div class="p-4 sm:p-5 flex-1 flex flex-col min-w-0">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <span class="text-[10px] font-bold text-mute uppercase tracking-wider border border-hairline px-2 py-0.5 rounded-md">{{ $tugas->kode_laporan }}</span>
                    @if($tugas->prioritas == 'Tinggi' || $tugas->prioritas == 'Mendesak')
                        <span class="text-error text-[10px] font-black uppercase tracking-wider flex items-center gap-1"><i data-lucide="flame" class="w-3 h-3"></i> {{ $tugas->prioritas }}</span>
                    @else
                        <span class="text-info text-[10px] font-black uppercase tracking-wider flex items-center gap-1"><i data-lucide="flag" class="w-3 h-3"></i> {{ $tugas->prioritas }}</span>
                    @endif
                </div>
                
                <h3 class="text-sm sm:text-base font-bold text-ink mb-3 group-hover:text-primary transition-colors line-clamp-2 leading-snug">{{ $tugas->judul }}</h3>
                
                <div class="mt-auto flex flex-col gap-1.5 pt-2">
                    <div class="flex items-center gap-2 text-xs text-body">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-mute shrink-0"></i>
                        <span class="truncate font-medium">{{ $tugas->alamat }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-2 text-xs text-body">
                        <div class="flex items-center gap-2 min-w-0">
                            <i data-lucide="tag" class="w-3.5 h-3.5 text-mute shrink-0"></i>
                            <span class="truncate font-medium">{{ $tugas->kategori->nama ?? '-' }}</span>
                        </div>
                        <span class="text-[10px] font-bold text-mute shrink-0"><i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>{{ $tugas->ditugaskan_pada->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8 flex justify-center">
        {{ $tasks->withQueryString()->links() }}
    </div>

    @else
    {{-- Empty State --}}
    <div class="bg-canvas rounded-3xl border border-hairline p-12 sm:p-20 text-center shadow-sm max-w-3xl mx-auto flex flex-col items-center justify-center min-h-[50vh]">
        <div class="w-24 h-24 bg-primary-soft rounded-full flex items-center justify-center mb-6 relative">
            <div class="absolute inset-0 bg-primary/20 rounded-full animate-ping opacity-20"></div>
            <i data-lucide="clipboard-check" class="w-10 h-10 text-primary"></i>
        </div>
        <h3 class="text-2xl font-black text-ink mb-3 tracking-tight">Tidak Ada Tugas</h3>
        <p class="text-body mb-8 max-w-md mx-auto text-base">Saat ini tidak ada laporan sampah yang perlu Anda bersihkan di filter ini.</p>
    </div>
    @endif
</div>
@endsection

@extends('layouts.officer')
@section('title', 'Dashboard Petugas')
@section('page_title', 'Dashboard Petugas')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-primary to-[#094009] rounded-[24px] shadow-card-lg p-8 relative overflow-hidden animate-fade-in-up">
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none mix-blend-overlay"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none mix-blend-overlay"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight mb-2">Siap Bertugas, {{ auth()->user()->nama }}?</h1>
                <p class="text-white/80 font-medium max-w-xl text-sm leading-relaxed">Pantau penugasan baru dan kelola jadwal pembersihanmu hari ini. Setiap tumpukan sampah yang dibersihkan membawa senyum bagi masyarakat.</p>
            </div>
            <div class="shrink-0 hidden md:block">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner">
                    <i data-lucide="shield-check" class="w-10 h-10 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Grid (Compact/Slim Layout for Mobile) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Tugas Baru -->
        <div class="relative bg-canvas rounded-2xl p-4 border border-hairline shadow-sm hover:shadow-card-md hover:border-error/30 transition-all duration-300 flex items-center justify-between group overflow-hidden animate-fade-in-up stagger-1">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-error-soft rounded-full blur-xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
            
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 bg-error-soft text-error rounded-xl flex items-center justify-center border border-error/10">
                    <i data-lucide="bell-ring" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-baseline gap-1.5 mb-1">
                        <h3 class="text-2xl font-black text-ink leading-none tracking-tight">{{ $tugasBaru }}</h3>
                        <span class="text-[10px] font-bold text-mute uppercase tracking-widest">Tugas</span>
                    </div>
                    <p class="text-[11px] font-bold text-mute uppercase tracking-widest leading-none">Menunggu Eksekusi</p>
                </div>
            </div>

            @if($tugasBaru > 0)
            <div class="relative z-10 shrink-0 mr-2" title="Wajib Tindakan">
                <div class="w-3 h-3 bg-error rounded-full animate-ping absolute inset-0 opacity-75"></div>
                <div class="w-3 h-3 bg-error rounded-full relative z-10"></div>
            </div>
            @endif
        </div>

        <!-- Sedang Dikerjakan -->
        <div class="relative bg-canvas rounded-2xl p-4 border border-hairline shadow-sm hover:shadow-card-md hover:border-warning/30 transition-all duration-300 flex items-center justify-between group overflow-hidden animate-fade-in-up stagger-2">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-warning-soft rounded-full blur-xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
            
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 bg-warning-soft text-warning rounded-xl flex items-center justify-center border border-warning/10">
                    <i data-lucide="loader" class="w-5 h-5 animate-[spin_3s_linear_infinite]"></i>
                </div>
                <div>
                    <div class="flex items-baseline gap-1.5 mb-1">
                        <h3 class="text-2xl font-black text-ink leading-none tracking-tight">{{ $sedangDikerjakan }}</h3>
                        <span class="text-[10px] font-bold text-mute uppercase tracking-widest">Tugas</span>
                    </div>
                    <p class="text-[11px] font-bold text-mute uppercase tracking-widest leading-none">Sedang Diproses</p>
                </div>
            </div>
        </div>

        <!-- Tugas Selesai -->
        <div class="relative bg-canvas rounded-2xl p-4 border border-hairline shadow-sm hover:shadow-card-md hover:border-primary/30 transition-all duration-300 flex items-center justify-between group overflow-hidden animate-fade-in-up stagger-3">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary-soft rounded-full blur-xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
            
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 bg-primary-soft text-primary rounded-xl flex items-center justify-center border border-primary/10">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-baseline gap-1.5 mb-1">
                        <h3 class="text-2xl font-black text-ink leading-none tracking-tight">{{ $tugasSelesai }}</h3>
                        <span class="text-[10px] font-bold text-mute uppercase tracking-widest">Tugas</span>
                    </div>
                    <p class="text-[11px] font-bold text-mute uppercase tracking-widest leading-none">Total Diselesaikan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Tasks --}}
    <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden animate-fade-in-up stagger-4">
        <div class="p-6 border-b border-hairline flex items-center justify-between bg-canvas-soft/50">
            <div>
                <h2 class="text-lg font-black text-ink tracking-tight">Tugas Terbaru</h2>
                <p class="text-sm font-medium text-mute mt-1">Daftar penugasan terakhir yang diamanatkan ke Anda.</p>
            </div>
            <a href="{{ route('officer.tasks') }}" class="text-sm font-bold text-primary hover:text-primary-dark flex items-center gap-1">
                Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        
        <div class="p-0">
            @if($tugasTerbaru->count() > 0)
                <div class="divide-y divide-hairline">
                    @foreach($tugasTerbaru as $tugas)
                    <a href="{{ route('officer.tasks.show', $tugas->id) }}" class="block p-6 hover:bg-canvas-soft transition-colors group">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-canvas-soft-2 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-primary-soft transition-all duration-300">
                                    <i data-lucide="map-pin" class="w-5 h-5 text-mute group-hover:text-primary transition-colors"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-ink group-hover:text-primary transition-colors mb-1">
                                        {{ $tugas->judul }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-mute">
                                        <span class="border border-hairline bg-white px-2 py-0.5 rounded-md text-[10px] uppercase tracking-wider font-bold">
                                            {{ $tugas->kode_laporan }}
                                        </span>
                                        <span class="w-1 h-1 bg-mute rounded-full"></span>
                                        <span><i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>{{ $tugas->ditugaskan_pada->format('d M Y, H:i') }}</span>
                                        <span class="w-1 h-1 bg-mute rounded-full"></span>
                                        <span>{{ $tugas->wilayah->nama ?? 'Wilayah Tidak Diketahui' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center gap-3 sm:flex-col sm:items-end">
                                <span class="{{ $tugas->kelas_badge_status }} px-3 py-1 text-xs font-bold rounded-full border shadow-sm">
                                    {{ $tugas->label_status }}
                                </span>
                                @if($tugas->prioritas == 'Tinggi' || $tugas->prioritas == 'Mendesak')
                                    <span class="text-xs font-bold text-error flex items-center gap-1">
                                        <i data-lucide="flame" class="w-3 h-3"></i> {{ $tugas->prioritas }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center flex flex-col items-center">
                    <div class="w-20 h-20 bg-canvas-soft-2 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="coffee" class="w-8 h-8 text-mute"></i>
                    </div>
                    <h3 class="text-lg font-bold text-ink mb-2">Belum Ada Tugas</h3>
                    <p class="text-sm text-mute max-w-sm">Anda bisa bersantai sejenak. Saat ini tidak ada laporan yang ditugaskan ke Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

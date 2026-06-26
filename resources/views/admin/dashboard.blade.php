@extends('layouts.admin')

@section('page_title', 'Dashboard Admin')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-primary to-[#094009] rounded-[24px] shadow-card-lg p-8 relative overflow-hidden animate-fade-in-up">
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none mix-blend-overlay"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none mix-blend-overlay"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight mb-2">Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}!</h1>
                <p class="text-white/80 font-medium max-w-xl text-sm leading-relaxed">Pantau metrik utama TrashReport, kelola laporan masuk, dan koordinasikan petugas lapangan untuk menjaga kebersihan kota.</p>
            </div>
            <div class="shrink-0 hidden md:block">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner">
                    <i data-lucide="layout-dashboard" class="w-10 h-10 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up stagger-1">
        <div class="bg-canvas rounded-[20px] p-5 border border-hairline shadow-sm flex items-center gap-4 group hover:shadow-card-md transition-all duration-300 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary-soft rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="w-12 h-12 relative z-10 shrink-0 rounded-xl bg-primary-soft text-primary flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <i data-lucide="file-text" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-0.5">Total Laporan</p>
            <p class="text-2xl font-black text-ink leading-none tracking-tight">{{ number_format($totalLaporan) }}</p>
        </div>
    </div>
    
    <div class="bg-canvas rounded-[20px] p-5 border border-hairline shadow-sm flex items-center gap-4 group hover:shadow-card-md transition-all duration-300 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-warning-soft rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-12 h-12 relative z-10 shrink-0 rounded-xl bg-warning-soft text-warning flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <i data-lucide="clock" class="w-6 h-6"></i>
        </div>
        <div class="relative z-10">
            <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-0.5">Menunggu Verifikasi</p>
            <p class="text-2xl font-black text-ink leading-none tracking-tight">{{ number_format($perluVerifikasi) }}</p>
        </div>
    </div>
    
    <div class="bg-canvas rounded-[20px] p-5 border border-hairline shadow-sm flex items-center gap-4 group hover:shadow-card-md transition-all duration-300 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-info-soft rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-12 h-12 relative z-10 shrink-0 rounded-xl bg-info-soft text-info flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <i data-lucide="users" class="w-6 h-6"></i>
        </div>
        <div class="relative z-10">
            <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-0.5">Petugas Aktif</p>
            <p class="text-2xl font-black text-ink leading-none tracking-tight">{{ number_format($petugasAktif) }}</p>
        </div>
    </div>
    
    <div class="bg-canvas rounded-[20px] p-5 border border-hairline shadow-sm flex items-center gap-4 group hover:shadow-card-md transition-all duration-300 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-success-soft rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-12 h-12 relative z-10 shrink-0 rounded-xl bg-success-soft text-success flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
        </div>
        <div class="relative z-10">
            <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-0.5">Laporan Selesai</p>
            <p class="text-2xl font-black text-ink leading-none tracking-tight">{{ number_format($laporanSelesai) }}</p>
        </div>
    </div>
</div>
    
    {{-- FR-AD-12 Alarm TPS Liar Predictor --}}
    @if(isset($tpsLiarAlarms) && $tpsLiarAlarms->count() > 0)
    <div class="bg-error-soft border border-error/30 rounded-[24px] p-6 shadow-sm animate-fade-in-up stagger-1 flex flex-col gap-4 relative overflow-hidden">
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-64 h-64 bg-error/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="flex items-start gap-4 relative z-10">
            <div class="w-12 h-12 rounded-full bg-error text-white flex items-center justify-center shrink-0 shadow-inner">
                <i data-lucide="siren" class="w-6 h-6 animate-pulse"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-error-dark mb-1 flex items-center gap-2">
                    Alarm Prediksi TPS Liar Baru
                    <span class="bg-error text-white text-[10px] font-black uppercase px-2 py-0.5 rounded-full tracking-widest">{{ $tpsLiarAlarms->count() }} Titik Rawan</span>
                </h2>
                <p class="text-sm text-error-dark/80 mb-4 max-w-3xl">
                    Sistem mendeteksi ada area yang terus-menerus dilaporkan kotor (lebih dari 3 kali dalam sebulan terakhir). Area ini direkomendasikan untuk tindakan pencegahan permanen seperti pemasangan spanduk larangan atau tong sampah baru.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($tpsLiarAlarms as $alarm)
                    <div class="bg-white/80 backdrop-blur-md rounded-[16px] p-4 border border-error/20 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-error shrink-0"></i>
                            <p class="text-sm font-bold text-ink truncate">{{ $alarm->center_report->alamat }}</p>
                        </div>
                        <p class="text-[11px] text-mute mb-3">{{ $alarm->center_report->wilayah?->nama ?? 'Tidak diketahui' }}</p>
                        
                        <div class="flex items-center justify-between bg-canvas-soft rounded-lg p-2.5 border border-hairline">
                            <span class="text-xs font-semibold text-body">Tingkat Kerawanan:</span>
                            <span class="text-xs font-black text-error">{{ $alarm->count }} Laporan / Bln</span>
                        </div>
                        
                        <a href="https://maps.google.com/?q={{ $alarm->center_report->lintang }},{{ $alarm->center_report->bujur }}" target="_blank" class="mt-3 btn-secondary-sm w-full justify-center bg-white border-error/20 text-error hover:bg-error-soft transition-colors">
                            <i data-lucide="map" class="w-4 h-4"></i> Lihat Titik di Peta
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

<div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden animate-fade-in-up stagger-2">
    <div class="px-6 py-5 border-b border-hairline bg-canvas-soft flex items-center justify-between">
        <h2 class="text-lg font-bold text-ink tracking-tight">Menunggu Verifikasi</h2>
        <a href="{{ route('admin.reports') }}" class="text-[11px] font-bold text-primary uppercase tracking-widest hover:text-primary-dark transition-colors inline-flex items-center gap-1">
            Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-canvas text-mute text-[10px] uppercase tracking-widest border-b border-hairline">
                    <th class="px-6 py-4 font-bold">Laporan</th>
                    <th class="px-6 py-4 font-bold">Pelapor</th>
                    <th class="px-6 py-4 font-bold">Lokasi</th>
                    <th class="px-6 py-4 font-bold">Status</th>
                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($laporanTerbaru as $laporan)
                <tr class="hover:bg-canvas-soft transition-colors group">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-ink">{{ $laporan->kode_laporan }}</p>
                        <p class="text-[11px] text-mute mt-0.5">{{ $laporan->dilaporkan_pada->format('d M Y, H:i') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $laporan->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($laporan->user?->name ?? 'User').'&background=000&color=fff' }}" alt="{{ $laporan->user?->name ?? 'User' }}" class="w-7 h-7 rounded-full object-cover">
                            <span class="text-sm font-medium text-body">{{ $laporan->user?->name ?? 'User' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-mute shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-body line-clamp-1">{{ $laporan->alamat }}</p>
                                <p class="text-[11px] text-mute">{{ $laporan->wilayah?->nama ?? 'Tidak diketahui' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-warning-soft text-warning border border-warning/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-warning animate-pulse"></span>
                            {{ $laporan->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.reports') }}" class="btn-primary-sm inline-flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Tinjau
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-canvas-soft-2 rounded-full flex items-center justify-center mb-3">
                                <i data-lucide="check-circle-2" class="w-6 h-6 text-success"></i>
                            </div>
                            <p class="text-sm font-bold text-ink">Semua Laporan Terverifikasi</p>
                            <p class="text-xs text-mute mt-1">Tidak ada laporan baru yang menunggu verifikasi saat ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection


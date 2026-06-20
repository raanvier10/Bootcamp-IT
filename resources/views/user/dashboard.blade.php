@extends('layouts.user')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('content')

{{-- Welcome Banner --}}
<div class="relative overflow-hidden rounded-[24px] shadow-card-lg mb-8 animate-fade-in-up border border-primary/20 group">
    <!-- Background Image -->
    <img src="{{ asset('images/dashboard-bg-2.png') }}" alt="Nature Background" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000 ease-out">
    
    <!-- Dark Green Overlay (Mix Blend Multiply + Solid Gradient) -->
    <div class="absolute inset-0 bg-gradient-to-r from-primary to-[#094009] mix-blend-multiply opacity-80"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-[#0D530E]/95 via-[#0D530E]/80 to-transparent"></div>
    
    <!-- Decorative blobs -->
    <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none mix-blend-overlay"></div>
    <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none mix-blend-overlay"></div>
    
    <div class="relative z-10 p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3 tracking-tight">Halo, {{ auth()->user()->nama ?? 'Sobat Bumi' }}!</h1>
            <p class="text-white/90 max-w-xl text-sm md:text-base leading-relaxed font-medium">Terima kasih telah berkontribusi menjaga kebersihan lingkungan. Laporkan titik tumpukan sampah liar di sekitar Anda, biar petugas kami yang tangani.</p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('user.report.create') }}" class="inline-flex items-center gap-2 bg-white text-primary hover:bg-canvas-soft font-bold py-3.5 px-7 rounded-full shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 active:translate-y-0">
                <i data-lucide="camera" class="w-5 h-5"></i>
                Lapor Sekarang
            </a>
        </div>
    </div>
</div>

{{-- Bento Grid Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Left Column (Stats & Recent) --}}
    <div class="lg:col-span-8 space-y-6">
        
        {{-- Stats Bento --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4">
            <!-- Total Card (Spans full width on mobile) -->
            <div class="col-span-2 md:col-span-1 bg-gradient-to-br from-primary to-[#094009] p-5 md:p-5 rounded-[20px] shadow-md animate-fade-in-up stagger-1 group text-white relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-500 pointer-events-none">
                    <i data-lucide="file-text" class="w-24 h-24"></i>
                </div>
                <div class="relative z-10 flex items-center gap-2 mb-2 md:mb-3 text-white/90 group-hover:text-white transition-colors">
                    <i data-lucide="file-text" class="w-4.5 h-4.5"></i>
                    <p class="text-[11px] font-bold uppercase tracking-wider">Total Laporan</p>
                </div>
                <p class="relative z-10 text-4xl md:text-3xl font-black">{{ $stats['total'] }}</p>
            </div>
            
            <!-- Pending Card -->
            <div class="bg-canvas p-4 md:p-5 rounded-[20px] border border-hairline shadow-sm hover:shadow-md transition-shadow animate-fade-in-up stagger-2 group">
                <div class="flex items-center gap-2 mb-2 md:mb-3 text-warning group-hover:text-warning-dark transition-colors">
                    <i data-lucide="clock" class="w-4.5 h-4.5"></i>
                    <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-wider">Menunggu</p>
                </div>
                <p class="text-2xl md:text-3xl font-black text-ink">{{ $stats['pending'] }}</p>
            </div>
            
            <!-- Processing Card -->
            <div class="bg-canvas p-4 md:p-5 rounded-[20px] border border-hairline shadow-sm hover:shadow-md transition-shadow animate-fade-in-up stagger-3 group">
                <div class="flex items-center gap-2 mb-2 md:mb-3 text-info group-hover:text-info-dark transition-colors">
                    <i data-lucide="loader" class="w-4.5 h-4.5"></i>
                    <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-wider">Diproses</p>
                </div>
                <p class="text-2xl md:text-3xl font-black text-ink">{{ $stats['processing'] }}</p>
            </div>
            
            <!-- Completed Card -->
            <div class="bg-canvas p-4 md:p-5 rounded-[20px] border border-hairline shadow-sm hover:shadow-md transition-shadow animate-fade-in-up stagger-4 group">
                <div class="flex items-center gap-2 mb-2 md:mb-3 text-success group-hover:text-success-dark transition-colors">
                    <i data-lucide="check-circle" class="w-4.5 h-4.5"></i>
                    <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-wider">Selesai</p>
                </div>
                <p class="text-2xl md:text-3xl font-black text-ink">{{ $stats['completed'] }}</p>
            </div>

            <!-- Rejected Card -->
            <div class="bg-canvas p-4 md:p-5 rounded-[20px] border border-hairline shadow-sm hover:shadow-md transition-shadow animate-fade-in-up stagger-5 group">
                <div class="flex items-center gap-2 mb-2 md:mb-3 text-error group-hover:text-error-dark transition-colors">
                    <i data-lucide="x-circle" class="w-4.5 h-4.5"></i>
                    <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-wider">Ditolak</p>
                </div>
                <p class="text-2xl md:text-3xl font-black text-ink">{{ $stats['rejected'] }}</p>
            </div>
        </div>

        {{-- Recent Reports --}}
        <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden animate-fade-in-up stagger-6">
            <div class="p-6 border-b border-hairline flex items-center justify-between bg-canvas/50 backdrop-blur-sm">
                <div>
                    <h2 class="text-lg font-extrabold text-ink tracking-tight">Riwayat Terbaru</h2>
                    <p class="text-sm text-mute mt-1 font-medium">Pantau status laporan terakhir Anda</p>
                </div>
                <a href="{{ route('user.reports') }}" class="text-sm font-bold text-primary hover:text-primary-dark flex items-center gap-1 hover:gap-2 transition-all">
                    Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
            
            @if($recentReports->count() > 0)
                <div class="p-3 space-y-2">
                    @foreach($recentReports as $report)
                    <a href="{{ route('user.report.detail', $report->id) }}" class="group block p-4 rounded-[16px] hover:bg-canvas-soft border border-transparent hover:border-hairline transition-all hover:shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-canvas-soft-2 flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-primary-soft transition-all duration-300">
                                    <i data-lucide="file-text" class="w-5 h-5 text-mute group-hover:text-primary transition-colors"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-ink group-hover:text-primary transition-colors">{{ $report->judul ?? $report->title }}</h3>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-mute font-medium">
                                        <span class="bg-canvas-soft-2 px-2 py-0.5 rounded-md text-[10px] uppercase tracking-wider font-bold">{{ $report->kode_laporan ?? $report->report_code }}</span>
                                        <span class="w-1 h-1 bg-mute rounded-full"></span>
                                        <span><i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="{{ $report->kelas_badge_status ?? 'badge-neutral' }} px-3 py-1.5 text-xs font-bold rounded-full border shadow-sm tracking-wide">
                                    {{ $report->label_status ?? $report->status }}
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center flex flex-col items-center">
                    <div class="w-24 h-24 bg-canvas-soft-2 rounded-full flex items-center justify-center mb-5">
                        <i data-lucide="leaf" class="w-10 h-10 text-mute"></i>
                    </div>
                    <h3 class="text-lg font-bold text-ink mb-2 tracking-tight">Belum Ada Laporan</h3>
                    <p class="text-sm text-mute max-w-sm mb-7 leading-relaxed">Mulai laporkan tumpukan sampah liar yang mengganggu di lingkungan Anda. Bersama kita wujudkan kota yang bersih.</p>
                    <a href="{{ route('user.report.create') }}" class="btn-primary rounded-full shadow-lg shadow-primary/30 font-bold px-6">
                        <i data-lucide="camera" class="w-4 h-4"></i> Buat Laporan Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Right Column (Quick Actions & Notifications) --}}
    <div class="lg:col-span-4 space-y-6">
        
        {{-- Quick Actions --}}
        <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg p-6 animate-fade-in-up stagger-2">
            <h2 class="text-base font-extrabold text-ink tracking-tight mb-5 flex items-center gap-2">
                <i data-lucide="zap" class="w-5 h-5 text-warning fill-warning/20"></i> Aksi Cepat
            </h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('user.report.create') }}" class="flex flex-col items-center justify-center gap-3 p-5 rounded-[18px] border border-primary/20 bg-primary-soft hover:bg-primary text-primary hover:text-white transition-all group shadow-sm hover:shadow-md">
                    <i data-lucide="camera" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold text-center tracking-wide">Buat Laporan</span>
                </a>
                <a href="{{ route('user.reports') }}" class="flex flex-col items-center justify-center gap-3 p-5 rounded-[18px] border border-hairline hover:bg-canvas-soft-2 hover:border-mute/30 text-ink transition-all group shadow-sm hover:shadow-md">
                    <i data-lucide="list" class="w-6 h-6 text-mute group-hover:scale-110 transition-transform group-hover:text-ink"></i>
                    <span class="text-xs font-bold text-center tracking-wide">Riwayat Laporan</span>
                </a>
                <a href="{{ route('user.profile') }}" class="flex flex-col items-center justify-center gap-3 p-5 rounded-[18px] border border-hairline hover:bg-canvas-soft-2 hover:border-mute/30 text-ink transition-all group shadow-sm hover:shadow-md">
                    <i data-lucide="user" class="w-6 h-6 text-mute group-hover:scale-110 transition-transform group-hover:text-ink"></i>
                    <span class="text-xs font-bold text-center tracking-wide">Profil Saya</span>
                </a>
                <a href="{{ route('user.notifications') }}" class="flex flex-col items-center justify-center gap-3 p-5 rounded-[18px] border border-hairline hover:bg-canvas-soft-2 hover:border-mute/30 text-ink transition-all group shadow-sm hover:shadow-md">
                    <i data-lucide="bell" class="w-6 h-6 text-mute group-hover:scale-110 transition-transform group-hover:text-ink"></i>
                    <span class="text-xs font-bold text-center tracking-wide">Notifikasi</span>
                </a>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden animate-fade-in-up stagger-4 flex flex-col h-[400px]">
            <div class="p-6 border-b border-hairline flex items-center justify-between shrink-0">
                <h2 class="text-base font-extrabold text-ink tracking-tight flex items-center gap-2">
                    <i data-lucide="bell" class="w-5 h-5 text-primary fill-primary/10"></i> Notifikasi
                </h2>
                <a href="{{ route('user.notifications') }}" class="text-xs font-bold text-primary hover:text-primary-dark hover:underline">Lihat Semua</a>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                @if($notifications->count() > 0)
                    <div class="divide-y divide-hairline">
                        @foreach($notifications as $notif)
                        <div class="p-5 hover:bg-canvas-soft transition-colors flex gap-4 relative overflow-hidden group">
                            @if(!$notif->dibaca)
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                            @endif
                            <div class="shrink-0 mt-0.5">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shadow-sm {{ !$notif->dibaca ? 'bg-primary-soft text-primary' : 'bg-canvas-soft-2 text-mute' }}">
                                    <i data-lucide="{{ !$notif->dibaca ? 'bell-ring' : 'check-check' }}" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-bold {{ !$notif->dibaca ? 'text-ink' : 'text-body' }} tracking-tight">{{ $notif->judul }}</p>
                                <p class="text-xs text-mute mt-1.5 line-clamp-2 leading-relaxed font-medium">{{ $notif->pesan }}</p>
                                <p class="text-[10px] text-mute mt-2.5 font-bold uppercase tracking-wider"><i data-lucide="clock" class="w-3 h-3 inline mr-1 opacity-70"></i>{{ \Carbon\Carbon::parse($notif->dibuat_pada)->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 h-full flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-canvas-soft-2 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="bell-off" class="w-8 h-8 text-mute"></i>
                        </div>
                        <p class="text-sm font-bold text-ink">Semua Beres!</p>
                        <p class="text-xs text-mute mt-1.5 leading-relaxed">Anda sudah membaca semua notifikasi terbaru.</p>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>
@endsection

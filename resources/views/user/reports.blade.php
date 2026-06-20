@extends('layouts.user')
@section('title', 'Riwayat Laporan')
@section('page_title', 'Riwayat Laporan')

@section('content')
{{-- Header Action --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div class="flex-1">
        <h2 class="text-xl font-bold text-ink tracking-tight mb-1">Daftar Laporan Anda</h2>
        <p class="text-sm text-mute">Pantau dan kelola semua status laporan sampah yang telah Anda kirimkan.</p>
    </div>
    <a href="{{ route('user.report.create') }}" class="btn-primary shrink-0 w-full md:w-auto justify-center shadow-lg shadow-primary/30" id="btn-new-report">
        <i data-lucide="camera" class="w-4.5 h-4.5"></i> Lapor Sekarang
    </a>
</div>

{{-- Filters (Dropdown) --}}
<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-canvas p-3 rounded-2xl border border-hairline shadow-sm">
    <div class="flex items-center gap-2 pl-1">
        <div class="w-8 h-8 rounded-full bg-primary-soft flex items-center justify-center">
            <i data-lucide="filter" class="w-4 h-4 text-primary"></i>
        </div>
        <span class="text-sm font-bold text-ink">Filter Laporan</span>
    </div>
    
    <div class="relative w-full sm:w-64">
        <select onchange="window.location.href=this.value" class="w-full bg-canvas-soft border border-hairline text-ink text-sm font-bold rounded-xl block p-3 pr-10 appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-inner cursor-pointer transition-all">
            <option value="{{ route('user.reports') }}" {{ !request('status') ? 'selected' : '' }}>Semua Laporan</option>
            <option value="{{ route('user.reports', ['status' => 'Menunggu']) }}" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Status: Menunggu</option>
            <option value="{{ route('user.reports', ['status' => 'Sedang Dibersihkan']) }}" {{ request('status') == 'Sedang Dibersihkan' ? 'selected' : '' }}>Status: Diproses</option>
            <option value="{{ route('user.reports', ['status' => 'Selesai']) }}" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Status: Selesai</option>
            <option value="{{ route('user.reports', ['status' => 'Ditolak']) }}" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Status: Ditolak</option>
        </select>
        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
            <i data-lucide="chevron-down" class="w-4 h-4 text-mute"></i>
        </div>
    </div>
</div>

{{-- Reports Grid --}}
@if($reports->count() > 0)
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    @foreach($reports as $report)
    <a href="{{ route('user.report.detail', $report->id) }}" class="group flex bg-canvas rounded-2xl border border-hairline overflow-hidden hover:shadow-card-md hover:border-primary/40 transition-all duration-300" id="report-item-{{ $report->id }}">
        
        {{-- Thumbnail Area (Left) --}}
        <div class="relative w-32 sm:w-40 shrink-0 bg-canvas-soft overflow-hidden">
            @if($report->before_images && $report->before_images->count() > 0)
                <img src="{{ Storage::url($report->before_images->first()->jalur_gambar) }}" alt="Foto Sampah" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out">
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-mute bg-canvas-soft-2">
                    <i data-lucide="image" class="w-8 h-8 opacity-30 mb-1"></i>
                </div>
            @endif
            
            {{-- Status Badge (Floating Top Left) --}}
            <div class="absolute top-2 left-2">
                <span class="{{ $report->kelas_badge_status }} scale-75 origin-top-left shadow-sm backdrop-blur-md">{{ $report->label_status }}</span>
            </div>
        </div>

        {{-- Content Area (Right) --}}
        <div class="p-4 sm:p-5 flex-1 flex flex-col min-w-0">
            <div class="flex items-start justify-between gap-2 mb-2">
                <span class="text-[10px] font-bold text-mute uppercase tracking-wider border border-hairline px-2 py-0.5 rounded-md">{{ $report->report_code }}</span>
            </div>
            
            <h3 class="text-sm sm:text-base font-bold text-ink mb-3 group-hover:text-primary transition-colors line-clamp-2 leading-snug">{{ $report->title }}</h3>
            
            <div class="mt-auto flex flex-col gap-1.5 pt-2">
                <div class="flex items-center gap-2 text-xs text-body">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-mute shrink-0"></i>
                    <span class="truncate font-medium">{{ $report->district->nama ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 text-xs text-body">
                    <div class="flex items-center gap-2 min-w-0">
                        <i data-lucide="tag" class="w-3.5 h-3.5 text-mute shrink-0"></i>
                        <span class="truncate font-medium">{{ $report->category->nama ?? '-' }}</span>
                    </div>
                    <span class="text-[10px] font-bold text-mute shrink-0">{{ $report->created_at->format('d M y') }}</span>
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>

{{-- Pagination --}}
<div class="mt-10 flex justify-center">
    {{ $reports->withQueryString()->links() }}
</div>

@else

{{-- Empty State Premium --}}
<div class="bg-canvas rounded-3xl border border-hairline p-12 sm:p-20 text-center shadow-sm max-w-3xl mx-auto flex flex-col items-center justify-center min-h-[50vh]">
    <div class="w-24 h-24 bg-primary-soft rounded-full flex items-center justify-center mb-6 relative">
        <div class="absolute inset-0 bg-primary/20 rounded-full animate-ping opacity-20"></div>
        <i data-lucide="layout-grid" class="w-10 h-10 text-primary"></i>
    </div>
    <h3 class="text-2xl font-black text-ink mb-3 tracking-tight">Belum Ada Laporan</h3>
    <p class="text-body mb-8 max-w-md mx-auto text-base">Anda belum mengirimkan laporan apapun. Mulai buat laporan pertama Anda dan bantu jaga kebersihan lingkungan sekitar kita.</p>
    <a href="{{ route('user.report.create') }}" class="btn-primary shadow-xl shadow-primary/40 px-8 py-4 text-base rounded-2xl group hover:-translate-y-1 transition-all">
        <i data-lucide="camera" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i>
        Buka Kamera Pintar
    </a>
</div>

@endif
@endsection

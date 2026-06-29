@extends('layouts.admin')

@section('page_title', 'Verifikasi Laporan')
@section('title', 'Verifikasi Laporan')

@section('content')
<div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden">
    <div class="px-6 py-6 border-b border-hairline bg-canvas-soft space-y-5">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-lg font-bold text-ink tracking-tight">Daftar Laporan Masuk</h2>
                <p class="text-[11px] text-mute mt-1">Kelola dan verifikasi laporan sampah dari masyarakat.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" form="filter-form" formaction="{{ route('admin.reports.export_pdf') }}" formtarget="_blank" class="btn-primary-sm bg-ink text-canvas hover:bg-ink/80 shrink-0 inline-flex items-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak PDF
                </button>
            </div>
        </div>
        
        <div class="pt-5 border-t border-hairline border-dashed">
            <form method="GET" action="{{ route('admin.reports') }}" id="filter-form">
                
                {{-- Hidden Default Submit Button to capture 'Enter' key --}}
                <button type="submit" class="hidden"></button>

                {{-- Main Search Bar --}}
                <div class="flex items-center gap-2 w-full sm:max-w-md">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-input text-sm w-full bg-white border-hairline rounded-xl py-2 pl-4 pr-12 focus:ring-primary focus:border-primary shadow-sm" placeholder="Cari data (laporan, pelapor, lokasi, kategori, status)...">
                        <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1.5 bg-canvas-soft hover:bg-canvas rounded-lg text-ink border border-hairline transition-colors" title="Cari Data">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                    @php
                        $activeFiltersCount = 0;
                        if(request()->filled('kategori_id') && request('kategori_id') !== 'Semua Kategori') $activeFiltersCount++;
                        if(request()->filled('status') && request('status') !== 'Semua Status') $activeFiltersCount++;
                        if(request()->filled('wilayah_id') && request('wilayah_id') !== 'Semua Wilayah') $activeFiltersCount++;
                        if(request()->filled('start_date') && request()->filled('end_date')) $activeFiltersCount++;
                    @endphp
                    <button type="button" onclick="document.getElementById('advanced-filters-modal').classList.remove('hidden'); document.getElementById('advanced-filters-modal').classList.add('flex');" class="btn-primary-sm bg-ink text-canvas hover:bg-ink/80 shrink-0 inline-flex items-center gap-1.5 h-[38px] px-3 transition-colors">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> 
                        <span class="font-medium">Filter</span>
                        @if($activeFiltersCount > 0)
                            <span class="flex items-center justify-center bg-white text-ink text-[11px] font-bold w-5 h-5 rounded-full ml-1 shrink-0">
                                {{ $activeFiltersCount }}
                            </span>
                        @endif
                    </button>
                </div>

                {{-- Advanced Filters (Centered Modal with Blur) --}}
                <div id="advanced-filters-modal" class="hidden fixed inset-0 z-[100] bg-black/40 backdrop-blur-sm items-center justify-center p-4 transition-all" onclick="if(event.target === this) { this.classList.remove('flex'); this.classList.add('hidden'); }">
                    <div class="bg-white border border-hairline rounded-2xl shadow-2xl overflow-hidden flex flex-col" style="width: 100%; max-width: 420px;" onclick="event.stopPropagation()">
                        
                        {{-- Modal Header --}}
                        <div class="px-5 py-4 border-b border-hairline bg-canvas-soft flex items-center justify-between">
                            <h3 class="text-sm font-bold text-ink">Filter Laporan</h3>
                            <button type="button" onclick="document.getElementById('advanced-filters-modal').classList.remove('flex'); document.getElementById('advanced-filters-modal').classList.add('hidden');" class="text-mute hover:text-ink transition-colors p-1 rounded-lg hover:bg-canvas">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                        
                        {{-- Modal Body --}}
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-ink mb-1.5">Kategori Laporan</label>
                                <select name="kategori_id" class="form-select text-sm w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm">
                                    <option value="Semua Kategori" {{ request('kategori_id') == 'Semua Kategori' ? 'selected' : '' }}>Semua Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-ink mb-1.5">Status Verifikasi</label>
                                <select name="status" class="form-select text-sm w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm">
                                    <option value="Semua Status" {{ request('status') == 'Semua Status' ? 'selected' : '' }}>Semua Status</option>
                                    <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                    <option value="Terverifikasi" {{ request('status') == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                                    <option value="Ditugaskan" {{ request('status') == 'Ditugaskan' ? 'selected' : '' }}>Ditugaskan</option>
                                    <option value="Dalam Perjalanan" {{ request('status') == 'Dalam Perjalanan' ? 'selected' : '' }}>Dalam Perjalanan</option>
                                    <option value="Sedang Dibersihkan" {{ request('status') == 'Sedang Dibersihkan' ? 'selected' : '' }}>Sedang Dibersihkan</option>
                                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="Ditutup" {{ request('status') == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
                                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-ink mb-1.5">Wilayah</label>
                                <select name="wilayah_id" class="form-select text-sm w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm">
                                    <option value="Semua Wilayah" {{ request('wilayah_id') == 'Semua Wilayah' ? 'selected' : '' }}>Semua Wilayah</option>
                                    @foreach($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id }}" {{ request('wilayah_id') == $wilayah->id ? 'selected' : '' }}>{{ $wilayah->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-ink mb-1.5">Rentang Waktu</label>
                                <div class="flex items-center gap-2">
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input text-sm w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm">
                                    <span class="text-mute text-xs font-medium">s/d</span>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input text-sm w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm">
                                </div>
                            </div>
                        </div>
                        
                        {{-- Modal Footer --}}
                        <div class="px-5 py-4 border-t border-hairline bg-canvas-soft flex justify-end gap-2">
                            <a href="{{ route('admin.reports') }}" class="btn-secondary-sm bg-white text-error border-hairline hover:bg-error-soft hover:border-error/30 shrink-0 inline-flex items-center gap-1.5 transition-colors">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset Filter
                            </a>
                            <button type="submit" class="btn-primary-sm bg-ink text-canvas hover:bg-ink/80 shrink-0 inline-flex items-center gap-1.5">
                                <i data-lucide="check" class="w-4 h-4"></i> Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-canvas text-mute text-[10px] uppercase tracking-widest border-b border-hairline">
                    <th class="px-6 py-4 font-bold">Laporan</th>
                    <th class="px-6 py-4 font-bold">Pelapor</th>
                    <th class="px-6 py-4 font-bold">Lokasi</th>
                    <th class="px-6 py-4 font-bold">Kategori</th>
                    <th class="px-6 py-4 font-bold">Status</th>
                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($laporans as $laporan)
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
                                <p class="text-sm font-medium text-body line-clamp-1 max-w-[200px]" title="{{ $laporan->alamat }}">{{ $laporan->alamat }}</p>
                                <p class="text-[11px] text-mute">{{ $laporan->wilayah?->nama ?? 'Tidak diketahui' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 bg-canvas-soft-2 text-mute text-[11px] font-bold rounded-lg border border-hairline">
                            {{ $laporan->kategori?->nama ?? 'Umum' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'Menunggu' => 'bg-warning-soft text-warning border-warning/20',
                                'Terverifikasi' => 'bg-info-soft text-info border-info/20',
                                'Ditugaskan' => 'bg-info-soft text-info border-info/20',
                                'Dalam Perjalanan' => 'bg-primary-soft text-primary border-primary/20',
                                'Sedang Dibersihkan' => 'bg-primary-soft text-primary border-primary/20',
                                'Selesai' => 'bg-success-soft text-success border-success/20',
                                'Ditutup' => 'bg-success-soft text-success border-success/20',
                                'Ditolak' => 'bg-error-soft text-error border-error/20',
                            ];
                            $colorClass = $statusColors[$laporan->status] ?? 'bg-canvas-soft-2 text-mute border-hairline';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $colorClass }} border">
                            @if($laporan->status === 'Menunggu')
                                <span class="w-1.5 h-1.5 rounded-full bg-warning animate-pulse"></span>
                            @endif
                            {{ $laporan->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($laporan->status === 'Menunggu')
                            <a href="{{ route('admin.reports.show', $laporan->id) }}" class="btn-primary-sm inline-flex items-center gap-1.5">
                                Verifikasi
                            </a>
                        @else
                            <a href="{{ route('admin.reports.show', $laporan->id) }}" class="btn-secondary-sm inline-flex items-center gap-1.5 opacity-50 group-hover:opacity-100 transition-opacity bg-canvas-soft-2 border-hairline text-ink hover:bg-canvas-soft-2/80">
                                <i data-lucide="eye" class="w-4 h-4"></i> Detail
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-canvas-soft-2 rounded-full flex items-center justify-center mb-4">
                                <i data-lucide="inbox" class="w-8 h-8 text-mute"></i>
                            </div>
                            <p class="text-base font-bold text-ink">Tidak Ada Laporan</p>
                            <p class="text-sm text-mute mt-1">Belum ada laporan yang sesuai dengan filter pencarian.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($laporans->hasPages())
    <div class="px-6 py-4 border-t border-hairline bg-canvas-soft">
        {{ $laporans->links() }}
    </div>
    @endif
</div>
@endsection


@extends('layouts.admin')

@section('page_title', 'Verifikasi Laporan')
@section('title', 'Verifikasi Laporan')

@section('content')
<div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-hairline bg-canvas-soft flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-ink tracking-tight">Daftar Laporan Masuk</h2>
            <p class="text-[11px] text-mute mt-1">Kelola dan verifikasi laporan sampah dari masyarakat.</p>
        </div>
        <form method="GET" action="{{ route('admin.reports') }}" class="flex gap-2 w-full sm:w-auto" id="filter-form">
            <select name="status" class="form-select text-sm w-full sm:w-48 bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm" onchange="document.getElementById('filter-form').submit()">
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
            <a href="{{ route('admin.reports.export_pdf', request()->all()) }}" class="btn-primary-sm bg-ink text-canvas hover:bg-ink/80 shrink-0 inline-flex items-center gap-1.5" target="_blank">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak PDF
            </a>
        </form>
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


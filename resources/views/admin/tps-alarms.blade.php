@extends('layouts.admin')

@section('page_title', 'Prediksi TPS Liar')
@section('title', 'Prediksi TPS Liar')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-ink tracking-tight">Daftar Titik Rawan TPS Liar</h1>
            <p class="text-sm text-mute mt-1">Sistem mendeteksi area yang sering dilaporkan kotor. Disarankan tindakan pencegahan permanen.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary-sm inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    @if(isset($tpsLiarAlarms) && $tpsLiarAlarms->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up">
            @foreach($tpsLiarAlarms as $alarm)
            <div class="bg-white rounded-[20px] p-5 border border-hairline shadow-sm hover:shadow-card-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-error-soft text-error flex items-center justify-center shrink-0">
                        <i data-lucide="siren" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-ink line-clamp-1" title="{{ $alarm->center_report->alamat }}">{{ $alarm->center_report->alamat }}</p>
                        <p class="text-xs text-mute">{{ $alarm->center_report->wilayah?->nama ?? 'Tidak diketahui' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between bg-canvas-soft rounded-xl p-3 border border-hairline mb-4">
                    <span class="text-xs font-semibold text-body">Tingkat Kerawanan:</span>
                    <span class="text-xs font-black text-white bg-error px-2 py-1 rounded-md">{{ $alarm->count }} Laporan / Bln</span>
                </div>
                
                <div class="flex flex-col gap-2 mt-3">
                    <a href="https://maps.google.com/?q={{ $alarm->center_report->lintang }},{{ $alarm->center_report->bujur }}" target="_blank" class="btn-secondary-sm w-full justify-center bg-white border-hairline text-ink hover:bg-canvas-soft transition-colors">
                        <i data-lucide="map" class="w-4 h-4"></i> Lihat Titik di Peta
                    </a>
                    
                    <form action="{{ route('admin.tps_alarms.dismiss') }}" method="POST" class="w-full">
                        @csrf
                        @php
                            $reportIds = collect($alarm->reports)->pluck('id')->implode(',');
                        @endphp
                        <input type="hidden" name="report_ids" value="{{ $reportIds }}">
                        <button type="submit" class="btn-primary-sm w-full justify-center bg-success-soft text-success border border-success/20 hover:bg-success hover:text-white transition-colors" onclick="return confirm('Apakah Anda yakin sudah menindaklanjuti dan ingin menghilangkan alarm untuk titik ini?')">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Tandai Sudah Ditangani
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-canvas rounded-[24px] border border-hairline p-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-success-soft text-success rounded-full flex items-center justify-center mb-4">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-ink mb-1">Tidak Ada Titik Rawan</h3>
            <p class="text-sm text-mute max-w-md">Saat ini sistem tidak mendeteksi adanya area yang berpotensi menjadi TPS Liar baru berdasarkan laporan sebulan terakhir.</p>
        </div>
    @endif
</div>
@endsection

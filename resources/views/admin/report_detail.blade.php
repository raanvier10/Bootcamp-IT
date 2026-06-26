@extends('layouts.admin')

@section('title', 'Detail & Verifikasi Laporan')
@section('page_title', 'Detail Laporan')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.reports') }}" class="inline-flex items-center gap-2 text-sm font-bold text-mute hover:text-ink transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Laporan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kolom Utama: Detail Laporan -->
    <div class="lg:col-span-2 flex flex-col space-y-6 h-full">
        <!-- Peringatan Duplikat -->
        @if(isset($duplicateReports) && $duplicateReports->count() > 0)
        <div class="bg-warning-soft border border-warning/30 rounded-[24px] p-5 shadow-sm animate-fade-in">
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-warning/20 text-warning flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-warning-dark mb-1">⚠️ Terindikasi Duplikat (Radius 50m)</h3>
                    <p class="text-sm text-warning-dark/80 mb-3">Sistem mendeteksi ada {{ $duplicateReports->count() }} laporan aktif di area yang berdekatan. Mohon periksa foto sebelum memverifikasi.</p>
                    
                    <div class="space-y-3">
                        @foreach($duplicateReports as $dup)
                        <div class="bg-white/60 backdrop-blur-sm border border-warning/20 rounded-xl p-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if($dup->gambarSebelum->count() > 0)
                                    <img src="{{ asset('storage/' . $dup->gambarSebelum->first()->jalur_gambar) }}" class="w-12 h-12 rounded-lg object-cover border border-hairline">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-canvas-soft flex items-center justify-center border border-hairline"><i data-lucide="image" class="w-5 h-5 text-mute"></i></div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-ink">{{ $dup->kode_laporan }}</p>
                                    <p class="text-xs text-mute">Status: {{ $dup->status }} &bull; Jarak: <span class="font-bold text-warning-dark">{{ $dup->distance }} meter</span></p>
                                </div>
                            </div>
                            <a href="{{ route('admin.reports.show', $dup->id) }}" target="_blank" class="btn-secondary-sm bg-white text-xs">Cek Laporan</a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Informasi Utama -->
        <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-sm overflow-hidden flex-1 flex flex-col">
            <div class="p-6 border-b border-hairline flex flex-wrap gap-4 items-center justify-between bg-canvas-soft">
                <div>
                    <h2 class="text-xl font-bold text-ink">{{ $laporan->judul }}</h2>
                    <p class="text-sm text-mute mt-1">{{ $laporan->kode_laporan }} &bull; {{ $laporan->dilaporkan_pada->format('d M Y, H:i') }}</p>
                </div>
                <div>
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
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold {{ $colorClass }} border shadow-sm">
                        @if($laporan->status === 'Menunggu')
                            <span class="w-2 h-2 rounded-full bg-warning animate-pulse"></span>
                        @endif
                        {{ $laporan->status }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6 flex-1 flex flex-col">
                <!-- Deskripsi -->
                <div>
                    <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-2">Deskripsi Laporan</p>
                    <p class="text-body leading-relaxed bg-canvas-soft p-4 rounded-xl border border-hairline">{{ $laporan->deskripsi }}</p>
                </div>

                <!-- Foto Bukti -->
                <div>
                    <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-3">Foto Bukti (Sebelum)</p>
                    @if($laporan->gambarSebelum->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($laporan->gambarSebelum as $gambar)
                                <div onclick="openLightbox('{{ asset('storage/' . $gambar->jalur_gambar) }}')" class="block aspect-video rounded-xl overflow-hidden border border-hairline group relative cursor-pointer">
                                    <img src="{{ asset('storage/' . $gambar->jalur_gambar) }}" alt="Foto Bukti" class="w-full h-full object-contain bg-black/5 group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-ink/0 group-hover:bg-ink/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <i data-lucide="maximize-2" class="w-6 h-6 text-white drop-shadow-md"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-canvas-soft border border-hairline border-dashed rounded-xl p-6 text-center text-mute text-sm">
                            Tidak ada foto bukti yang dilampirkan.
                        </div>
                    @endif
                </div>

                <!-- Lokasi -->
                <div class="mt-auto pt-4">
                    <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-3">Lokasi Kejadian</p>
                    <div class="bg-canvas-soft rounded-xl border border-hairline p-4 flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-primary-soft text-primary flex items-center justify-center shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-ink">{{ $laporan->alamat }}</p>
                            <p class="text-xs text-mute mt-1">Wilayah: {{ $laporan->wilayah?->nama ?? '-' }}</p>
                            <a href="https://maps.google.com/?q={{ $laporan->lintang }},{{ $laporan->bujur }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-primary-dark mt-2 transition-colors">
                                Lihat di Google Maps <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Foto Sesudah (Jika ada) -->
        @if($laporan->gambarSesudah->count() > 0)
        <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-sm overflow-hidden p-6">
            <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-3 text-success">Hasil Penanganan (Sesudah)</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($laporan->gambarSesudah as $gambar)
                    <div onclick="openLightbox('{{ asset('storage/' . $gambar->jalur_gambar) }}')" class="block aspect-video rounded-xl overflow-hidden border border-success/30 group relative cursor-pointer">
                        <img src="{{ asset('storage/' . $gambar->jalur_gambar) }}" alt="Foto Sesudah" class="w-full h-full object-contain bg-black/5 group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-success/0 group-hover:bg-success/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <i data-lucide="maximize-2" class="w-6 h-6 text-white drop-shadow-md"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Kolom Kanan: Aksi & Info Pelapor -->
    <div class="space-y-6">
        <!-- Panel Verifikasi (Hanya jika status Menunggu) -->
        @if($laporan->status === 'Menunggu')
        <div class="bg-canvas rounded-[24px] border border-primary/20 shadow-card-lg overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-info"></div>
            <div class="p-5 border-b border-hairline bg-primary-soft/30">
                <h3 class="font-bold text-ink flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-5 h-5 text-primary"></i> Aksi Verifikasi
                </h3>
            </div>
            <div class="p-5">
                <form action="{{ route('admin.reports.verify', $laporan->id) }}" method="POST" id="verify-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-2">Keputusan</label>
                            <select name="status" id="status-select" class="form-select w-full bg-white border-hairline rounded-xl py-2.5 px-3 focus:ring-primary focus:border-primary shadow-sm text-sm" required onchange="toggleVerifyFields()">
                                <option value="">-- Pilih Keputusan --</option>
                                <option value="Terverifikasi">Terima & Tugaskan</option>
                                <option value="Ditolak">Tolak Laporan</option>
                            </select>
                        </div>

                        <div id="petugas-field" class="hidden">
                            <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-2">Tugaskan ke Petugas</label>
                            <select name="petugas_id" class="form-select w-full bg-white border-hairline rounded-xl py-2.5 px-3 focus:ring-primary focus:border-primary shadow-sm text-sm">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach($petugas as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Wilayah: {{ $p->wilayah->nama ?? 'Umum' }})</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-mute mt-1.5">Sistem akan mengirimkan notifikasi otomatis ke petugas terpilih.</p>
                        </div>

                        <div id="alasan-field" class="hidden">
                            <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-2">Alasan Penolakan</label>
                            <textarea name="alasan_penolakan" rows="3" class="form-textarea w-full bg-white border-hairline rounded-xl py-2.5 px-3 focus:ring-error focus:border-error shadow-sm text-sm" placeholder="Jelaskan alasan laporan ditolak..."></textarea>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-hairline">
                        <button type="submit" class="btn-primary w-full justify-center">Kirim Keputusan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Informasi Pelapor -->
        <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-sm p-5">
            <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-4">Informasi Pelapor</p>
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ $laporan->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($laporan->user?->name ?? 'User').'&background=000&color=fff' }}" alt="{{ $laporan->user?->name ?? 'User' }}" class="w-12 h-12 rounded-full object-cover border border-hairline">
                <div>
                    <p class="text-sm font-bold text-ink">{{ $laporan->user?->name ?? 'User' }}</p>
                    <p class="text-xs text-mute">{{ $laporan->user?->email ?? '-' }}</p>
                </div>
            </div>
            @if($laporan->user?->telepon)
            <div class="flex items-center gap-2 text-sm text-body">
                <i data-lucide="phone" class="w-4 h-4 text-mute"></i> {{ $laporan->user?->telepon }}
            </div>
            @endif
        </div>

        <!-- Riwayat Status -->
        @if($laporan->ulasan)
        <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-sm p-5">
            <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-4">Ulasan Pelapor</p>
            <div class="flex items-center gap-1 mb-2">
                @for($i = 1; $i <= 5; $i++)
                    <i data-lucide="star" class="w-5 h-5 {{ $i <= $laporan->ulasan->rating ? 'text-warning fill-warning' : 'text-hairline' }}"></i>
                @endfor
                <span class="text-sm font-bold text-ink ml-2">{{ $laporan->ulasan->rating }}/5</span>
            </div>
            @if($laporan->ulasan->komentar)
                <p class="text-sm text-body bg-canvas-soft p-3 rounded-xl border border-hairline mt-2">{{ $laporan->ulasan->komentar }}</p>
            @endif
        </div>
        @endif

        <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-sm p-5">
            <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-4">Riwayat Status</p>
            <div class="space-y-4">
                @foreach($laporan->riwayatStatus->sortByDesc('created_at') as $riwayat)
                <div class="flex gap-3 relative">
                    @if(!$loop->last)
                        <div class="absolute top-6 bottom-[-16px] left-[11px] w-[2px] bg-hairline"></div>
                    @endif
                    <div class="w-6 h-6 shrink-0 rounded-full bg-canvas-soft-2 border-2 border-canvas flex items-center justify-center relative z-10 text-mute">
                        <i data-lucide="circle-dot" class="w-3 h-3"></i>
                    </div>
                    <div class="pb-2">
                        <p class="text-sm font-bold text-ink">{{ $riwayat->status }}</p>
                        <p class="text-[11px] text-mute mt-0.5">{{ $riwayat->created_at->format('d M Y, H:i') }} &bull; Oleh: {{ $riwayat->user?->name ?? 'Sistem' }}</p>
                        @if($riwayat->catatan)
                            <p class="text-xs text-body mt-2 bg-canvas-soft p-2 rounded-lg border border-hairline">{{ $riwayat->catatan }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleVerifyFields() {
        const status = document.getElementById('status-select').value;
        const petugasField = document.getElementById('petugas-field');
        const alasanField = document.getElementById('alasan-field');
        const petugasSelect = petugasField.querySelector('select');
        const alasanTextarea = alasanField.querySelector('textarea');

        if (status === 'Terverifikasi') {
            petugasField.classList.remove('hidden');
            petugasSelect.required = true;
            alasanField.classList.add('hidden');
            alasanTextarea.required = false;
            alasanTextarea.value = ''; // Clear reason
        } else if (status === 'Ditolak') {
            alasanField.classList.remove('hidden');
            alasanTextarea.required = true;
            petugasField.classList.add('hidden');
            petugasSelect.required = false;
            
            // Auto-fill reason if duplicate detected
            @if(isset($duplicateReports) && $duplicateReports->count() > 0)
                alasanTextarea.value = "Laporan Duplikat: Tumpukan sampah di lokasi ini sudah dilaporkan oleh warga lain beberapa saat yang lalu, dan saat ini sedang dalam proses penanganan oleh petugas kami. Terima kasih banyak atas kepedulian Anda!";
            @endif
        } else {
            petugasField.classList.add('hidden');
            petugasSelect.required = false;
            alasanField.classList.add('hidden');
            alasanTextarea.required = false;
        }
    }
</script>
@endpush
@endsection

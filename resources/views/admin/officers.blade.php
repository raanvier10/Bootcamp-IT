@extends('layouts.admin')

@section('page_title', 'Kelola Petugas')
@section('title', 'Petugas Lapangan')

@section('content')
<div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-hairline bg-canvas-soft flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-ink tracking-tight">Daftar Petugas Lapangan</h2>
            <p class="text-[11px] text-mute mt-1">Kelola data petugas yang akan menangani laporan sampah.</p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <form method="GET" action="{{ route('admin.officers') }}" class="flex gap-2 w-full">
                <div class="relative w-full sm:w-64">
                    <i data-lucide="search" class="w-4 h-4 text-mute absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode..." class="form-input w-full pl-9 bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm text-sm">
                </div>
                <button type="submit" class="btn-primary-sm shrink-0">Cari</button>
            </form>
            <!-- Tombol tambah petugas -->
            <button class="btn-primary-sm bg-ink text-canvas hover:bg-ink/80 border-transparent shrink-0 flex items-center gap-1.5" onclick="openAddModal()">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-canvas text-mute text-[10px] uppercase tracking-widest border-b border-hairline">
                    <th class="px-6 py-4 font-bold">Petugas</th>
                    <th class="px-6 py-4 font-bold">Kode Pegawai</th>
                    <th class="px-6 py-4 font-bold">Wilayah Tugas</th>
                    <th class="px-6 py-4 font-bold">Kinerja (Selesai/Total)</th>
                    <th class="px-6 py-4 font-bold">Rating</th>
                    <th class="px-6 py-4 font-bold">Status</th>
                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($officers as $officer)
                <tr class="hover:bg-canvas-soft transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $officer->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($officer->nama).'&background=000&color=fff' }}" alt="{{ $officer->nama }}" class="w-10 h-10 rounded-full object-cover border border-hairline">
                            <div>
                                <p class="text-sm font-bold text-ink">{{ $officer->nama }}</p>
                                <p class="text-[11px] text-mute mt-0.5">{{ $officer->email }}</p>
                                <p class="text-[11px] text-mute">{{ $officer->telepon ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 bg-canvas-soft-2 text-ink text-xs font-mono font-bold rounded-lg border border-hairline">
                            {{ $officer->kode_pegawai ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="map" class="w-4 h-4 text-mute"></i>
                            <span class="text-sm font-medium text-body">{{ $officer->wilayah->nama ?? 'Semua Wilayah' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $totalTugas = $officer->penugasan->count();
                            $tugasSelesai = $officer->penugasan->filter(function($p) {
                                return $p->laporan && in_array($p->laporan->status, ['Selesai', 'Ditutup']);
                            })->count();
                            
                            $persentase = $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100) : 0;
                            
                            // Calculate rating
                            $totalRating = 0;
                            $countRating = 0;
                            foreach($officer->penugasan as $p) {
                                if($p->laporan && $p->laporan->ulasan) {
                                    $totalRating += $p->laporan->ulasan->nilai;
                                    $countRating++;
                                }
                            }
                            $avgRating = $countRating > 0 ? number_format($totalRating / $countRating, 1) : 0;
                        @endphp
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-ink">{{ $tugasSelesai }} / {{ $totalTugas }}</span>
                                <span class="text-mute">{{ $persentase }}%</span>
                            </div>
                            <div class="w-full bg-canvas-soft-2 rounded-full h-1.5">
                                <div class="bg-primary h-1.5 rounded-full" style="width: {{ $persentase }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1 text-sm font-bold {{ $avgRating > 0 ? 'text-warning' : 'text-mute' }}">
                            <i data-lucide="star" class="w-4 h-4 {{ $avgRating > 0 ? 'fill-warning' : '' }}"></i>
                            {{ $avgRating > 0 ? $avgRating : '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($officer->aktif)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-success-soft text-success border border-success/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-success"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-mute/10 text-mute border border-hairline">
                                <span class="w-1.5 h-1.5 rounded-full bg-mute"></span> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="btn-secondary-sm inline-flex items-center gap-1.5 opacity-50 group-hover:opacity-100 transition-opacity" 
                            onclick="openEditModal({{ $officer->id }}, '{{ addslashes($officer->nama) }}', '{{ addslashes($officer->telepon) }}', '{{ $officer->wilayah_id }}', {{ $officer->aktif ? 'true' : 'false' }})">
                            <i data-lucide="edit" class="w-4 h-4"></i> Edit
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-canvas-soft-2 rounded-full flex items-center justify-center mb-4">
                                <i data-lucide="users" class="w-8 h-8 text-mute"></i>
                            </div>
                            <p class="text-base font-bold text-ink">Tidak Ada Data Petugas</p>
                            <p class="text-sm text-mute mt-1">Gunakan kata kunci lain atau tambahkan petugas baru.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($officers->hasPages())
    <div class="px-6 py-4 border-t border-hairline bg-canvas-soft">
        {{ $officers->links() }}
    </div>
    @endif
</div>

<!-- Modal Edit Petugas -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="relative w-full max-w-md bg-canvas rounded-[24px] shadow-card-lg border border-hairline overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-hairline bg-canvas-soft flex items-center justify-between">
            <h3 class="font-bold text-ink">Edit Data Petugas</h3>
            <button onclick="closeEditModal()" class="text-mute hover:text-ink transition-colors p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="edit-form" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" id="edit-nama" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm" required>
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">No. Telepon</label>
                    <input type="text" name="telepon" id="edit-telepon" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm">
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Wilayah Tugas</label>
                    <select name="wilayah_id" id="edit-wilayah" class="form-select w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm">
                        <option value="">Semua Wilayah</option>
                        @foreach($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id }}">{{ $wilayah->nama }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Status Akun</label>
                    <select name="aktif" id="edit-aktif" class="form-select w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm" required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-hairline bg-canvas-soft flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="btn-secondary-sm bg-white">Batal</button>
                <button type="submit" class="btn-primary-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Petugas -->
<div id="add-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="relative w-full max-w-md bg-canvas rounded-[24px] shadow-card-lg border border-hairline overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-hairline bg-canvas-soft flex items-center justify-between">
            <h3 class="font-bold text-ink">Tambah Petugas Baru</h3>
            <button onclick="closeAddModal()" class="text-mute hover:text-ink transition-colors p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="add-form" method="POST" action="{{ route('admin.officers.store') }}">
            @csrf
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm" required>
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Email Akses</label>
                    <input type="email" name="email" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm" required>
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm" required minlength="8">
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">No. Telepon</label>
                    <input type="text" name="telepon" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm">
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Wilayah Tugas</label>
                    <select name="wilayah_id" class="form-select w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm">
                        <option value="">Semua Wilayah</option>
                        @foreach($wilayahs as $wilayah)
                            <option value="{{ $wilayah->id }}">{{ $wilayah->nama }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Status Akun</label>
                    <select name="aktif" class="form-select w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm" required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-hairline bg-canvas-soft flex justify-end gap-3">
                <button type="button" onclick="closeAddModal()" class="btn-secondary-sm bg-white">Batal</button>
                <button type="submit" class="btn-primary-sm">Tambahkan Petugas</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Edit Modal
    function openEditModal(id, nama, telepon, wilayahId, isAktif) {
        const modal = document.getElementById('edit-modal');
        const form = document.getElementById('edit-form');
        
        // Setup form action route
        form.action = `/admin/petugas/${id}`;
        
        // Fill data
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-telepon').value = telepon || '';
        document.getElementById('edit-wilayah').value = wilayahId || '';
        document.getElementById('edit-aktif').value = isAktif ? '1' : '0';
        
        // Show modal (remove hidden, use flex)
        modal.classList.remove('hidden');
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
    }
    
    function closeEditModal() {
        const modal = document.getElementById('edit-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    // Add Modal
    function openAddModal() {
        const modal = document.getElementById('add-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeAddModal() {
        const modal = document.getElementById('add-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
</script>
@endpush

@endsection

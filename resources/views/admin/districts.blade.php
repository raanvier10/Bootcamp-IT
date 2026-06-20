@extends('layouts.admin')

@section('page_title', 'Master Data: Wilayah')
@section('title', 'Kelola Wilayah')

@section('content')
<div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-hairline bg-canvas-soft flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-ink tracking-tight">Daftar Wilayah Operasional</h2>
            <p class="text-[11px] text-mute mt-1">Kelola data kecamatan/kelurahan untuk pemetaan laporan.</p>
        </div>
        <button class="btn-primary-sm bg-ink text-canvas hover:bg-ink/80 flex items-center gap-1.5" onclick="openAddModal()">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Wilayah
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-canvas text-mute text-[10px] uppercase tracking-widest border-b border-hairline">
                    <th class="px-6 py-4 font-bold">Kode</th>
                    <th class="px-6 py-4 font-bold">Nama Wilayah</th>
                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($wilayahs as $wilayah)
                <tr class="hover:bg-canvas-soft transition-colors group">
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 bg-canvas-soft-2 text-ink text-xs font-mono font-bold rounded-lg border border-hairline">
                            {{ $wilayah->kode }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-ink">{{ $wilayah->nama }}</p>
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <button class="btn-secondary-sm opacity-50 group-hover:opacity-100" onclick="openEditModal({{ $wilayah->id }}, '{{ addslashes($wilayah->kode) }}', '{{ addslashes($wilayah->nama) }}')">
                            <i data-lucide="edit" class="w-4 h-4"></i> Edit
                        </button>
                        <form action="{{ route('admin.districts.destroy', $wilayah->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus wilayah ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-secondary-sm text-error border-error/20 hover:bg-error-soft opacity-50 group-hover:opacity-100">
                                <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-mute text-sm">Tidak ada data wilayah.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="district-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative w-full max-w-md bg-canvas rounded-[24px] shadow-card-lg border border-hairline overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-hairline bg-canvas-soft flex items-center justify-between">
            <h3 id="modal-title" class="font-bold text-ink">Tambah Wilayah</h3>
            <button onclick="closeModal()" class="text-mute hover:text-ink"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        
        <form id="district-form" method="POST" action="{{ route('admin.districts.store') }}">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Kode Wilayah</label>
                    <input type="text" name="kode" id="input-kode" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary shadow-sm" required placeholder="Contoh: KRW-BRT">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Nama Wilayah</label>
                    <input type="text" name="nama" id="input-nama" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary shadow-sm" required placeholder="Contoh: Karawang Barat">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-hairline bg-canvas-soft flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="btn-secondary-sm bg-white">Batal</button>
                <button type="submit" class="btn-primary-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('modal-title').innerText = 'Tambah Wilayah';
        document.getElementById('district-form').action = "{{ route('admin.districts.store') }}";
        document.getElementById('form-method').value = 'POST';
        document.getElementById('input-kode').value = '';
        document.getElementById('input-nama').value = '';
        document.getElementById('district-modal').classList.remove('hidden');
    }

    function openEditModal(id, kode, nama) {
        document.getElementById('modal-title').innerText = 'Edit Wilayah';
        document.getElementById('district-form').action = `/admin/wilayah/${id}`;
        document.getElementById('form-method').value = 'PUT';
        document.getElementById('input-kode').value = kode;
        document.getElementById('input-nama').value = nama;
        document.getElementById('district-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('district-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection

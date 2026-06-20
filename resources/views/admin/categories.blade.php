@extends('layouts.admin')

@section('page_title', 'Master Data: Kategori Sampah')
@section('title', 'Kelola Kategori')

@section('content')
<div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-hairline bg-canvas-soft flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-ink tracking-tight">Daftar Kategori Sampah</h2>
            <p class="text-[11px] text-mute mt-1">Kelola jenis-jenis sampah yang dapat dilaporkan masyarakat.</p>
        </div>
        <button class="btn-primary-sm bg-ink text-canvas hover:bg-ink/80 flex items-center gap-1.5" onclick="openAddModal()">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-canvas text-mute text-[10px] uppercase tracking-widest border-b border-hairline">
                    <th class="px-6 py-4 font-bold">Nama Kategori</th>
                    <th class="px-6 py-4 font-bold">Deskripsi</th>
                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($kategoris as $kategori)
                <tr class="hover:bg-canvas-soft transition-colors group">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-ink">{{ $kategori->nama }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-body">{{ $kategori->deskripsi ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <button class="btn-secondary-sm opacity-50 group-hover:opacity-100" onclick="openEditModal({{ $kategori->id }}, '{{ addslashes($kategori->nama) }}', '{{ addslashes($kategori->deskripsi) }}')">
                            <i data-lucide="edit" class="w-4 h-4"></i> Edit
                        </button>
                        <form action="{{ route('admin.categories.destroy', $kategori->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
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
                    <td colspan="3" class="px-6 py-12 text-center text-mute text-sm">Tidak ada data kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="category-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative w-full max-w-md bg-canvas rounded-[24px] shadow-card-lg border border-hairline overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-hairline bg-canvas-soft flex items-center justify-between">
            <h3 id="modal-title" class="font-bold text-ink">Tambah Kategori</h3>
            <button onclick="closeModal()" class="text-mute hover:text-ink"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        
        <form id="category-form" method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Nama Kategori</label>
                    <input type="text" name="nama" id="input-nama" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary shadow-sm" required placeholder="Contoh: Sampah Organik">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" id="input-deskripsi" rows="3" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary shadow-sm" placeholder="Opsional"></textarea>
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
        document.getElementById('modal-title').innerText = 'Tambah Kategori';
        document.getElementById('category-form').action = "{{ route('admin.categories.store') }}";
        document.getElementById('form-method').value = 'POST';
        document.getElementById('input-nama').value = '';
        document.getElementById('input-deskripsi').value = '';
        document.getElementById('category-modal').classList.remove('hidden');
    }

    function openEditModal(id, nama, deskripsi) {
        document.getElementById('modal-title').innerText = 'Edit Kategori';
        document.getElementById('category-form').action = `/admin/kategori/${id}`;
        document.getElementById('form-method').value = 'PUT';
        document.getElementById('input-nama').value = nama;
        document.getElementById('input-deskripsi').value = deskripsi;
        document.getElementById('category-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('category-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection

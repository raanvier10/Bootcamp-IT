@extends('layouts.admin')

@section('page_title', 'Tambah Artikel')
@section('title', 'Tambah Artikel')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-container.ql-snow {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1rem;
        min-height: 500px;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        border-color: #e2e8f0; /* hairline */
        background-color: white;
    }
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        border-color: #e2e8f0;
        background-color: #f8fafc;
        padding: 12px;
    }
    .ql-editor {
        min-height: 500px;
        line-height: 1.7;
        padding: 1.5rem;
    }
    .ql-editor p {
        margin-bottom: 1em;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.artikel.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-mute hover:text-ink transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Artikel
        </a>
    </div>

    <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-hairline bg-canvas-soft">
            <h2 class="text-lg font-bold text-ink tracking-tight">Tulis Artikel Baru</h2>
        </div>
        
        <form id="article-form" action="{{ route('admin.artikel.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            
            @if ($errors->any())
                <div class="alert-error">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="space-y-4">
                <div>
                    <label for="judul" class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Judul Artikel</label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm" required placeholder="Masukkan judul artikel...">
                </div>

                <div>
                    <label for="gambar_sampul" class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Gambar Sampul</label>
                    <input type="file" name="gambar_sampul" id="gambar_sampul" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm" accept="image/*">
                    <p class="text-xs text-mute mt-1">Format: JPG, PNG, GIF. Maksimal 5MB.</p>
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Isi Artikel</label>
                    <input type="hidden" name="isi" id="isi_hidden" value="{{ old('isi') }}">
                    <div id="editor-container">{!! old('isi') !!}</div>
                </div>
                
                <div>
                    <label for="status" class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Status Publikasi</label>
                    <select name="status" id="status" class="form-select w-full bg-white border-hairline rounded-xl py-2 px-3 focus:ring-primary focus:border-primary shadow-sm" required>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Terbitkan Langsung</option>
                    </select>
                </div>
            </div>
            
            <div class="pt-6 border-t border-hairline flex justify-end gap-3">
                <a href="{{ route('admin.artikel.index') }}" class="btn-secondary-sm bg-white">Batal</a>
                <button type="submit" class="btn-primary-sm bg-ink text-canvas hover:bg-ink/80 border-transparent">
                    Simpan Artikel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Mulai menulis artikel Anda di sini...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });

        const form = document.getElementById('article-form');
        form.addEventListener('submit', function(e) {
            const isiHidden = document.getElementById('isi_hidden');
            // Check if editor is empty (Quill leaves a blank paragraph)
            if (quill.getText().trim().length === 0 && !quill.root.querySelector('img')) {
                e.preventDefault();
                alert('Isi artikel tidak boleh kosong!');
                return false;
            }
            isiHidden.value = quill.root.innerHTML;
        });
    });
</script>
@endpush

@endsection

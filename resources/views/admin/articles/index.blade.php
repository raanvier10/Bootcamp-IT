@extends('layouts.admin')

@section('page_title', 'Kelola Artikel')
@section('title', 'Kelola Artikel')

@section('content')
<div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-hairline bg-canvas-soft flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-ink tracking-tight">Daftar Artikel</h2>
            <p class="text-[11px] text-mute mt-1">Kelola artikel edukasi dan berita lingkungan.</p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.artikel.create') }}" class="btn-primary-sm bg-ink text-canvas hover:bg-ink/80 border-transparent shrink-0 flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Artikel
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-canvas text-mute text-[10px] uppercase tracking-widest border-b border-hairline">
                    <th class="px-6 py-4 font-bold">Judul Artikel</th>
                    <th class="px-6 py-4 font-bold">Status</th>
                    <th class="px-6 py-4 font-bold">Tanggal Diterbitkan</th>
                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($articles as $article)
                <tr class="hover:bg-canvas-soft transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-canvas-soft-2 border border-hairline overflow-hidden shrink-0">
                                @if($article->gambar_sampul)
                                    <img src="{{ $article->gambar_sampul }}" alt="{{ $article->judul }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i data-lucide="image" class="w-5 h-5 text-mute"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-ink">{{ $article->judul }}</p>
                                <p class="text-[11px] text-mute mt-0.5 line-clamp-1 max-w-xs">{{ $article->kutipan }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($article->sudah_diterbitkan)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-success-soft text-success border border-success/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-success"></span> Diterbitkan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-mute/10 text-mute border border-hairline">
                                <span class="w-1.5 h-1.5 rounded-full bg-mute"></span> Draft
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-body">
                            {{ $article->diterbitkan_pada ? $article->diterbitkan_pada->format('d M Y') : '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.artikel.edit', $article->id) }}" class="btn-secondary-sm inline-flex items-center gap-1.5 opacity-50 group-hover:opacity-100 transition-opacity">
                                <i data-lucide="edit" class="w-4 h-4"></i> Edit
                            </a>
                            <form action="{{ route('admin.artikel.destroy', $article->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-error-soft text-error hover:bg-error hover:text-white transition-colors opacity-50 group-hover:opacity-100" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-canvas-soft-2 rounded-full flex items-center justify-center mb-4">
                                <i data-lucide="file-text" class="w-8 h-8 text-mute"></i>
                            </div>
                            <p class="text-base font-bold text-ink">Belum Ada Artikel</p>
                            <p class="text-sm text-mute mt-1">Tambahkan artikel baru untuk mulai berbagi informasi.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($articles->hasPages())
    <div class="px-6 py-4 border-t border-hairline bg-canvas-soft">
        {{ $articles->links() }}
    </div>
    @endif
</div>
@endsection

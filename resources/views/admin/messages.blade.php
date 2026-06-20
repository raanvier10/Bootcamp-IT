@extends('layouts.admin')

@section('page_title', 'Komunikasi: Pesan Publik')
@section('title', 'Pesan Masuk')

@section('content')
<div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-hairline bg-canvas-soft flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-ink tracking-tight">Kotak Masuk Pesan Publik</h2>
            <p class="text-[11px] text-mute mt-1">Kritik, saran, dan pertanyaan dari halaman Hubungi Kami.</p>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-canvas text-mute text-[10px] uppercase tracking-widest border-b border-hairline">
                    <th class="px-6 py-4 font-bold">Tanggal</th>
                    <th class="px-6 py-4 font-bold">Pengirim</th>
                    <th class="px-6 py-4 font-bold">Pesan</th>
                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @forelse($pesans as $pesan)
                <tr class="hover:bg-canvas-soft transition-colors group">
                    <td class="px-6 py-4 align-top w-40">
                        <span class="text-[11px] font-bold text-mute">{{ \Carbon\Carbon::parse($pesan->dibuat_pada)->format('d M Y, H:i') }}</span>
                    </td>
                    <td class="px-6 py-4 align-top w-64">
                        <p class="text-sm font-bold text-ink">{{ $pesan->nama }}</p>
                        <p class="text-[11px] text-mute">{{ $pesan->email }}</p>
                    </td>
                    <td class="px-6 py-4 align-top">
                        <p class="text-sm font-bold text-ink mb-1">{{ $pesan->subjek }}</p>
                        <p class="text-sm text-body line-clamp-2" title="{{ $pesan->pesan }}">{{ $pesan->pesan }}</p>
                    </td>
                    <td class="px-6 py-4 align-top text-right w-24">
                        <form action="{{ route('admin.messages.destroy', $pesan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-secondary-sm text-error border-error/20 hover:bg-error-soft opacity-50 group-hover:opacity-100 p-2" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-canvas-soft-2 rounded-full flex items-center justify-center mb-4">
                                <i data-lucide="mail-open" class="w-8 h-8 text-mute"></i>
                            </div>
                            <p class="text-base font-bold text-ink">Tidak Ada Pesan</p>
                            <p class="text-sm text-mute mt-1">Kotak masuk publik saat ini kosong.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pesans->hasPages())
    <div class="px-6 py-4 border-t border-hairline bg-canvas-soft">
        {{ $pesans->links() }}
    </div>
    @endif
</div>
@endsection

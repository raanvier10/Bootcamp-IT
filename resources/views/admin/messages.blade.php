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
                <tr class="hover:bg-canvas-soft transition-colors group cursor-pointer" 
                    onclick="openMessageModal(this)"
                    data-name="{{ $pesan->nama }}"
                    data-email="{{ $pesan->email }}"
                    data-date="{{ \Carbon\Carbon::parse($pesan->dibuat_pada)->format('d M Y, H:i') }}"
                    data-subject="{{ $pesan->subjek }}"
                    data-message="{{ $pesan->pesan }}">
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
                    <td class="px-6 py-4 align-top text-right w-24" onclick="event.stopPropagation()">
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

{{-- Modal Detail Pesan --}}
<div id="message-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 opacity-0 transition-opacity duration-300 backdrop-blur-sm" onclick="closeMessageModal()">
    <div class="bg-canvas rounded-[24px] shadow-modal w-full max-w-xl mx-4 transform scale-95 transition-transform duration-300 border border-hairline overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 py-5 border-b border-hairline flex justify-between items-center bg-canvas-soft">
            <h3 class="text-lg font-bold text-ink truncate pr-4" id="modal-subject">Subjek Pesan</h3>
            <button type="button" class="text-mute hover:text-ink shrink-0 bg-white border border-hairline rounded-full p-1.5 hover:bg-canvas-soft-2 transition-colors" onclick="closeMessageModal()">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-hairline">
                <div class="w-12 h-12 rounded-full bg-primary-soft flex items-center justify-center text-primary font-bold text-lg shrink-0" id="modal-initial">
                    A
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-ink text-base truncate" id="modal-name">Nama Pengirim</p>
                    <p class="text-sm text-mute truncate" id="modal-email">email@contoh.com</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-xs font-bold text-mute uppercase tracking-widest mb-1">Tanggal</p>
                    <p class="text-sm text-ink" id="modal-date">01 Jan 2026</p>
                </div>
            </div>
            
            <div>
                <p class="text-xs font-bold text-mute uppercase tracking-widest mb-3">Isi Pesan</p>
                <div class="bg-canvas-soft-2 rounded-xl p-5 border border-hairline max-h-64 overflow-y-auto">
                    <p class="text-sm text-body whitespace-pre-wrap leading-relaxed" id="modal-message">Isi pesan...</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-hairline flex justify-end bg-canvas-soft">
            <button type="button" class="btn-primary-sm bg-ink text-white hover:bg-ink/80 border-transparent" onclick="closeMessageModal()">Tutup</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openMessageModal(element) {
        const name = element.getAttribute('data-name');
        const email = element.getAttribute('data-email');
        const date = element.getAttribute('data-date');
        const subject = element.getAttribute('data-subject');
        const message = element.getAttribute('data-message');
        
        document.getElementById('modal-name').textContent = name;
        document.getElementById('modal-email').textContent = email;
        document.getElementById('modal-date').textContent = date;
        document.getElementById('modal-subject').textContent = subject;
        document.getElementById('modal-message').textContent = message;
        document.getElementById('modal-initial').textContent = name.charAt(0).toUpperCase();

        const modal = document.getElementById('message-modal');
        const modalInner = modal.querySelector('div.transform');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Trigger reflow
        void modal.offsetWidth;
        
        modal.classList.remove('opacity-0');
        modalInner.classList.remove('scale-95');
        modalInner.classList.add('scale-100');
    }

    function closeMessageModal() {
        const modal = document.getElementById('message-modal');
        const modalInner = modal.querySelector('div.transform');
        
        modal.classList.add('opacity-0');
        modalInner.classList.remove('scale-100');
        modalInner.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>
@endpush
@endsection

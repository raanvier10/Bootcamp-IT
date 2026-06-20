@extends('layouts.officer')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi Tugas')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-hairline flex items-center justify-between bg-canvas-soft/50">
            <div>
                <h2 class="text-lg font-black text-ink tracking-tight">Semua Notifikasi</h2>
                <p class="text-sm font-medium text-mute mt-1">Pemberitahuan tugas dan pembaruan sistem.</p>
            </div>
        </div>
        
        <div class="divide-y divide-hairline">
            @forelse($notifications as $notif)
                <a href="{{ route('officer.notification.read', $notif->id) }}" class="block p-6 hover:bg-canvas-soft transition-colors group {{ !$notif->sudah_dibaca ? 'bg-primary-soft/10' : '' }}">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 {{ !$notif->sudah_dibaca ? 'bg-primary-soft text-primary shadow-inner border border-primary/20' : 'bg-canvas-soft-2 text-mute' }}">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-base font-bold mb-1 {{ !$notif->sudah_dibaca ? 'text-ink' : 'text-body' }} group-hover:text-primary transition-colors">
                                {{ $notif->judul }}
                            </p>
                            <p class="text-sm text-body leading-relaxed max-w-3xl">
                                {{ $notif->pesan }}
                            </p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-mute mt-3 flex items-center gap-1.5">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                {{ \Carbon\Carbon::parse($notif->dibuat_pada)->diffForHumans() }}
                            </p>
                        </div>
                        @if(!$notif->sudah_dibaca)
                            <div class="flex-shrink-0 flex items-center pl-2">
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                                </span>
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="p-16 text-center flex flex-col items-center">
                    <div class="w-20 h-20 bg-canvas-soft-2 rounded-full flex items-center justify-center mb-5">
                        <i data-lucide="bell-off" class="w-8 h-8 text-mute"></i>
                    </div>
                    <h3 class="text-lg font-bold text-ink mb-2">Belum Ada Notifikasi</h3>
                    <p class="text-sm text-mute max-w-sm">Semua pemberitahuan dan tugas baru akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="p-6 border-t border-hairline bg-canvas-soft/30">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

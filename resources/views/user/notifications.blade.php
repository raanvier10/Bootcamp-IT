@extends('layouts.user')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Semua Notifikasi</h2>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notif)
                <a href="{{ route('user.notification.read', $notif->id) }}" class="block p-6 hover:bg-gray-50 transition-colors {{ !$notif->sudah_dibaca ? 'bg-primary/5' : '' }}">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ !$notif->sudah_dibaca ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-gray-100 text-gray-500' }}">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold {{ !$notif->sudah_dibaca ? 'text-gray-900' : 'text-gray-700' }}">
                                {{ $notif->judul }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                                {{ $notif->pesan }}
                            </p>
                            <p class="text-xs text-gray-400 mt-2 flex items-center gap-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                {{ \Carbon\Carbon::parse($notif->dibuat_pada)->diffForHumans() }}
                            </p>
                        </div>
                        @if(!$notif->sudah_dibaca)
                            <div class="flex-shrink-0 flex items-center">
                                <span class="w-3 h-3 bg-primary rounded-full block shadow-sm shadow-primary/40 animate-pulse"></span>
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="bell-off" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-gray-900 font-bold mb-1">Belum Ada Notifikasi</h3>
                    <p class="text-gray-500 text-sm">Semua pembaruan laporan Anda akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="p-6 border-t border-gray-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Tandai semua notifikasi sudah dibaca (opsional, jika ada endpoint mark-as-read, bisa ditambahkan via AJAX)
</script>
@endpush
@endsection

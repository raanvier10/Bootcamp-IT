@extends('layouts.admin')

@section('page_title', 'Profil Saya')
@section('title', 'Profil')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-lg overflow-hidden relative">
        <div class="h-32 bg-gradient-to-r from-primary to-info opacity-90 relative">
            <div class="absolute inset-0 bg-black/10"></div>
        </div>
        
        <div class="px-8 pb-8">
            <div class="relative flex justify-between items-end -mt-12 mb-6">
                <div class="flex items-end gap-5">
                    <div class="w-24 h-24 rounded-full border-4 border-canvas bg-white overflow-hidden shadow-md relative z-10">
                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=000&color=fff' }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="mb-1">
                        <h2 class="text-2xl font-black text-ink tracking-tight leading-none">{{ $user->name }}</h2>
                        <p class="text-sm font-medium text-mute mt-1">Administrator Sistem</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                <div class="space-y-5">
                    <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-2 border-b border-hairline pb-2">Informasi Akun</p>
                    
                    <div>
                        <p class="text-[10px] font-bold text-mute uppercase tracking-wider mb-1">Nama Lengkap</p>
                        <p class="text-sm font-medium text-ink">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-bold text-mute uppercase tracking-wider mb-1">Email</p>
                        <p class="text-sm font-medium text-ink">{{ $user->email }}</p>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-bold text-mute uppercase tracking-wider mb-1">Nomor Telepon</p>
                        <p class="text-sm font-medium text-ink">{{ $user->telepon ?? 'Belum diatur' }}</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <p class="text-[11px] font-bold text-mute uppercase tracking-widest mb-2 border-b border-hairline pb-2">Pengaturan Keamanan</p>
                    
                    <button class="w-full bg-canvas-soft border border-hairline rounded-xl p-4 flex items-center justify-between group hover:border-primary/30 transition-colors text-left" onclick="alert('Fitur ubah kata sandi dapat ditambahkan di pengembangan selanjutnya.')">
                        <div>
                            <p class="text-sm font-bold text-ink group-hover:text-primary transition-colors">Ubah Kata Sandi</p>
                            <p class="text-xs text-mute mt-0.5">Perbarui kata sandi secara berkala</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-5 h-5 text-mute group-hover:text-primary transition-colors"></i>
                    </button>
                    
                    <button class="w-full bg-canvas-soft border border-hairline rounded-xl p-4 flex items-center justify-between group hover:border-primary/30 transition-colors text-left" onclick="alert('Fitur edit profil dapat ditambahkan di pengembangan selanjutnya.')">
                        <div>
                            <p class="text-sm font-bold text-ink group-hover:text-primary transition-colors">Edit Profil</p>
                            <p class="text-xs text-mute mt-0.5">Ubah nama, email, dan foto</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-5 h-5 text-mute group-hover:text-primary transition-colors"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

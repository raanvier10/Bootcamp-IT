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
            <div class="relative -mt-12 mb-6 text-center sm:text-left">
                <div class="w-24 h-24 mx-auto sm:mx-0 rounded-full border-4 border-canvas bg-white overflow-hidden shadow-md relative z-10 mb-4">
                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=000&color=fff' }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <h2 class="text-2xl font-black text-ink tracking-tight leading-none">{{ $user->name }}</h2>
                    <p class="text-sm font-medium text-mute mt-1.5">Administrator Sistem</p>
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
                    
                    <button class="w-full bg-canvas-soft border border-hairline rounded-xl p-4 flex items-center justify-between group hover:border-primary/30 transition-colors text-left" onclick="openPasswordModal()">
                        <div>
                            <p class="text-sm font-bold text-ink group-hover:text-primary transition-colors">Ubah Kata Sandi</p>
                            <p class="text-xs text-mute mt-0.5">Perbarui kata sandi secara berkala</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-5 h-5 text-mute group-hover:text-primary transition-colors"></i>
                    </button>
                    
                    <button class="w-full bg-canvas-soft border border-hairline rounded-xl p-4 flex items-center justify-between group hover:border-primary/30 transition-colors text-left" onclick="openProfileModal()">
                        <div>
                            <p class="text-sm font-bold text-ink group-hover:text-primary transition-colors">Edit Profil</p>
                            <p class="text-xs text-mute mt-0.5">Ubah nama, email, dan telepon</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-5 h-5 text-mute group-hover:text-primary transition-colors"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div id="profile-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeProfileModal()"></div>
    <div class="relative w-full max-w-md bg-canvas rounded-[24px] shadow-card-lg border border-hairline overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-hairline bg-canvas-soft flex items-center justify-between">
            <h3 class="font-bold text-ink">Edit Profil</h3>
            <button onclick="closeProfileModal()" class="text-mute hover:text-ink transition-colors p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ $user->name }}" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm" required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm" required>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Nomor Telepon</label>
                    <input type="text" name="telepon" value="{{ $user->telepon }}" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 text-sm focus:ring-primary focus:border-primary shadow-sm">
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-hairline bg-canvas-soft flex justify-end gap-3">
                <button type="button" onclick="closeProfileModal()" class="btn-secondary-sm bg-white">Batal</button>
                <button type="submit" class="btn-primary-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Password -->
<div id="password-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closePasswordModal()"></div>
    <div class="relative w-full max-w-md bg-canvas rounded-[24px] shadow-card-lg border border-hairline overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-hairline bg-canvas-soft flex items-center justify-between">
            <h3 class="font-bold text-ink">Ubah Kata Sandi</h3>
            <button onclick="closePasswordModal()" class="text-mute hover:text-ink transition-colors p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.profile.password') }}">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Kata Sandi Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 pr-10 text-sm focus:ring-primary focus:border-primary shadow-sm" required>
                        <button type="button" onclick="togglePassword('current_password', 'eye-curr')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                            <i data-lucide="eye" id="eye-curr" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="new_password" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 pr-10 text-sm focus:ring-primary focus:border-primary shadow-sm" required minlength="8">
                        <button type="button" onclick="togglePassword('new_password', 'eye-new')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                            <i data-lucide="eye" id="eye-new" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <p class="text-xs text-mute mt-1">Gunakan kombinasi huruf, angka, dan karakter khusus (!@#$).</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-mute uppercase tracking-widest mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="new_password_confirmation" class="form-input w-full bg-white border-hairline rounded-xl py-2 px-3 pr-10 text-sm focus:ring-primary focus:border-primary shadow-sm" required minlength="8">
                        <button type="button" onclick="togglePassword('new_password_confirmation', 'eye-conf')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                            <i data-lucide="eye" id="eye-conf" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-hairline bg-canvas-soft flex justify-end gap-3">
                <button type="button" onclick="closePasswordModal()" class="btn-secondary-sm bg-white">Batal</button>
                <button type="submit" class="btn-primary-sm">Ubah Kata Sandi</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openProfileModal() {
        document.getElementById('profile-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeProfileModal() {
        document.getElementById('profile-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    function openPasswordModal() {
        document.getElementById('password-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closePasswordModal() {
        document.getElementById('password-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.outerHTML = `<i data-lucide="eye-off" id="${iconId}" class="w-4 h-4"></i>`;
        } else {
            input.type = 'password';
            icon.outerHTML = `<i data-lucide="eye" id="${iconId}" class="w-4 h-4"></i>`;
        }
        lucide.createIcons();
    }
</script>
@endpush
@endsection

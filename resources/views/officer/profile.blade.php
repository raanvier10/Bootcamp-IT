@extends('layouts.officer')
@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')
@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in-up">
    
    {{-- Header Profile Card --}}
    <div class="bg-canvas rounded-[24px] border border-hairline shadow-card-md overflow-hidden relative">
        <div class="h-32 bg-gradient-to-r from-primary to-[#094009] opacity-90 relative">
            <div class="absolute inset-0 bg-black/10"></div>
        </div>
        
        <div class="px-8 pb-8">
            <div class="relative -mt-16 mb-6 text-center sm:text-left">
                <div class="relative group inline-block mb-4">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatar-preview" class="w-32 h-32 rounded-full object-cover shadow-lg border-4 border-canvas bg-white transition-transform group-hover:scale-105 relative z-10">
                    <button type="button" onclick="document.getElementById('avatar').click()" class="absolute bottom-1 right-1 w-9 h-9 bg-white rounded-full shadow-md border border-hairline flex items-center justify-center text-primary hover:bg-canvas-soft hover:scale-110 transition-all cursor-pointer z-20">
                        <i data-lucide="camera" class="w-4.5 h-4.5"></i>
                    </button>
                </div>
                
                <div class="pb-2 text-center sm:text-left">
                    <h2 class="text-2xl font-black text-ink tracking-tight">{{ $user->name }}</h2>
                    <p class="text-sm font-medium text-mute mt-1">{{ $user->email }}</p>
                    <div class="flex items-center justify-center sm:justify-start gap-2 mt-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-soft text-primary text-[10px] sm:text-xs font-bold border border-primary/20">
                            <i data-lucide="shield-check" class="w-3 h-3"></i> Petugas Lapangan
                        </span>
                        <span class="text-xs text-mute font-medium hidden sm:inline-block">Sejak {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8">
        {{-- Left Column: Data Diri --}}
        <div class="md:col-span-7">
            <form method="POST" action="{{ route('officer.profile.update') }}" enctype="multipart/form-data" class="bg-canvas rounded-[24px] shadow-card-sm border border-hairline p-6 sm:p-8" id="profile-form">
                @csrf
                @method('PUT')
                
                <h3 class="text-lg font-bold text-ink mb-6 flex items-center gap-2 tracking-tight">
                    <div class="w-8 h-8 rounded-full bg-primary-soft flex items-center justify-center text-primary shrink-0">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    Informasi Pribadi
                </h3>
                
                <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                
                <div class="space-y-5">
                    @error('avatar')<p class="form-error">{{ $message }}</p>@enderror

                    <div>
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="user" class="w-4.5 h-4.5 text-mute"></i>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-input pl-10 h-11 rounded-xl" required>
                        </div>
                        @error('name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email_display" class="form-label">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="w-4.5 h-4.5 text-mute"></i>
                            </div>
                            <input type="email" id="email_display" value="{{ $user->email }}" class="form-input pl-10 h-11 rounded-xl bg-canvas-soft-2 text-mute border-dashed cursor-not-allowed" disabled>
                        </div>
                        <p class="text-[10px] text-mute mt-1.5 font-medium"><i data-lucide="info" class="w-3 h-3 inline mr-1"></i>Email digunakan untuk otentikasi login dan tidak dapat diubah.</p>
                    </div>

                    <div>
                        <label for="phone" class="form-label">Nomor Telepon / WhatsApp</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="phone" class="w-4.5 h-4.5 text-mute"></i>
                            </div>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->telepon) }}" class="form-input pl-10 h-11 rounded-xl" required placeholder="Contoh: 081234567890">
                        </div>
                        @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-hairline flex justify-end">
                    <button type="submit" class="btn-primary w-full sm:w-auto px-8 rounded-xl font-bold shadow-lg shadow-primary/30" id="btn-update-profile">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Column: Ganti Password --}}
        <div class="md:col-span-5">
            <form method="POST" action="{{ route('officer.profile.password') }}" class="bg-canvas rounded-[24px] shadow-card-sm border border-hairline p-6 sm:p-8" id="password-form">
                @csrf
                @method('PUT')
                
                <h3 class="text-lg font-bold text-ink mb-6 flex items-center gap-2 tracking-tight">
                    <div class="w-8 h-8 rounded-full bg-warning-soft flex items-center justify-center text-warning shrink-0">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    Keamanan Akun
                </h3>

                <div class="space-y-5">
                    <div>
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="key-round" class="w-4 h-4 text-mute"></i>
                            </div>
                            <input type="password" name="current_password" id="current_password" class="form-input pl-10 pr-10 h-11 rounded-xl" required placeholder="••••••••">
                            <button type="button" onclick="togglePassword('current_password', 'eye-curr')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                                <i data-lucide="eye" id="eye-curr" class="w-5 h-5"></i>
                            </button>
                        </div>
                        @error('current_password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="form-label">Password Baru</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="shield" class="w-4 h-4 text-mute"></i>
                            </div>
                            <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" class="form-input pl-10 pr-10 h-11 rounded-xl" required>
                            <button type="button" onclick="togglePassword('password', 'eye-new')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                                <i data-lucide="eye" id="eye-new" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <p class="text-[10px] text-mute mt-1.5 font-medium"><i data-lucide="info" class="w-3 h-3 inline mr-1"></i>Gunakan kombinasi huruf (huruf besar dan kecil), angka, dan karakter khusus (!@#$).</p>
                        @error('password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label">Ulangi Password Baru</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="shield-check" class="w-4 h-4 text-mute"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input pl-10 pr-10 h-11 rounded-xl" required placeholder="Minimal 8 karakter">
                            <button type="button" onclick="togglePassword('password_confirmation', 'eye-conf')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                                <i data-lucide="eye" id="eye-conf" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-hairline">
                    <button type="submit" class="w-full btn-secondary rounded-xl font-bold border-hairline-strong hover:bg-canvas-soft hover:text-ink transition-all shadow-sm" id="btn-update-password">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.outerHTML = `<i data-lucide="eye-off" id="${iconId}" class="w-5 h-5"></i>`;
        } else {
            input.type = 'password';
            icon.outerHTML = `<i data-lucide="eye" id="${iconId}" class="w-5 h-5"></i>`;
        }
        lucide.createIcons();
    }
</script>
@endpush
@endsection

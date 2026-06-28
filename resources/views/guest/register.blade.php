@extends('layouts.guest')
@section('title', 'Daftar')
@section('content')
<section class="py-16 lg:py-24">
    <div class="max-w-md mx-auto px-6">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="TrashReport Logo" class="h-12 w-auto" />
            </div>
            <h1 class="text-2xl font-semibold text-ink tracking-tight mb-2" style="letter-spacing:-0.8px">Buat akun TrashReport</h1>
            <p class="text-body">Daftar gratis untuk mulai melaporkan sampah liar.</p>
        </div>
        <div class="bg-canvas p-8 rounded-xl shadow-card-lg border border-hairline">
            <form method="POST" action="{{ route('register') }}" class="space-y-5" id="register-form">
                @csrf
                <div>
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Nama lengkap Anda" class="form-input" required autofocus>
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="email@example.com" class="form-input" required>
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" class="form-input" required>
                    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="form-label">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" class="form-input w-full pr-10" required>
                        <button type="button" onclick="togglePassword('password', 'eye-icon-1')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                            <i data-lucide="eye" id="eye-icon-1" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p class="text-xs text-mute mt-2">Gunakan kombinasi huruf (huruf besar dan kecil), angka, dan karakter khusus (!@#$)</p>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password" class="form-input w-full pr-10" required>
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                            <i data-lucide="eye" id="eye-icon-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full" id="register-submit-btn">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Daftar Sekarang
                </button>
            </form>
            <p class="text-center text-sm text-body mt-6">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary font-medium hover:underline">Masuk di sini</a></p>
        </div>
    </div>
</section>

@push('scripts')
<script>
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

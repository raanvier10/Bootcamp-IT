@extends('layouts.guest')
@section('title', 'Masuk')
@section('content')
<section class="py-16 lg:py-24">
    <div class="max-w-md mx-auto px-6">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="TrashReport Logo" class="h-12 w-auto" />
            </div>
            <h1 class="text-2xl font-semibold text-ink tracking-tight mb-2" style="letter-spacing:-0.8px">Masuk ke TrashReport</h1>
            <p class="text-body">Masuk untuk mulai melaporkan sampah liar di sekitar Anda.</p>
        </div>
        <div class="bg-canvas p-8 rounded-xl shadow-card-lg border border-hairline">
            <form method="POST" action="{{ route('login') }}" class="space-y-5" id="login-form">
                @csrf
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="email@example.com" class="form-input" required autofocus>
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="form-label">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Masukkan password" class="form-input w-full pr-10" required>
                        <button type="button" onclick="togglePassword('password', 'eye-icon')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                            <i data-lucide="eye" id="eye-icon" class="w-5 h-5"></i>
                        </button>
                    </div>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-body cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-hairline text-primary focus:ring-primary-soft">
                        Ingat saya
                    </label>
                </div>
                <button type="submit" class="btn-primary w-full" id="login-submit-btn">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Masuk
                </button>
            </form>
            <p class="text-center text-sm text-body mt-6">Belum punya akun? <a href="{{ route('register') }}" class="text-primary font-medium hover:underline">Daftar sekarang</a></p>
        </div>
        <div class="mt-6 p-4 bg-canvas-soft rounded-lg border border-hairline">
            <p class="text-xs text-mute text-center mb-2">Demo Account</p>
            <p class="text-xs text-body text-center">Email: <strong>budi@email.com</strong> | Password: <strong>password</strong></p>
        </div>
        <div class="mt-6 text-center">
            <a href="{{ route('admin.login') }}" class="text-xs text-mute hover:text-primary transition-colors font-medium"><i data-lucide="shield" class="w-3.5 h-3.5 inline mr-1 -mt-0.5"></i>Masuk sebagai Petugas / Admin</a>
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
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>
@endpush
@endsection

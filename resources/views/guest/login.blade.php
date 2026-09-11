@extends('layouts.guest')
@section('title', 'Masuk')
@section('content')
<section class="pt-4 pb-12 lg:pt-6 lg:pb-16">
    <div class="max-w-md mx-auto px-6">
        <div class="text-center mb-6">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="TrashReport Logo" class="h-12 w-auto" />
            </div>
            <h1 class="text-2xl font-semibold text-ink tracking-tight mb-2" style="letter-spacing:-0.8px">Masuk ke TrashReport</h1>
            <p class="text-body">Masuk ke akun Anda untuk mengakses sistem TrashReport.</p>
        </div>
        <div class="bg-canvas p-8 rounded-xl shadow-card-lg border border-hairline">
            @if(session('warning'))
                <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-start gap-2.5">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-2.5">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

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
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary hover:underline">Lupa Password?</a>
                </div>
                <button type="submit" class="btn-primary w-full" id="login-submit-btn">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Masuk
                </button>
            </form>
            <p class="text-center text-sm text-body mt-6">Belum punya akun? <a href="{{ route('register') }}" class="text-primary font-medium hover:underline">Daftar sekarang</a></p>
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

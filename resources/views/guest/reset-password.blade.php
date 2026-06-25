@extends('layouts.guest')
@section('title', 'Reset Password')
@section('content')
<section class="py-16 lg:py-24">
    <div class="max-w-md mx-auto px-6">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="TrashReport Logo" class="h-12 w-auto" />
            </div>
            <h1 class="text-2xl font-semibold text-ink tracking-tight mb-2" style="letter-spacing:-0.8px">Buat Password Baru</h1>
            <p class="text-body">Silakan buat password baru untuk akun Anda.</p>
        </div>

        <div class="bg-canvas p-8 rounded-xl shadow-card-lg border border-hairline">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="email" value="{{ session('reset_email') }}">
                <input type="hidden" name="token" value="{{ session('reset_token') }}">
                
                <div>
                    <label class="form-label">Email Terdaftar</label>
                    <input type="email" value="{{ session('reset_email') }}" class="form-input bg-canvas-soft text-mute cursor-not-allowed" disabled>
                </div>
                
                <div>
                    <label for="password" class="form-label">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" class="form-input w-full pr-10" required>
                        <button type="button" onclick="togglePassword('password', 'eye-icon-1')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                            <i data-lucide="eye" id="eye-icon-1" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p class="text-xs text-mute mt-2">Gunakan kombinasi huruf, angka, dan karakter khusus (!@#$)</p>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password" class="form-input w-full pr-10" required>
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')" class="absolute inset-y-0 right-0 px-3 flex items-center text-mute hover:text-ink transition-colors">
                            <i data-lucide="eye" id="eye-icon-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary w-full mt-4">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Simpan Password Baru
                </button>
            </form>
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

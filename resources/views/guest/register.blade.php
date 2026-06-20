@extends('layouts.guest')
@section('title', 'Daftar')
@section('content')
<section class="py-16 lg:py-24">
    <div class="max-w-md mx-auto px-6">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
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
                    <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" class="form-input" required>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password" class="form-input" required>
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
@endsection

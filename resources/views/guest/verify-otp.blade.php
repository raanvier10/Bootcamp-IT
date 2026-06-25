@extends('layouts.guest')
@section('title', 'Verifikasi OTP')
@section('content')
<section class="py-16 lg:py-24">
    <div class="max-w-md mx-auto px-6">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="TrashReport Logo" class="h-12 w-auto" />
            </div>
            <h1 class="text-2xl font-semibold text-ink tracking-tight mb-2" style="letter-spacing:-0.8px">Verifikasi Kode</h1>
            <p class="text-body">Masukkan 6 digit kode OTP yang telah dikirimkan ke email Anda.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-primary-soft text-primary rounded-xl border border-primary/20 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-canvas p-8 rounded-xl shadow-card-lg border border-hairline">
            <form method="POST" action="{{ route('password.verify.otp') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="email" value="{{ request('email') }}">
                
                <div>
                    <label for="token" class="form-label text-center block">Kode OTP (6 Angka)</label>
                    <input type="text" name="token" id="token" placeholder="123456" maxlength="6" class="form-input text-2xl" required autofocus style="letter-spacing: 12px; font-weight: bold; text-align: center; padding-left: 20px;">
                    @error('token')<p class="form-error text-center mt-2">{{ $message }}</p>@enderror
                </div>
                
                <button type="submit" class="btn-primary w-full mt-4">
                    Verifikasi Kode
                </button>
            </form>
            
            <div class="mt-6 text-center text-sm text-body">
                Belum menerima kode? <a href="{{ route('password.request') }}" class="text-primary font-medium hover:underline">Kirim Ulang</a>
            </div>
        </div>
    </div>
</section>
@endsection

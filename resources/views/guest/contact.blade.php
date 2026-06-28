@extends('layouts.guest')

@section('title', 'Kontak Kami')

@section('content')
<section class="py-12 lg:py-16">
    <div class="max-w-[1280px] mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h1 class="font-delight font-semibold text-3xl lg:text-4xl text-ink tracking-tight mb-4" style="letter-spacing: -1.5px">Hubungi kami</h1>
                <p class="text-lg text-body leading-relaxed mb-8">Punya pertanyaan, saran, atau kritik? Jangan ragu untuk menghubungi kami.</p>
                <div class="space-y-5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary-soft rounded-lg flex items-center justify-center flex-shrink-0"><i data-lucide="map-pin" class="w-5 h-5 text-primary"></i></div>
                        <div><p class="text-sm font-medium text-ink mb-1">Alamat</p><p class="text-sm text-body">Jl. Banten No. 1 41315 Karawang Barat Jawa Barat</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary-soft rounded-lg flex items-center justify-center flex-shrink-0"><i data-lucide="mail" class="w-5 h-5 text-primary"></i></div>
                        <div><p class="text-sm font-medium text-ink mb-1">Email</p><p class="text-sm text-body">contact@trashreport.web.id</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary-soft rounded-lg flex items-center justify-center flex-shrink-0"><i data-lucide="phone" class="w-5 h-5 text-primary"></i></div>
                        <div><p class="text-sm font-medium text-ink mb-1">Telepon</p><p class="text-sm text-body">085559443285</p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary-soft rounded-lg flex items-center justify-center flex-shrink-0"><i data-lucide="clock" class="w-5 h-5 text-primary"></i></div>
                        <div><p class="text-sm font-medium text-ink mb-1">Jam Operasional</p><p class="text-sm text-body">Senin — Jumat: 07.00 — 21.00 WIB</p></div>
                    </div>
                </div>
            </div>
            <div class="bg-canvas p-8 rounded-xl shadow-card-lg border border-hairline">
                <h2 class="text-xl font-semibold text-ink tracking-tight mb-6">Kirim pesan</h2>
                
                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5" id="contact-form">
                    @csrf
                    <div>
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Masukkan nama Anda" class="form-input" required>
                        @error('name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="email@example.com" class="form-input" required>
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="subject" class="form-label">Subjek</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" placeholder="Topik pesan Anda" class="form-input" required>
                        @error('subject')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="message" class="form-label">Pesan</label>
                        <textarea name="message" id="message" placeholder="Tulis pesan Anda..." class="form-textarea" rows="5" required>{{ old('message') }}</textarea>
                        @error('message')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary w-full" id="contact-submit-btn">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    Swal.fire({
        title: 'Terkirim!',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#0D530E',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'font-bold px-6 py-2.5 rounded-xl'
        }
    });
    @endif
});
</script>
@endpush
@endsection

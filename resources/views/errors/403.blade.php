<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Dibatasi | TrashReport</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#16a34a',
                            hover: '#15803d',
                            light: '#dcfce7',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen flex flex-col justify-between antialiased">
    <!-- Navbar Minimal -->
    <header class="w-full bg-white border-b border-gray-100 py-4 px-6">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="TrashReport Logo" class="h-8 w-auto" onerror="this.style.display='none'">
                <span class="text-xl font-bold text-gray-900 tracking-tight">TrashReport</span>
            </a>
            @auth
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="hidden sm:inline">Masuk sebagai:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ ucfirst(Auth::user()->peran ?? 'Pengguna') }}
                    </span>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-6">
        <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 p-6 sm:p-10 text-center relative overflow-hidden">
            <!-- Background Decorative Accent -->
            <div class="absolute -top-20 -right-20 w-48 h-48 bg-amber-50 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-emerald-50 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Icon -->
            <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 mb-6 shadow-sm">
                <i data-lucide="shield-alert" class="w-10 h-10"></i>
                <span class="absolute -bottom-1.5 -right-1.5 bg-amber-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full border-2 border-white">
                    403
                </span>
            </div>

            <!-- Title & Description -->
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2 tracking-tight">
                Akses Dibatasi
            </h1>
            <p class="text-gray-500 text-sm sm:text-base mb-6 leading-relaxed">
                Halaman ini memiliki pembatasan hak akses dan tidak dapat dibuka dengan akun yang sedang aktif.
            </p>

            <!-- User Context Card -->
            @auth
                @php
                    $role = strtolower(Auth::user()->peran ?? 'pelapor');
                    $dashboardUrl = url('/user/dashboard');
                    $dashboardLabel = 'Dashboard Pelapor';

                    if ($role === 'admin') {
                        $dashboardUrl = url('/admin/dashboard');
                        $dashboardLabel = 'Dashboard Admin';
                    } elseif ($role === 'petugas' || $role === 'officer') {
                        $dashboardUrl = url('/officer/dashboard');
                        $dashboardLabel = 'Dashboard Petugas';
                    }
                @endphp

                <div class="bg-gray-50 rounded-2xl p-4 sm:p-5 text-left border border-gray-200/70 mb-6 space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200/60">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Status Login Anda</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $role === 'admin' ? 'bg-purple-100 text-purple-700' : ($role === 'petugas' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                            <i data-lucide="{{ $role === 'admin' ? 'shield-check' : ($role === 'petugas' ? 'truck' : 'user') }}" class="w-3.5 h-3.5"></i>
                            {{ ucfirst(Auth::user()->peran ?? 'Pelapor') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <img src="{{ Auth::user()->avatar_url ?? Auth::user()->foto_profil_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name ?? 'User') }}" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-200 object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name ?? 'Pengguna' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="text-xs text-amber-800 bg-amber-50/80 rounded-xl p-3 border border-amber-200/60 flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                        <span>
                            Jika Anda bermaksud mengelola sebagai <strong>Admin / Petugas</strong>, silakan klik tombol <strong>Ganti Akun</strong> di bawah untuk masuk dengan email yang sesuai.
                        </span>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-2xl p-4 text-left border border-gray-200/70 mb-6">
                    <p class="text-sm text-gray-600 flex items-center gap-2">
                        <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        Anda belum masuk ke sistem. Silakan login terlebih dahulu.
                    </p>
                </div>
            @endauth

            <!-- Action Buttons -->
            <div class="space-y-2.5">
                @auth
                    <!-- Back to Dashboard -->
                    <a href="{{ $dashboardUrl }}" class="w-full inline-flex items-center justify-center gap-2 py-3 px-5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition shadow-sm shadow-emerald-200">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Kembali ke {{ $dashboardLabel }}
                    </a>

                    <!-- Switch Account / Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3 px-5 rounded-xl text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 active:bg-gray-100 transition shadow-sm">
                            <i data-lucide="log-out" class="w-4 h-4 text-gray-500"></i>
                            Ganti Akun (Logout)
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 py-3 px-5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition shadow-sm shadow-emerald-200">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Halaman Login
                    </a>
                @endauth

                <!-- Back to Home -->
                <a href="{{ url('/') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-5 rounded-xl text-xs font-medium text-gray-500 hover:text-gray-900 transition">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} TrashReport. Seluruh hak cipta dilindungi.
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

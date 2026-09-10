<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | TrashReport</title>
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
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-6">
        <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 p-6 sm:p-10 text-center relative overflow-hidden">
            <!-- Background Decorative Accent -->
            <div class="absolute -top-20 -right-20 w-48 h-48 bg-emerald-50 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Icon -->
            <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gray-100 border border-gray-200 text-gray-600 mb-6 shadow-sm">
                <i data-lucide="compass" class="w-10 h-10"></i>
                <span class="absolute -bottom-1.5 -right-1.5 bg-gray-700 text-white text-[11px] font-bold px-2 py-0.5 rounded-full border-2 border-white">
                    404
                </span>
            </div>

            <!-- Title & Description -->
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2 tracking-tight">
                Halaman Tidak Ditemukan
            </h1>
            <p class="text-gray-500 text-sm sm:text-base mb-6 leading-relaxed">
                Tautan yang Anda tuju mungkin salah ketik, sudah dipindahkan, atau tidak tersedia.
            </p>

            <!-- Action Buttons -->
            <div class="space-y-2.5">
                <a href="{{ url('/') }}" class="w-full inline-flex items-center justify-center gap-2 py-3 px-5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition shadow-sm shadow-emerald-200">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    Kembali ke Beranda
                </a>
                <button onclick="window.history.back()" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-5 rounded-xl text-xs font-medium text-gray-500 hover:text-gray-900 transition">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Kembali ke Halaman Sebelumnya
                </button>
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

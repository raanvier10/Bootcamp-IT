<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'TrashReport — Sistem pelaporan sampah liar berbasis komunitas. Laporkan titik sampah, jaga kebersihan lingkungan kita bersama.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TrashReport') — Laporkan Sampah, Jaga Bumi</title>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing.jsx'])

    @stack('styles')
</head>
<body class="bg-[#F8F9FA] font-delight text-gray-800 antialiased selection:bg-primary selection:text-white">
    {{-- ── Navbar ── --}}
    <nav id="navbar" class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-100 transition-all">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-20">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group" id="nav-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="TrashReport Logo" class="h-10 w-auto transition-transform group-hover:scale-105" />
                    <span class="text-gray-900 font-bold text-xl tracking-tight">TrashReport</span>
                </a>

                {{-- Nav Links (Desktop) --}}
                <div class="hidden md:flex items-center gap-8" id="nav-links-desktop">
                    <a href="{{ route('home') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-primary' : 'text-gray-600 hover:text-primary' }}">Beranda</a>
                    <a href="{{ route('map') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('map') ? 'text-primary' : 'text-gray-600 hover:text-primary' }}">Peta Sampah</a>
                    <a href="{{ route('articles') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('articles', 'article.detail') ? 'text-primary' : 'text-gray-600 hover:text-primary' }}">Artikel</a>
                    <a href="{{ route('contact') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('contact') ? 'text-primary' : 'text-gray-600 hover:text-primary' }}">Kontak</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="hidden md:flex items-center gap-3" id="nav-auth-buttons">
                    @auth
                        <div class="flex items-center gap-3">
                            <a href="{{ auth()->user()->isAdmin() ? '/admin/dashboard' : (auth()->user()->isOfficer() ? '/officer/dashboard' : route('user.dashboard')) }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2"></i> Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center p-2 text-gray-400 bg-white border border-gray-200 rounded-lg hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-colors shadow-sm" title="Keluar">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary-deep transition-colors shadow-sm shadow-primary/20">Daftar</a>
                    @endauth
                </div>

                {{-- Mobile Hamburger --}}
                <button class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-600 relative w-10 h-10 flex items-center justify-center" id="mobile-menu-toggle" onclick="toggleMobileMenu()">
                    <span id="icon-menu" class="absolute transition-all duration-300 transform opacity-100 rotate-0 scale-100 flex items-center justify-center">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </span>
                    <span id="icon-close" class="absolute transition-all duration-300 transform opacity-0 rotate-90 scale-50 flex items-center justify-center">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </span>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div class="md:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg origin-top transition-all duration-300 ease-out opacity-0 -translate-y-4 pointer-events-none" id="mobile-menu">
            <div class="px-6 py-4 space-y-2">
                <a href="{{ route('home') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('home') ? 'bg-primary-soft text-primary' : 'text-gray-600 hover:bg-gray-50' }}">Beranda</a>
                <a href="{{ route('map') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('map') ? 'bg-primary-soft text-primary' : 'text-gray-600 hover:bg-gray-50' }}">Peta Sampah</a>
                <a href="{{ route('articles') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('articles', 'article.detail') ? 'bg-primary-soft text-primary' : 'text-gray-600 hover:bg-gray-50' }}">Artikel</a>
                <a href="{{ route('contact') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('contact') ? 'bg-primary-soft text-primary' : 'text-gray-600 hover:bg-gray-50' }}">Kontak</a>

                <div class="border-t border-gray-100 pt-4 mt-4 flex flex-col gap-3">
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? '/admin/dashboard' : (auth()->user()->isOfficer() ? '/officer/dashboard' : route('user.dashboard')) }}" class="inline-flex items-center justify-center px-4 py-3 w-full text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-3 w-full text-sm font-bold text-red-600 bg-red-50 border border-red-100 rounded-xl">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-3 w-full text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-3 w-full text-sm font-bold text-white bg-primary rounded-xl">Daftar Gratis</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ── Main Content ── --}}
    <main class="pt-20">
        @yield('content')
    </main>

    {{-- ── Footer ── --}}
    <footer id="footer" class="bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/logo.png') }}" alt="TrashReport Logo" class="h-10 w-auto" />
                        <span class="text-gray-900 font-bold text-xl tracking-tight">TrashReport</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">Sistem pelaporan sampah liar berbasis komunitas untuk lingkungan yang lebih bersih dan sehat.</p>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-wider">Navigasi</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">Beranda</a></li>
                        <li><a href="{{ route('map') }}" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">Peta Sampah</a></li>
                        <li><a href="{{ route('articles') }}" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">Artikel Edukasi</a></li>
                        <li><a href="{{ route('contact') }}" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">Kontak Kami</a></li>
                    </ul>
                </div>

                {{-- Account --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-wider">Akun</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">Daftar Gratis</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-wider">Hubungi Kami</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0 text-primary">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <span class="mt-1.5">contact@trashreport.web.id</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0 text-primary">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                            <span class="mt-1.5">085559443285</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0 text-primary">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </div>
                            <span class="mt-1.5">Jl. Banten No. 1 41315 Karawang Barat Jawa Barat</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-100 mt-16 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm font-medium text-gray-400">&copy; {{ date('Y') }} TrashReport. Hak cipta dilindungi.</p>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                    <p class="text-sm font-medium text-gray-400">Dibangun untuk bumi yang lebih hijau.</p>
                </div>
            </div>
        </div>
    </footer>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const iconMenu = document.getElementById('icon-menu');
            const iconClose = document.getElementById('icon-close');
            
            if (!menu || !iconMenu || !iconClose) return;

            const isOpen = menu.classList.contains('opacity-100');

            if (isOpen) {
                // Close menu
                menu.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
                menu.classList.add('opacity-0', '-translate-y-4', 'pointer-events-none');

                iconMenu.classList.remove('opacity-0', '-rotate-90', 'scale-50');
                iconMenu.classList.add('opacity-100', 'rotate-0', 'scale-100');
                
                iconClose.classList.remove('opacity-100', 'rotate-0', 'scale-100');
                iconClose.classList.add('opacity-0', 'rotate-90', 'scale-50');
            } else {
                // Open menu
                menu.classList.remove('opacity-0', '-translate-y-4', 'pointer-events-none');
                menu.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');

                iconMenu.classList.remove('opacity-100', 'rotate-0', 'scale-100');
                iconMenu.classList.add('opacity-0', '-rotate-90', 'scale-50');
                
                iconClose.classList.remove('opacity-0', 'rotate-90', 'scale-50');
                iconClose.classList.add('opacity-100', 'rotate-0', 'scale-100');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>

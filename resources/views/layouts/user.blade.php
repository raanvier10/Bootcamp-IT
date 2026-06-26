<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Pelapor — TrashReport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — TrashReport</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Force Leaflet maps to stay under Tailwind's z-30 header and z-40 sidebar */
        .leaflet-pane,
        .leaflet-control,
        .leaflet-top,
        .leaflet-bottom {
            z-index: 10 !important;
        }
        .leaflet-container {
            z-index: 10 !important;
        }
    </style>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-canvas-soft font-jakarta text-body">
    <div class="flex min-h-screen">
        {{-- ── Sidebar ── --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-60 bg-canvas border-r border-hairline flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
            {{-- Logo --}}
            <div class="flex items-center gap-2.5 px-5 h-16 border-b border-hairline">
                <img src="{{ asset('images/logo.png') }}" alt="TrashReport Logo" class="h-8 w-auto" />
                <span class="text-ink font-semibold text-sm tracking-tight">TrashReport</span>
            </div>

            {{-- Nav Menu --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p class="px-3 text-xs font-medium text-mute uppercase tracking-wider mb-3">Menu Utama</p>

                <a href="{{ route('user.dashboard') }}" class="sidebar-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" id="sidebar-dashboard">
                    <i data-lucide="layout-dashboard" class="w-4.5 h-4.5"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('user.report.create') }}" class="sidebar-item {{ request()->routeIs('user.report.create') ? 'active' : '' }}" id="sidebar-create-report">
                    <i data-lucide="camera" class="w-4.5 h-4.5"></i>
                    <span>Buat Laporan</span>
                </a>

                <a href="{{ route('user.reports') }}" class="sidebar-item {{ request()->routeIs('user.reports', 'user.report.detail') ? 'active' : '' }}" id="sidebar-reports">
                    <i data-lucide="file-text" class="w-4.5 h-4.5"></i>
                    <span>Riwayat Laporan</span>
                </a>

                <a href="{{ route('user.notifications') }}" class="sidebar-item {{ request()->routeIs('user.notifications') ? 'active' : '' }}" id="sidebar-notifications">
                    <i data-lucide="bell" class="w-4.5 h-4.5"></i>
                    <span>Notifikasi</span>
                    @if(auth()->user()->notifikasi()->belumDibaca()->count() > 0)
                        <span class="ml-auto bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Baru</span>
                    @endif
                </a>

                <div class="pt-4 mt-4 border-t border-hairline">
                    <p class="px-3 text-xs font-medium text-mute uppercase tracking-wider mb-3">Lainnya</p>

                    <a href="{{ route('user.profile') }}" class="sidebar-item {{ request()->routeIs('user.profile') ? 'active' : '' }}" id="sidebar-profile">
                        <i data-lucide="user" class="w-4.5 h-4.5"></i>
                        <span>Profil Saya</span>
                    </a>

                    <a href="{{ route('home') }}" class="sidebar-item" id="sidebar-home">
                        <i data-lucide="globe" class="w-4.5 h-4.5"></i>
                        <span>Beranda Publik</span>
                    </a>
                </div>
            </nav>

            {{-- User Profile --}}
            <div class="px-3 py-4 border-t border-hairline">
                <div class="flex items-center gap-3 px-3 py-2">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-mute truncate">Pelapor</p>
                    </div>
                    <a href="{{ route('logout') }}" class="p-1.5 rounded-md hover:bg-error-soft text-mute hover:text-error transition-colors" title="Keluar" id="sidebar-logout">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </aside>

        {{-- ── Main Area ── --}}
        <div class="flex-1 lg:ml-60">
            {{-- Header --}}
            <header id="user-header" class="sticky top-0 z-30 bg-canvas border-b border-hairline h-16 flex items-center px-6">
                {{-- Mobile menu toggle --}}
                <button class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-canvas-soft-2 mr-3" onclick="toggleSidebar()" id="sidebar-toggle">
                    <i data-lucide="menu" class="w-5 h-5 text-ink"></i>
                </button>

                <div class="flex-1">
                    <h1 class="text-lg font-semibold text-ink tracking-tight">@yield('page_title', 'Dashboard')</h1>
                </div>

                {{-- Header Right --}}
                <div class="flex items-center gap-3">
                    {{-- Notification bell --}}
                    <a href="{{ route('user.notifications') }}" class="p-2 rounded-lg hover:bg-canvas-soft-2 relative transition-colors" id="notification-bell">
                        <i data-lucide="bell" class="w-5 h-5 text-body"></i>
                        @if(auth()->user()->notifikasi()->belumDibaca()->count() > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full"></span>
                        @endif
                    </a>

                    <div class="hidden sm:flex items-center gap-2.5 pl-3 border-l border-hairline">
                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm font-medium text-ink">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="px-6 pt-4">
                    <div class="alert-success animate-fade-in" id="flash-success">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="px-6 pt-4">
                    <div class="alert-error animate-fade-in" id="flash-error">
                        <div class="flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Page Content --}}
            <main class="p-6">
                @yield('content')
            </main>
        </div>

        {{-- Sidebar Overlay (mobile) --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>
    </div>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Auto-dismiss flash messages
        setTimeout(function() {
            const flash = document.querySelectorAll('#flash-success, #flash-error');
            flash.forEach(el => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>

    {{-- Global Image Lightbox --}}
    <div id="image-lightbox" class="fixed inset-0 bg-black/90 hidden items-center justify-center opacity-0 transition-opacity duration-300 backdrop-blur-sm" style="z-index: 99999;" onclick="closeLightbox()">
        <button type="button" class="absolute top-6 right-6 w-12 h-12 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/80 transition-colors z-10" onclick="closeLightbox()">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <img id="lightbox-img" src="" alt="Enlarged View" class="object-contain rounded-xl shadow-2xl scale-95 transition-transform duration-300" style="max-width: 90vw; max-height: 90vh;" onclick="event.stopPropagation()">
    </div>
    <script>
        function openLightbox(imgSrc) {
            const lightbox = document.getElementById('image-lightbox');
            const img = document.getElementById('lightbox-img');
            img.src = imgSrc;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            void lightbox.offsetWidth; // Trigger reflow
            lightbox.classList.remove('opacity-0');
            img.classList.remove('scale-95');
            img.classList.add('scale-100');
        }
        function closeLightbox() {
            const lightbox = document.getElementById('image-lightbox');
            const img = document.getElementById('lightbox-img');
            lightbox.classList.add('opacity-0');
            img.classList.remove('scale-100');
            img.classList.add('scale-95');
            setTimeout(() => {
                lightbox.classList.add('hidden');
                lightbox.classList.remove('flex');
                img.src = '';
            }, 300);
        }
    </script>

    @stack('scripts')
</body>
</html>

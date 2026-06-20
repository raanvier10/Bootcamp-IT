<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Admin — TrashReport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — TrashReport</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-canvas-soft font-jakarta text-body">
    <div class="flex min-h-screen">
        {{-- ── Sidebar ── --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-canvas border-r border-hairline flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-6 h-16 border-b border-hairline">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-black/20">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                    </svg>
                </div>
                <span class="text-ink font-bold text-lg tracking-tight">TrashReport</span>
            </div>

            {{-- Nav Menu --}}
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <p class="px-3 text-xs font-medium text-mute uppercase tracking-wider mb-3">Menu Utama</p>

                <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" id="sidebar-dashboard">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard Admin</span>
                </a>

                <a href="{{ route('admin.reports') }}" class="sidebar-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" id="sidebar-reports">
                    <i data-lucide="file-check-2" class="w-5 h-5"></i>
                    <span>Verifikasi Laporan</span>
                </a>

                <a href="{{ route('admin.officers') }}" class="sidebar-item {{ request()->routeIs('admin.officers*') ? 'active' : '' }}" id="sidebar-officers">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Kelola Petugas</span>
                </a>

                <a href="{{ route('admin.artikel.index') }}" class="sidebar-item {{ request()->routeIs('admin.artikel*') ? 'active' : '' }}" id="sidebar-articles">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span>Kelola Artikel</span>
                </a>

                <div class="pt-4 mt-4 border-t border-hairline">
                    <p class="px-3 text-xs font-medium text-mute uppercase tracking-wider mb-3">Master Data</p>

                    <a href="{{ route('admin.districts') }}" class="sidebar-item {{ request()->routeIs('admin.districts*') ? 'active' : '' }}" id="sidebar-districts">
                        <i data-lucide="map" class="w-5 h-5"></i>
                        <span>Wilayah</span>
                    </a>

                    <a href="{{ route('admin.categories') }}" class="sidebar-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" id="sidebar-categories">
                        <i data-lucide="tags" class="w-5 h-5"></i>
                        <span>Kategori Sampah</span>
                    </a>

                    <a href="{{ route('admin.messages') }}" class="sidebar-item {{ request()->routeIs('admin.messages*') ? 'active' : '' }}" id="sidebar-messages">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                        <span>Pesan Publik</span>
                    </a>
                </div>

                <div class="pt-4 mt-4 border-t border-hairline">
                    <p class="px-3 text-xs font-medium text-mute uppercase tracking-wider mb-3">Lainnya</p>

                    <a href="{{ route('admin.profile') }}" class="sidebar-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}" id="sidebar-profile">
                        <i data-lucide="user" class="w-5 h-5"></i>
                        <span>Profil Saya</span>
                    </a>

                    <a href="{{ route('home') }}" class="sidebar-item" id="sidebar-home">
                        <i data-lucide="globe" class="w-5 h-5"></i>
                        <span>Beranda Publik</span>
                    </a>
                </div>
            </nav>

            {{-- User Profile --}}
            <div class="px-4 py-4 border-t border-hairline bg-canvas">
                <div class="flex items-center gap-3 px-3 py-2">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->nama ?? 'Admin').'&background=000&color=fff' }}" alt="Profile" class="w-9 h-9 rounded-full object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-ink truncate">{{ auth()->user()->nama ?? 'Admin' }}</p>
                        <p class="text-xs text-mute truncate">Administrator</p>
                    </div>
                    <a href="{{ route('logout') }}" class="p-1.5 rounded-md hover:bg-error-soft text-mute hover:text-error transition-colors" title="Keluar" id="sidebar-logout">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </aside>

        {{-- ── Main Area ── --}}
        <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
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
                    <div class="hidden sm:flex items-center gap-2.5 pl-3 border-l border-hairline">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->nama ?? 'Admin').'&background=000&color=fff' }}" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm font-medium text-ink">{{ auth()->user()->nama ?? 'Admin' }}</span>
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
            <main class="flex-1 p-6">
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

    @stack('scripts')
</body>
</html>


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#16a34a">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="POS Tani">
        <link rel="apple-touch-icon" href="/pwa-icon.png">
        <link rel="manifest" href="/manifest.json">

        <title>{{ config('app.name', 'POS Toko Tani') }} - @yield('title', 'Dashboard')</title>
        <meta name="description" content="Aplikasi POS Toko Pertanian - Kelola penjualan, pembelian, dan stok toko pertanian Anda">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Flatpickr CSS & Select2 CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <style>
            /* Custom styling for Select2 to match Clean Glassmorphism theme */
            .select2-container--default .select2-selection--single {
                background-color: rgba(255, 255, 255, 0.7) !important;
                border: 1.5px solid rgba(209, 213, 219, 0.6) !important;
                border-radius: 12px !important;
                height: 46px !important;
                backdrop-filter: blur(8px) !important;
                display: flex !important;
                align-items: center !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #1F2937 !important;
                font-size: 0.875rem !important;
                padding-left: 12px !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 44px !important;
                right: 8px !important;
            }
            .select2-dropdown {
                background-color: rgba(255, 255, 255, 0.95) !important;
                border: 1px solid rgba(255, 255, 255, 0.5) !important;
                border-radius: 12px !important;
                box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08) !important;
                backdrop-filter: blur(20px) !important;
                padding: 4px !important;
                z-index: 9999 !important;
            }
            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: #16a34a !important;
                border-radius: 8px !important;
            }
            .select2-container--default .select2-results__option {
                padding: 8px 12px !important;
                font-size: 0.875rem !important;
                border-radius: 8px !important;
            }
            .select2-search--dropdown .select2-search__field {
                border: 1.5px solid rgba(209, 213, 219, 0.6) !important;
                border-radius: 8px !important;
                padding: 6px 10px !important;
                outline: none !important;
            }
            /* Flatpickr customization */
            .flatpickr-calendar {
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(20px) !important;
                border: 1px solid rgba(255, 255, 255, 0.5) !important;
                box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08) !important;
                border-radius: 16px !important;
            }
            .flatpickr-day.selected, .flatpickr-day.selected:hover {
                background: #16a34a !important;
                border-color: #16a34a !important;
            }
        </style>

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen" style="background: linear-gradient(135deg, #FAFAF9 0%, #f0fdf4 30%, #FAFAF9 60%, #fff7ed 100%);">

            {{-- Top Header --}}
            <header class="sticky top-0 z-30 glass-nav">
                <div class="max-w-lg mx-auto px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-float">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-base font-bold text-dark leading-tight">POS Toko Tani</h1>
                            <p class="text-[10px] text-gray-400 font-medium">Sistem Kasir Pertanian</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- Profile Menu --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="w-9 h-9 rounded-xl bg-white/60 border border-white/40 flex items-center justify-center transition-all hover:bg-white/80">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 glass-card-solid p-2 z-50">
                                <div class="px-3 py-2 border-b border-gray-100 mb-1">
                                    <p class="text-sm font-semibold text-dark">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Profil
                                </a>
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                    Pengaturan
                                </a>
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="fixed top-16 left-4 right-4 z-50 max-w-lg mx-auto">
                    <div class="glass-card-solid px-4 py-3 flex items-center gap-3 border-l-4 border-primary-500">
                        <svg class="w-5 h-5 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-dark">{{ session('success') }}</p>
                        <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition
                     class="fixed top-16 left-4 right-4 z-50 max-w-lg mx-auto">
                    <div class="glass-card-solid px-4 py-3 flex items-center gap-3 border-l-4 border-red-500">
                        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-dark">{{ session('error') }}</p>
                        <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endif

            @if (isset($header))
                <div class="max-w-lg mx-auto px-4 pt-4">
                    {{ $header }}
                </div>
            @endif

            {{-- Page Content --}}
            <main class="max-w-lg mx-auto px-4 pb-24 pt-2 page-enter">
                @yield('content')
                {{ $slot ?? '' }}
            </main>

            {{-- Floating Bottom Navigation --}}
            <nav class="fixed bottom-0 left-0 right-0 z-40 px-4 pb-4">
                <div class="max-w-lg mx-auto glass-nav rounded-2xl px-2 py-1">
                    <div class="flex items-center justify-around">
                        {{-- Dashboard --}}
                        <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-primary-50/80' : 'hover:bg-gray-50/50' }}">
                            <div class="relative">
                                <svg class="w-6 h-6 transition-colors {{ request()->routeIs('dashboard') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('dashboard') ? '2.5' : '2' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                @if(request()->routeIs('dashboard'))
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-accent-500 rounded-full"></span>
                                @endif
                            </div>
                            <span class="text-[10px] mt-1 font-semibold {{ request()->routeIs('dashboard') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">Beranda</span>
                        </a>

                        {{-- Jual (POS) --}}
                        <a href="{{ route('sales.create') }}" class="flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('sales.*') ? 'bg-primary-50/80' : 'hover:bg-gray-50/50' }}">
                            <div class="relative">
                                <svg class="w-6 h-6 transition-colors {{ request()->routeIs('sales.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('sales.*') ? '2.5' : '2' }}" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                @if(request()->routeIs('sales.*'))
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-accent-500 rounded-full"></span>
                                @endif
                            </div>
                            <span class="text-[10px] mt-1 font-semibold {{ request()->routeIs('sales.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">Jual</span>
                        </a>

                        {{-- Beli --}}
                        <a href="{{ route('purchases.index') }}" class="flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('purchases.*') ? 'bg-primary-50/80' : 'hover:bg-gray-50/50' }}">
                            <div class="relative">
                                <svg class="w-6 h-6 transition-colors {{ request()->routeIs('purchases.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('purchases.*') ? '2.5' : '2' }}" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                @if(request()->routeIs('purchases.*'))
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-accent-500 rounded-full"></span>
                                @endif
                            </div>
                            <span class="text-[10px] mt-1 font-semibold {{ request()->routeIs('purchases.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">Beli</span>
                        </a>

                        {{-- Laporan --}}
                        <a href="{{ route('reports.sales') }}" class="flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('reports.*') ? 'bg-primary-50/80' : 'hover:bg-gray-50/50' }}">
                            <div class="relative">
                                <svg class="w-6 h-6 transition-colors {{ request()->routeIs('reports.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('reports.*') ? '2.5' : '2' }}" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                @if(request()->routeIs('reports.*'))
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-accent-500 rounded-full"></span>
                                @endif
                            </div>
                            <span class="text-[10px] mt-1 font-semibold {{ request()->routeIs('reports.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">Laporan</span>
                        </a>

                        {{-- More --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('products.*', 'suppliers.*', 'customers.*', 'categories.*', 'units.*', 'payments.*', 'settings.*', 'cash-transactions.*') ? 'bg-primary-50/80' : 'hover:bg-gray-50/50' }}">
                                <svg class="w-6 h-6 transition-colors {{ request()->routeIs('products.*', 'suppliers.*', 'customers.*', 'categories.*', 'units.*', 'payments.*', 'settings.*', 'cash-transactions.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span class="text-[10px] mt-1 font-semibold {{ request()->routeIs('products.*', 'suppliers.*', 'customers.*', 'categories.*', 'units.*', 'payments.*', 'settings.*', 'cash-transactions.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">Lainnya</span>
                            </button>
                            {{-- More Menu Popup --}}
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-4"
                                 class="absolute bottom-16 right-0 w-56 glass-card-solid p-2 z-50">
                                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('products.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">📦</span> Produk
                                </a>
                                <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('categories.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">🏷️</span> Kategori
                                </a>
                                <a href="{{ route('units.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('units.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">📏</span> Satuan
                                </a>
                                <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('suppliers.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">🤝</span> Tengkulak
                                </a>
                                <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('customers.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">👥</span> Pelanggan
                                </a>
                                <a href="{{ route('galleries.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('galleries.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">🖼️</span> Galeri
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('payments.suppliers') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('payments.suppliers*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">💸</span> Hutang Tengkulak
                                </a>
                                <a href="{{ route('payments.customers') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('payments.customers*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">💰</span> Piutang Pelanggan
                                </a>
                                <a href="{{ route('cash-transactions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('cash-transactions.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">🏦</span> Kas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        {{-- Global Alpine.js helper for currency formatting --}}
        <script>
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(number);
            }

            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }
        </script>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize Select2 globally on all standard select elements
                function initSelect2() {
                    $('select:not(.no-select2)').each(function() {
                        if (!$(this).hasClass("select2-hidden-accessible")) {
                            $(this).select2({
                                width: '100%'
                            });
                        }
                    });
                }
                
                // Initialize Flatpickr globally on all date input fields
                function initFlatpickr() {
                    $('.datepicker:not([x-init])').each(function() {
                        if (!$(this).hasClass("flatpickr-input")) {
                            flatpickr(this, {
                                locale: 'id',
                                dateFormat: 'Y-m-d',
                                allowInput: true,
                                altInput: true,
                                altFormat: 'd F Y',
                                disableMobile: true
                            });
                        }
                    });
                }

                initSelect2();
                initFlatpickr();

                // Make these globally accessible to run on demand (e.g. dynamic elements)
                window.reinitSelect2 = initSelect2;
                window.reinitFlatpickr = initFlatpickr;
            });
        </script>

        <!-- Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('Service Worker registered', reg))
                        .catch(err => console.error('Service Worker registration failed', err));
                });
            }
        </script>

        @stack('scripts')
    </body>
</html>

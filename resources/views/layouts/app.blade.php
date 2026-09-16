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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Flatpickr CSS & Select2 CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">

        <style>
            .swal2-container {
                z-index: 999999 !important;
            }
            .select2-container {
                max-width: 100% !important;
            }
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
                background: #FFFFFF !important;
                border: 1px solid #D1D5DB !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                border-radius: 12px !important;
            }
            .flatpickr-months {
                background-color: #F3F4F6 !important;
                border-bottom: 1px solid #E5E7EB !important;
                border-radius: 12px 12px 0 0 !important;
            }
            .flatpickr-current-month {
                color: #1F2937 !important;
            }
            .flatpickr-current-month .flatpickr-monthDropdown-months {
                font-weight: 700 !important;
                color: #1F2937 !important;
            }
            .flatpickr-current-month .flatpickr-monthDropdown-months option {
                color: #1F2937 !important;
                background-color: #FFFFFF !important;
            }
            .flatpickr-current-month input.cur-year {
                font-weight: 700 !important;
                color: #1F2937 !important;
            }
            .flatpickr-months .flatpickr-prev-month, 
            .flatpickr-months .flatpickr-next-month {
                color: #4B5563 !important;
                fill: #4B5563 !important;
            }
            .flatpickr-months .flatpickr-prev-month:hover, 
            .flatpickr-months .flatpickr-next-month:hover {
                color: #16A34A !important;
                fill: #16A34A !important;
            }
            .flatpickr-months .flatpickr-prev-month:hover svg, 
            .flatpickr-months .flatpickr-next-month:hover svg {
                fill: #16A34A !important;
            }
            .flatpickr-weekdaycontainer {
                padding: 4px 0 !important;
                background-color: #F9FAFB !important;
                border-bottom: 1px solid #E5E7EB !important;
            }
            span.flatpickr-weekday {
                color: #4B5563 !important;
                font-weight: 600 !important;
            }
            .flatpickr-day {
                color: #1F2937 !important;
                border-radius: 6px !important;
            }
            .flatpickr-day.today {
                border-color: #16A34A !important;
                color: #16A34A !important;
                font-weight: 700 !important;
            }
            .flatpickr-day.today:hover {
                background-color: #F0FDF4 !important;
                color: #16A34A !important;
            }
            .flatpickr-day.selected, 
            .flatpickr-day.selected:focus, 
            .flatpickr-day.selected:hover {
                background-color: #16A34A !important;
                border-color: #16A34A !important;
                color: #FFFFFF !important;
                font-weight: 700 !important;
            }
            .flatpickr-day:hover {
                background-color: #F3F4F6 !important;
            }
        </style>

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen" style="background: linear-gradient(135deg, #FAFAF9 0%, #f0fdf4 30%, #FAFAF9 60%, #fff7ed 100%);">

            {{-- Desktop Sidebar Navigation (Hidden on mobile < 768px, visible on md: and up) --}}
            <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-40 bg-white/85 backdrop-blur-xl border-r border-gray-200/60 shadow-sm select-none">
                {{-- Sidebar Header --}}
                <div class="h-16 flex items-center gap-3 px-6 border-b border-gray-150/60">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-float flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-sm font-bold text-dark leading-tight truncate">POS Toko Tani</h1>
                        <p class="text-[10px] text-gray-400 font-medium truncate">Sistem Kasir Pertanian</p>
                    </div>
                </div>

                {{-- Sidebar Navigation Items --}}
                <div class="flex-1 overflow-y-auto px-3 py-4 space-y-4 scrollbar-thin">
                    {{-- Group: Transaksi --}}
                    <div>
                        <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Menu Utama</p>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V10.5Z" fill="currentColor"/>
                                    <path d="M3 10.5L12 3L21 10.5M9 21V14H15V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Beranda</span>
                            </a>
                            <a href="{{ route('sales.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('sales.create') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M5.4 5L7 13H17L21 5H5.4Z" fill="currentColor"/>
                                    <path d="M3 3H5.4L7 13H17L21 5H5.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="9" cy="20" r="1.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                                    <circle cx="17" cy="20" r="1.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                                </svg>
                                <span>Kasir Jual Baru</span>
                            </a>
                            <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('sales.index', 'sales.show') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M4 4C4 2.89543 4.89543 2 6 2H18C19.1046 2 20 2.89543 20 4V22L17 20L14 22L12 20L10 22L7 20L4 22V4Z" fill="currentColor"/>
                                    <path d="M8 7H16M8 11H16M8 15H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span>Riwayat Penjualan</span>
                            </a>
                            <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('purchases.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                                    <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Pembelian (Beli)</span>
                            </a>
                        </div>
                    </div>

                    {{-- Group: Inventaris --}}
                    <div>
                        <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Katalog & Stok</p>
                        <div class="space-y-1">
                            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('products.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 7.5L12 3L21 7.5V16.5L12 21L3 16.5V7.5Z" fill="currentColor"/>
                                    <path d="M3 7.5L12 12M12 12L21 7.5M12 12V21M7.5 5.25L16.5 9.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Daftar Produk</span>
                            </a>
                            <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('categories.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M4 4H10.5858C11.1163 4 11.625 4.21071 12 4.58579L20.4142 13C21.1953 13.781 21.1953 15.0474 20.4142 15.8284L15.8284 20.4142C15.0474 21.1953 13.781 21.1953 13 20.4142L4.58579 12C4.21071 11.625 4 11.1163 4 10.5858V4Z" fill="currentColor"/>
                                    <path d="M4 4H10.5858C11.1163 4 11.625 4.21071 12 4.58579L20.4142 13C21.1953 13.781 21.1953 15.0474 20.4142 15.8284L15.8284 20.4142C15.0474 21.1953 13.781 21.1953 13 20.4142L4.58579 12C4.21071 11.625 4 11.1163 4 10.5858V4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="8" cy="8" r="1.5" fill="currentColor"/>
                                </svg>
                                <span>Kategori</span>
                            </a>
                            <a href="{{ route('units.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('units.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 6C3 4.89543 3.89543 4 5 4H19C20.1046 4 21 4.89543 21 6V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V6Z" fill="currentColor"/>
                                    <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <path d="M7 4V8M11 4V10M15 4V8M19 4V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span>Satuan Unit</span>
                            </a>
                            <a href="{{ route('galleries.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('galleries.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 6C3 4.89543 3.89543 4 5 4H19C20.1046 4 21 4.89543 21 6V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V6Z" fill="currentColor"/>
                                    <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                                    <path d="M21 15L16 10L5 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Galeri Foto</span>
                            </a>
                        </div>
                    </div>

                    {{-- Group: Relasi --}}
                    <div>
                        <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Relasi Bisnis</p>
                        <div class="space-y-1">
                            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('customers.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                    <path opacity="0.3" d="M17 14C19.7614 14 22 16.2386 22 19V21H18" fill="currentColor"/>
                                    <path d="M4 21C4 17.134 7.13401 14 11 14H13C16.866 14 20 17.134 20 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span>Pelanggan</span>
                            </a>
                            <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('suppliers.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M1 4C1 3.44772 1.44772 3 2 3H14C14.5523 3 15 3.44772 15 4V16C15 16.5523 14.5523 17 14 17H2C1.44772 17 1 16.5523 1 16V4Z" fill="currentColor"/>
                                    <path d="M1 4C1 3.44772 1.44772 3 2 3H14C14.5523 3 15 3.44772 15 4V16C15 16.5523 14.5523 17 14 17H2C1.44772 17 1 16.5523 1 16V4Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M15 8H19.2C19.4652 8 19.7196 8.10536 19.9071 8.29289L22.7071 11.0929C22.8946 11.2804 23 11.5348 23 11.8V16C23 16.5523 22.5523 17 22 17H15V8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <circle cx="5.5" cy="18.5" r="2.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                                    <circle cx="18.5" cy="18.5" r="2.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                                </svg>
                                <span>Tengkulak (Supplier)</span>
                            </a>
                        </div>
                    </div>

                    {{-- Group: Keuangan & Laporan --}}
                    <div>
                        <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Keuangan & Laporan</p>
                        <div class="space-y-1">
                            <a href="{{ route('cash-transactions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('cash-transactions.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 9L12 3L21 9V11H3V9Z" fill="currentColor"/>
                                    <path d="M3 9L12 3L21 9M3 11H21M5 11V18M9 11V18M15 11V18M19 11V18M3 18H21M2 21H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Buku Kas Toko</span>
                            </a>
                            <a href="{{ route('payments.suppliers') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('payments.suppliers*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 6C3 4.89543 3.89543 4 5 4H19C20.1046 4 21 4.89543 21 6V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V6Z" fill="currentColor"/>
                                    <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <path d="M3 9H21M7 15H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span>Hutang Tengkulak</span>
                            </a>
                            <a href="{{ route('payments.customers') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('payments.customers*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" fill="currentColor"/>
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 7V17M14.5 9.5C14.5 8.11929 13.3807 7 12 7H10C8.89543 7 8 7.89543 8 9C8 10.1046 8.89543 11 10 11H14C15.1046 11 16 11.8954 16 13C16 14.1046 15.1046 15 14 15H12M12 17H9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span>Piutang Pelanggan</span>
                            </a>
                            <a href="{{ route('reports.sales') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('reports.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 4C3 3.44772 3.44772 3 4 3H20C20.5523 3 21 3.44772 21 4V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V4Z" fill="currentColor"/>
                                    <path d="M3 20H21M7 16V12M12 16V8M17 16V5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span>Laporan & Analisis</span>
                            </a>
                        </div>
                    </div>

                    {{-- Group: Pengaturan --}}
                    <div>
                        <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Sistem</p>
                        <div class="space-y-1">
                            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('users.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                    <path d="M5 21C5 17.134 8.13401 14 12 14C15.866 14 19 17.134 19 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span>Kelola Pengguna</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('settings.*') ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100/70 hover:text-dark' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle opacity="0.3" cx="12" cy="12" r="4" fill="currentColor"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Pengaturan Toko</span>
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Main Content Wrapper (With left margin md:pl-64 on desktop) --}}
            <div class="md:pl-64 flex flex-col min-h-screen">

                {{-- Desktop Top Bar (Hidden on mobile < 768px, visible on md: and up) --}}
                <header class="hidden md:flex items-center justify-between px-8 py-3.5 sticky top-0 z-30 bg-white/75 backdrop-blur-md border-b border-gray-200/50 shadow-sm select-none">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-gray-400">Hari ini:</span>
                        <span class="text-xs font-bold text-dark bg-gray-100/80 px-2.5 py-1 rounded-lg">
                            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('sales.create') }}" class="btn-primary py-1.5 px-4 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Jual Baru (Kasir)</span>
                        </a>
                        <div class="w-px h-6 bg-gray-200"></div>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-gray-100/80 transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="text-left hidden lg:block">
                                    <p class="text-xs font-bold text-dark leading-tight">{{ Auth::user()->name ?? 'User' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ Auth::user()->role ?? 'Kasir' }}</p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 glass-card-solid p-2 z-50 shadow-lg">
                                <div class="px-3 py-2 border-b border-gray-100 mb-1">
                                    <p class="text-xs font-bold text-dark">{{ Auth::user()->name ?? 'User' }}</p>
                                    <p class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-gray-600 rounded-lg hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    Profil Saya
                                </a>
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-gray-600 rounded-lg hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    Pengaturan
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                        Keluar (Logout)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Mobile Top Header (Visible only on mobile < 768px, hidden on md: and up) --}}
                <header class="sticky top-0 z-30 glass-nav transition-all duration-300 md:hidden"
                        x-data="{ scrolled: false, pageTitle: 'POS Toko Tani' }"
                        x-init="
                            window.addEventListener('scroll', () => {
                                scrolled = window.scrollY > 20;
                            });
                            
                            // Extract page title dynamically
                            setTimeout(() => {
                                const headerContainer = document.querySelector('main')?.previousElementSibling;
                                const h2El = headerContainer ? headerContainer.querySelector('h2') : null;
                                if (h2El) {
                                    pageTitle = h2El.textContent.trim();
                                } else {
                                    const parts = document.title.split('-');
                                    const rawTitle = parts[parts.length - 1]?.trim() || '';
                                    if (rawTitle && rawTitle !== 'POS Toko Tani') {
                                        pageTitle = rawTitle;
                                    }
                                }
                            }, 100);
                        ">
                    <div class="max-w-lg mx-auto px-3 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-float">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="relative h-9 min-w-[150px] overflow-hidden">
                                {{-- POS Toko Tani (Default State) --}}
                                <div x-show="!scrolled" 
                                     x-transition:enter="transition ease-out duration-300 transform"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-200 transform"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-2"
                                     class="absolute inset-y-0 left-0 flex flex-col justify-center">
                                    <h1 class="text-base font-bold text-dark leading-tight">POS Toko Tani</h1>
                                    <p class="text-[10px] text-gray-400 font-medium">Sistem Kasir Pertanian</p>
                                </div>

                                {{-- Page Title (Scrolled State) --}}
                                <div x-show="scrolled" 
                                     x-transition:enter="transition ease-out duration-300 transform"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-200 transform"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-2"
                                     class="absolute inset-y-0 left-0 flex flex-col justify-center"
                                     style="display: none;">
                                    <h1 class="text-base font-bold text-primary-600 leading-tight truncate max-w-[200px]" x-text="pageTitle"></h1>
                                    <p class="text-[10px] text-gray-400 font-medium">Halaman Aktif</p>
                                </div>
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
                                    <a href="{{ route('users.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                                            <path d="M6 21C6 17.134 9.13401 14 13 14H11C7.13401 14 4 17.134 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        Kelola Pengguna
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                @if (isset($header))
                    <div class="w-full max-w-lg mx-auto px-3 pt-4 md:max-w-7xl md:px-8 md:pt-6">
                        {{ $header }}
                    </div>
                @endif

                {{-- Page Content --}}
                <main class="w-full max-w-lg mx-auto px-3 pb-24 pt-2 page-enter md:max-w-7xl md:px-8 md:pt-4 md:pb-12 flex-1">
                    @yield('content')
                    {{ $slot ?? '' }}
                </main>

            </div>

            {{-- Floating Bottom Navigation (Visible only on mobile < 768px) --}}
            <nav class="fixed bottom-0 left-0 right-0 z-40 px-3 pb-4 md:hidden">
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

                        {{-- Galeri --}}
                        <a href="{{ route('galleries.index') }}" class="flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('galleries.*') ? 'bg-primary-50/80' : 'hover:bg-gray-50/50' }}">
                            <div class="relative">
                                <svg class="w-6 h-6 transition-colors {{ request()->routeIs('galleries.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('galleries.*') ? '2.5' : '2' }}" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                @if(request()->routeIs('galleries.*'))
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-accent-500 rounded-full"></span>
                                @endif
                            </div>
                            <span class="text-[10px] mt-1 font-semibold {{ request()->routeIs('galleries.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">Galeri</span>
                        </a>

                        {{-- More --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex flex-col items-center py-2 px-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('products.*', 'suppliers.*', 'customers.*', 'categories.*', 'units.*', 'payments.*', 'settings.*', 'cash-transactions.*', 'reports.*', 'users.*') ? 'bg-primary-50/80' : 'hover:bg-gray-50/50' }}">
                                <svg class="w-6 h-6 transition-colors {{ request()->routeIs('products.*', 'suppliers.*', 'customers.*', 'categories.*', 'units.*', 'payments.*', 'settings.*', 'cash-transactions.*', 'reports.*', 'users.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span class="text-[10px] mt-1 font-semibold {{ request()->routeIs('products.*', 'suppliers.*', 'customers.*', 'categories.*', 'units.*', 'payments.*', 'settings.*', 'cash-transactions.*', 'reports.*', 'users.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">Lainnya</span>
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
                                <a href="{{ route('reports.sales') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('reports.sales', 'reports.purchases', 'reports.profit', 'reports.debts') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">📊</span> Laporan
                                </a>
                                <a href="{{ route('reports.stock_movements') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('reports.stock_movements') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">📜</span> Riwayat Stok
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
                                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                    <span class="text-lg">👤</span> Pengguna
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
                    $('select:not(.no-select2):not(.swal2-select)').each(function() {
                        if (!$(this).hasClass("select2-hidden-accessible")) {
                            $(this).select2({
                                width: '100%'
                            });
                            // Fix Safari/iOS validation bug for hidden required selects
                            if ($(this).attr('required')) {
                                $(this).removeAttr('required');
                            }
                        }
                    });
                }
                
                const d = new Date();
                const clientTodayStr = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
                const serverTodayStr = '{{ date('Y-m-d') }}';

                // Initialize Flatpickr globally on all date input fields
                function initFlatpickr() {
                    $('.datepicker:not([x-init])').each(function() {
                        if (!$(this).hasClass("flatpickr-input")) {
                            let defaultDateVal = $(this).val();

                            // If it matches server today (possibly wrong clock) or is empty and required, default to client today
                            if (defaultDateVal === serverTodayStr || (!defaultDateVal && $(this).prop('required'))) {
                                defaultDateVal = clientTodayStr;
                                $(this).val(clientTodayStr);
                            }

                            flatpickr(this, {
                                locale: 'id',
                                dateFormat: 'Y-m-d',
                                allowInput: true,
                                altInput: true,
                                altFormat: 'd F Y',
                                defaultDate: defaultDateVal || null,
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

        <!-- Cropper JS CDN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

        <!-- Global Image Crop Modal -->
        <div id="global-crop-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500/75 backdrop-blur-sm"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white/95 backdrop-blur-xl border border-white/50 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-4 w-full">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-dark" id="modal-title">Sesuaikan Gambar (Crop)</h3>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-center bg-gray-50 border border-gray-100 rounded-xl overflow-hidden max-h-[50vh]">
                        <img id="global-crop-img-element" class="max-w-full max-h-[50vh] block">
                    </div>
                    
                    {{-- Rotation & Flip Toolbar --}}
                    <div class="mt-3 flex items-center justify-center gap-1.5">
                        <button type="button" id="global-crop-rotate-left" class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors border border-gray-200" title="Putar Kiri 90°">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l4-4M3 10l4 4"/></svg>
                        </button>
                        <button type="button" id="global-crop-rotate-right" class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors border border-gray-200" title="Putar Kanan 90°">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2M21 10l-4-4M21 10l-4 4"/></svg>
                        </button>
                        <div class="w-px h-6 bg-gray-200 mx-1"></div>
                        <button type="button" id="global-crop-flip-h" class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors border border-gray-200" title="Cermin Horizontal">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4l-4 4M17 16V4l4 4M12 2v20"/></svg>
                        </button>
                        <button type="button" id="global-crop-flip-v" class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors border border-gray-200" title="Cermin Vertikal">
                            <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4l-4 4M17 16V4l4 4M12 2v20"/></svg>
                        </button>
                        <div class="w-px h-6 bg-gray-200 mx-1"></div>
                        <button type="button" id="global-crop-reset" class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors border border-gray-200" title="Reset">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 9a9 9 0 0115.36-4.36M20 15a9 9 0 01-15.36 4.36"/></svg>
                        </button>
                    </div>
                    
                    <div class="mt-5 flex gap-2 justify-between">
                        <button type="button" id="global-crop-cancel" class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors border border-gray-200">
                            Batal
                        </button>
                        <div class="flex gap-2">
                            <button type="button" id="global-crop-skip" class="px-3 py-2 text-xs font-semibold text-primary-700 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors border border-primary-200">
                                Lewati Potong
                            </button>
                            <button type="button" id="global-crop-save" class="px-4 py-2 text-xs font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-colors shadow">
                                Potong & Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let currentCropper = null;

            // Pre-resize large images to prevent mobile browser freeze
            function preResizeImage(file, maxDim) {
                maxDim = maxDim || 2048;
                return new Promise(function(resolve) {
                    // Skip non-images
                    if (!file || !file.type.startsWith('image/')) {
                        resolve(file);
                        return;
                    }
                    
                    var img = new Image();
                    var url = URL.createObjectURL(file);
                    img.onload = function() {
                        // If image is already small enough, return original
                        if (img.naturalWidth <= maxDim && img.naturalHeight <= maxDim) {
                            URL.revokeObjectURL(url);
                            resolve(file);
                            return;
                        }
                        
                        // Calculate new dimensions
                        var ratio = Math.min(maxDim / img.naturalWidth, maxDim / img.naturalHeight);
                        var newW = Math.round(img.naturalWidth * ratio);
                        var newH = Math.round(img.naturalHeight * ratio);
                        
                        // Draw to offscreen canvas
                        var canvas = document.createElement('canvas');
                        canvas.width = newW;
                        canvas.height = newH;
                        var ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, newW, newH);
                        URL.revokeObjectURL(url);
                        
                        canvas.toBlob(function(blob) {
                            if (blob) {
                                var resizedFile = new File([blob], file.name, { type: 'image/jpeg' });
                                resolve(resizedFile);
                            } else {
                                resolve(file);
                            }
                            // Free canvas memory
                            canvas.width = 0;
                            canvas.height = 0;
                        }, 'image/jpeg', 0.85);
                    };
                    img.onerror = function() {
                        URL.revokeObjectURL(url);
                        resolve(file);
                    };
                    img.src = url;
                });
            }

            window.cropImage = function(file, successCallback, skipCallback, cancelCallback) {
                if (!file || !file.type.startsWith('image/')) {
                    if (skipCallback) skipCallback(file);
                    return;
                }

                // Show loading while pre-processing large images
                Swal.fire({
                    title: 'Memproses gambar...',
                    text: 'Mengoptimalkan ukuran gambar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: function() { Swal.showLoading(); },
                    customClass: { popup: 'rounded-2xl font-sans' }
                });

                preResizeImage(file, 2048).then(function(processedFile) {
                    Swal.close();

                    var modal = document.getElementById('global-crop-modal');
                    var imgElement = document.getElementById('global-crop-img-element');
                    var saveBtn = document.getElementById('global-crop-save');
                    var skipBtn = document.getElementById('global-crop-skip');
                    var cancelBtn = document.getElementById('global-crop-cancel');

                    // Use createObjectURL instead of readAsDataURL (much less memory)
                    var objectUrl = URL.createObjectURL(processedFile);
                    imgElement.src = objectUrl;
                    
                    imgElement.onload = function() {
                        modal.classList.remove('hidden');
                        
                        if (currentCropper) {
                            currentCropper.destroy();
                        }
                        
                        currentCropper = new Cropper(imgElement, {
                            aspectRatio: NaN,
                            viewMode: 2,
                            autoCropArea: 0.9,
                            responsive: true,
                            restore: false,
                            checkCrossOrigin: false,
                            rotateable: true
                        });
                    };

                    function closeModal() {
                        modal.classList.add('hidden');
                        if (currentCropper) {
                            currentCropper.destroy();
                            currentCropper = null;
                        }
                        URL.revokeObjectURL(objectUrl);
                    }

                    saveBtn.onclick = function() {
                        if (!currentCropper) return;
                        currentCropper.getCroppedCanvas({
                            maxWidth: 1920,
                            maxHeight: 1920,
                            imageSmoothingQuality: 'high'
                        }).toBlob(function(blob) {
                            if (blob) {
                                successCallback(blob);
                            } else {
                                skipCallback(processedFile);
                            }
                            closeModal();
                        }, 'image/jpeg', 0.85);
                    };

                    skipBtn.onclick = function() {
                        skipCallback(processedFile);
                        closeModal();
                    };

                    cancelBtn.onclick = function() {
                        if (cancelCallback) cancelCallback();
                        closeModal();
                    };

                    // Rotation & Flip handlers
                    var flipH = 1, flipV = 1;
                    document.getElementById('global-crop-rotate-left').onclick = function() {
                        if (currentCropper) currentCropper.rotate(-90);
                    };
                    document.getElementById('global-crop-rotate-right').onclick = function() {
                        if (currentCropper) currentCropper.rotate(90);
                    };
                    document.getElementById('global-crop-flip-h').onclick = function() {
                        if (currentCropper) { flipH = flipH * -1; currentCropper.scaleX(flipH); }
                    };
                    document.getElementById('global-crop-flip-v').onclick = function() {
                        if (currentCropper) { flipV = flipV * -1; currentCropper.scaleY(flipV); }
                    };
                    document.getElementById('global-crop-reset').onclick = function() {
                        if (currentCropper) { flipH = 1; flipV = 1; currentCropper.reset(); }
                    };
                });
            };

            // Global loading overlay on form submit (except GET filter forms or AJAX)
            document.addEventListener('submit', function(e) {
                const form = e.target;
                
                // Do not show loading for GET requests (like filters or search forms)
                if (form.getAttribute('method')?.toLowerCase() === 'get') {
                    return;
                }
                
                // If it is a confirm-delete form and not yet confirmed
                if (form.classList.contains('confirm-delete') && form.dataset.confirmed !== 'true') {
                    e.preventDefault();
                    const message = form.getAttribute('data-confirm') || 'Yakin ingin menghapus data ini?';
                    
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-2xl font-sans shadow-lg'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            // Show loading SweetAlert manually
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Mohon tunggu sebentar.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                                customClass: {
                                    popup: 'rounded-2xl font-sans'
                                }
                            });
                            form.submit();
                        }
                    });
                    return;
                }
                
                // Do not show loading for forms that are flagged with 'no-loading'
                if (form.classList.contains('no-loading')) {
                    return;
                }

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    customClass: {
                        popup: 'rounded-2xl font-sans'
                    }
                });
            });
        </script>

        @stack('scripts')
    </body>
</html>

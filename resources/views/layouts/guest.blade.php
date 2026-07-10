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

        <title>{{ config('app.name', 'POS Toko Tani') }} - @yield('title', 'Login')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8"
             style="background: linear-gradient(135deg, #f0fdf4 0%, #FAFAF9 40%, #fff7ed 100%);">

            {{-- Logo --}}
            <div class="mb-8 text-center page-enter">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-float mb-4">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-dark">POS Toko Tani</h1>
                <p class="text-sm text-gray-400 mt-1">Sistem Kasir Pertanian</p>
            </div>

            {{-- Card --}}
            <div class="w-full max-w-sm glass-card-solid p-6 page-enter" style="animation-delay: 0.1s;">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p class="mt-8 text-xs text-gray-400 page-enter" style="animation-delay: 0.2s;">
                &copy; {{ date('Y') }} POS Toko Tani
            </p>
        </div>

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
    </body>
</html>

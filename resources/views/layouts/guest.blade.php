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
    <body class="font-sans antialiased bg-gray-50/50">
        <div class="min-h-screen relative overflow-hidden flex flex-col items-center justify-center px-4 py-8">
            
            {{-- Floating Glassmorphic Blobs --}}
            <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-gradient-to-tr from-primary-400/20 to-primary-600/10 blur-[80px] pointer-events-none"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-gradient-to-br from-accent-400/20 to-accent-600/10 blur-[80px] pointer-events-none"></div>

            <div class="relative z-10 w-full flex flex-col items-center">
                {{-- Logo --}}
                <div class="mb-6 text-center page-enter">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-float mb-4">
                        <svg class="w-9 h-9 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="9" fill="currentColor" class="opacity-30" />
                            <path d="M12 3a9 9 0 00-9 9m9 9a9 9 0 009-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-dark">POS Toko Tani</h1>
                    <p class="text-xs text-gray-400 mt-1">Sistem Kasir Pertanian</p>
                </div>

                {{-- Card --}}
                <div class="w-full max-w-sm bg-white/40 backdrop-blur-xl border border-white/50 shadow-glass rounded-3xl p-6 page-enter" style="animation-delay: 0.1s;">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <p class="mt-8 text-xs text-gray-400 page-enter" style="animation-delay: 0.2s;">
                    &copy; {{ date('Y') }} POS Toko Tani
                </p>
            </div>
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

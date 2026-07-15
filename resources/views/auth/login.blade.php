<x-guest-layout>
    <x-slot name="title">Masuk</x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-xs font-semibold text-primary-600" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false }" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1">
            <label for="email" class="block text-xs font-bold text-gray-600">Email Kasir</label>
            <div class="relative flex items-center">
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="nama@postani.com"
                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-300 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white/70 backdrop-blur-sm transition-all duration-200 text-sm placeholder-gray-400">
                
                <div class="absolute left-0 pl-3 flex items-center justify-center pointer-events-none z-10" style="width: 44px; height: 100%;">
                    <!-- Duotone Envelope Icon -->
                    <svg style="width: 20px; height: 20px; color: #16a34a; display: block;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" fill="currentColor" class="opacity-25" />
                        <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
            @error('email') 
                <p class="text-[10px] text-red-500 font-semibold mt-1">{{ $message }}</p> 
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <label for="password" class="block text-xs font-bold text-gray-600">Kata Sandi</label>
            <div class="relative flex items-center">
                <input id="password" 
                       :type="showPassword ? 'text' : 'password'" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full pl-11 pr-10 py-3 rounded-xl border border-gray-300 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white/70 backdrop-blur-sm transition-all duration-200 text-sm placeholder-gray-400">
                
                <div class="absolute left-0 pl-3 flex items-center justify-center pointer-events-none z-10" style="width: 44px; height: 100%;">
                    <!-- Duotone Lock Icon -->
                    <svg style="width: 20px; height: 20px; color: #16a34a; display: block;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="11" width="18" height="11" rx="2" fill="currentColor" class="opacity-25" />
                        <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2" />
                        <path d="M12 14v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <circle cx="12" cy="14" r="1" fill="currentColor" />
                    </svg>
                </div>

                <!-- Toggle Password visibility with Duotone Eye Icons -->
                <button type="button" @click="showPassword = !showPassword" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none z-10">
                    <template x-if="!showPassword">
                        <!-- Duotone Eye Open -->
                        <svg style="width: 20px; height: 20px; color: #9ca3af; display: block;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" fill="currentColor" class="opacity-20" />
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="currentColor" />
                        </svg>
                    </template>
                    <template x-if="showPassword">
                        <!-- Duotone Eye Closed -->
                        <svg style="width: 20px; height: 20px; color: #9ca3af; display: block;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" fill="currentColor" class="opacity-20" />
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                            <path d="M3 3l18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </template>
                </button>
            </div>
            @error('password') 
                <p class="text-[10px] text-red-500 font-semibold mt-1">{{ $message }}</p> 
            @enderror
        </div>

        <div class="flex justify-between items-center text-[11px] pt-1">
            <span></span>
            @if (Route::has('password.request'))
                <a class="text-primary-600 hover:text-primary-700 font-semibold transition-colors underline" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200 active:scale-[0.98] shadow-md text-sm flex items-center justify-center gap-2">
                Masuk ke Kasir
                <!-- Duotone Login/Arrow Icon -->
                <svg style="width: 20px; height: 20px; color: #ffffff; display: block;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="9" fill="currentColor" class="opacity-30" />
                    <path d="M12 8l4 4-4 4M8 12h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                </svg>
            </button>
        </div>
    </form>
</x-guest-layout>

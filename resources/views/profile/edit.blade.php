<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Profil</h2>
        </div>
    </x-slot>

    <div class="py-5 pb-28 space-y-4">
        <div class="card-solid p-4">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card-solid p-4">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card-solid p-4">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

        {{-- Logout Section --}}
        <div class="card-solid p-4">
            <div class="max-w-xl">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2.5 px-4 py-3 text-sm font-bold text-red-500 rounded-xl border border-red-200 bg-red-50/50 hover:bg-red-100 active:scale-[0.98] transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar dari Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

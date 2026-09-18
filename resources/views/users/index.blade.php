<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-dark">Manajemen Pengguna</h2>
                <p class="text-xs text-gray-500">Kelola akun administrator dan kasir</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('users.create') }}" class="md:hidden w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg active:scale-95 transition-transform" title="Tambah Pengguna">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </a>
                <a href="{{ route('users.create') }}" class="hidden md:inline-flex btn-primary items-center gap-1.5 text-xs py-2 px-4 rounded-xl shadow-xs">
                    <span>+ Tambah Pengguna</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 pb-28 space-y-4 max-w-lg mx-auto md:max-w-none" x-data="{
        search: '{{ request('search', '') }}',
        role: '{{ request('role', '') }}',
        status: '{{ request('status', '') }}',
        applyFilter() {
            let params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            if (this.role) params.set('role', this.role);
            if (this.status) params.set('status', this.status);
            window.location.href = '{{ route('users.index') }}?' + params.toString();
        }
    }">
        {{-- Search & Filters --}}
        <div class="space-y-2.5">
            {{-- Search Bar --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle opacity="0.3" cx="11" cy="11" r="7" fill="currentColor"/>
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                        <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <input type="text" 
                       x-model="search" 
                       @keydown.enter.prevent="applyFilter()"
                       placeholder="Cari nama, email, atau no. telepon..."
                       class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm text-xs font-medium text-dark focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                <button type="button" 
                        x-show="search" 
                        @click="search = ''; applyFilter()" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        style="display: none;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Role Filter Chips --}}
            <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide py-1">
                <button type="button" 
                        @click="role = ''; applyFilter()"
                        :class="role === '' ? 'bg-primary-600 text-white shadow-sm' : 'bg-white/80 border border-gray-200/80 text-gray-600 hover:bg-white'"
                        class="px-3 py-1.5 rounded-full text-[11px] font-semibold transition-all shrink-0">
                    Semua Role
                </button>
                <button type="button" 
                        @click="role = 'admin'; applyFilter()"
                        :class="role === 'admin' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white/80 border border-gray-200/80 text-gray-600 hover:bg-white'"
                        class="px-3 py-1.5 rounded-full text-[11px] font-semibold transition-all shrink-0">
                    👑 Admin
                </button>
                <button type="button" 
                        @click="role = 'kasir'; applyFilter()"
                        :class="role === 'kasir' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white/80 border border-gray-200/80 text-gray-600 hover:bg-white'"
                        class="px-3 py-1.5 rounded-full text-[11px] font-semibold transition-all shrink-0">
                    🛒 Kasir
                </button>

                <div class="h-4 w-[1px] bg-gray-300 mx-1 shrink-0"></div>

                <button type="button" 
                        @click="status = status === 'active' ? '' : 'active'; applyFilter()"
                        :class="status === 'active' ? 'bg-green-600 text-white shadow-sm' : 'bg-white/80 border border-gray-200/80 text-gray-600 hover:bg-white'"
                        class="px-3 py-1.5 rounded-full text-[11px] font-semibold transition-all shrink-0">
                    🟢 Aktif
                </button>
                <button type="button" 
                        @click="status = status === 'inactive' ? '' : 'inactive'; applyFilter()"
                        :class="status === 'inactive' ? 'bg-gray-700 text-white shadow-sm' : 'bg-white/80 border border-gray-200/80 text-gray-600 hover:bg-white'"
                        class="px-3 py-1.5 rounded-full text-[11px] font-semibold transition-all shrink-0">
                    ⚪ Nonaktif
                </button>
            </div>
        </div>

        {{-- Active Filters Indicator --}}
        @if(request('search') || request('role') || request('status'))
            <div class="glass-card px-3.5 py-2 flex items-center justify-between text-xs font-semibold">
                <div class="flex items-center gap-1.5 text-gray-500 flex-wrap">
                    <span>Filter:</span>
                    @if(request('search'))
                        <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-lg text-[10px]">Cari: "{{ request('search') }}"</span>
                    @endif
                    @if(request('role'))
                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase">{{ request('role') }}</span>
                    @endif
                    @if(request('status'))
                        <span class="bg-green-50 text-green-700 px-2 py-0.5 rounded-lg text-[10px]">{{ request('status') === 'active' ? 'Aktif' : 'Nonaktif' }}</span>
                    @endif
                </div>
                <a href="{{ route('users.index') }}" class="text-[11px] font-bold text-red-500 hover:text-red-700 transition-colors shrink-0">
                    Reset
                </a>
            </div>
        @endif

        {{-- Users List --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($users as $user)
            <div class="card-solid p-4 hover:shadow-md transition-all bg-white relative flex flex-col justify-between">
                <div class="flex items-start justify-between gap-3">
                    {{-- User Avatar & Info --}}
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 font-extrabold text-sm {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-dark truncate">{{ $user->name }}</h3>
                                @if(auth()->id() === $user->id)
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-primary-100 text-primary-700 font-extrabold">Anda</span>
                                @endif
                            </div>
                            
                            <p class="text-xs text-gray-500 truncate flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 8L10.89 13.26C11.56 13.71 12.44 13.71 13.11 13.26L21 8M5 19H19C20.1 19 21 18.1 21 17V7C21 5.9 20.1 5 19 5H5C3.9 5 3 5.9 3 7V17C3 18.1 3.9 19 5 19Z" fill="currentColor"/>
                                    <path d="M3 8L10.89 13.26C11.56 13.71 12.44 13.71 13.11 13.26L21 8M5 19H19C20.1 19 21 18.1 21 17V7C21 5.9 20.1 5 19 5H5C3.9 5 3 5.9 3 7V17C3 18.1 3.9 19 5 19Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                </svg>
                                <span class="truncate">{{ $user->email }}</span>
                            </p>

                            @if($user->phone)
                            <p class="text-xs text-gray-400 truncate flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 5c0-1.1.9-2 2-2h3.5c.55 0 1.05.3 1.25.8l1.2 3c.2.5.05 1.1-.35 1.5L8.7 10.2c1.2 2.1 2.9 3.8 5 5l1.9-1.9c.4-.4 1-.55 1.5-.35l3 1.2c.5.2.8.7.8 1.25V19c0 1.1-.9 2-2 2-9.4 0-17-7.6-17-17z" fill="currentColor"/>
                                    <path d="M3 5c0-1.1.9-2 2-2h3.5c.55 0 1.05.3 1.25.8l1.2 3c.2.5.05 1.1-.35 1.5L8.7 10.2c1.2 2.1 2.9 3.8 5 5l1.9-1.9c.4-.4 1-.55 1.5-.35l3 1.2c.5.2.8.7.8 1.25V19c0 1.1-.9 2-2 2-9.4 0-17-7.6-17-17z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                </svg>
                                <span class="truncate">{{ $user->phone }}</span>
                            </p>
                            @endif

                            {{-- Badges --}}
                            <div class="flex items-center gap-1.5 pt-1 flex-wrap">
                                @if($user->role === 'admin')
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                        👑 Administrator
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        🛒 Kasir
                                    </span>
                                @endif

                                @if($user->is_active)
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                        Nonaktif
                                    </span>
                                @endif

                                <span class="text-[10px] text-gray-400">
                                    · {{ $user->sales_count + $user->purchases_count }} Transaksi
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Bar --}}
                <div class="border-t border-gray-100 mt-3 pt-2.5 flex items-center justify-between">
                    <a href="{{ route('users.show', $user) }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                        Lihat Riwayat
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('users.edit', $user) }}" 
                           class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition-colors active:scale-95" 
                           title="Ubah Data Pengguna">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>

                        @if(auth()->id() !== $user->id)
                        <form action="{{ route('users.destroy', $user) }}" 
                              method="POST" 
                              class="confirm-delete inline" 
                              data-confirm="Apakah Anda yakin ingin menghapus akun '{{ $user->name }}'?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-150 flex items-center justify-center transition-colors active:scale-95" 
                                    title="Hapus Pengguna">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M6 7H18L17 21H7L6 7Z" fill="currentColor"/>
                                    <path d="M6 7H18L17 21H7L6 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 7H20M10 3H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="card-solid p-8 text-center space-y-2 bg-white md:col-span-2 lg:col-span-3">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto text-gray-400">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                        <path d="M6 21C6 17.134 9.13401 14 13 14H11C7.13401 14 4 17.134 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-dark">Tidak ada pengguna ditemukan</h4>
                <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian atau filter yang digunakan.</p>
                <div class="pt-2">
                    <a href="{{ route('users.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-600 text-white text-xs font-bold shadow-md active:scale-95 transition-transform">
                        + Tambah Pengguna Baru
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-app-layout>

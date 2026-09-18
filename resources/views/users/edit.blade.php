<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform hover:bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-dark">Ubah Pengguna</h2>
                    <p class="text-xs text-gray-500">{{ $user->name }} ({{ $user->email }})</p>
                </div>
            </div>

            {{-- Desktop Header Actions --}}
            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 rounded-xl border border-gray-200 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" form="user-edit-form" class="btn-primary py-2 px-4 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="pb-24 pt-1 max-w-lg mx-auto md:max-w-none">
        <form id="user-edit-form" action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- 12-Column Responsive Grid on Desktop --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-start">
                
                {{-- LEFT COLUMN: User Summary & Status (4 cols on desktop) --}}
                <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-20">
                    {{-- User Profile Preview Card --}}
                    <div class="card-solid p-5 space-y-4 bg-white text-center">
                        <div class="w-20 h-20 rounded-3xl mx-auto flex items-center justify-center font-black text-2xl shadow-inner {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-dark">{{ $user->name }}</h3>
                            <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                        </div>

                        {{-- Active Status Card inside left column --}}
                        <div class="pt-3 border-t border-gray-150 flex items-center justify-between text-left">
                            <div>
                                <label for="is_active" class="text-xs font-bold text-dark block cursor-pointer">Status Akun Aktif</label>
                                <p class="text-[10px] text-gray-400">
                                    @if(auth()->id() === $user->id)
                                        <span class="text-amber-600 font-medium">Akun sendiri tetap aktif.</span>
                                    @else
                                        Dapat login ke sistem kasir
                                    @endif
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1" 
                                       class="sr-only peer" 
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                @if(auth()->id() === $user->id)
                                    <input type="hidden" name="is_active" value="1">
                                @endif
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Desktop Action Buttons --}}
                    <div class="hidden lg:block space-y-2">
                        <button type="submit" class="btn-primary w-full py-3 font-bold rounded-xl shadow-md transition-all active:scale-[0.98] text-xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                        <a href="{{ route('users.index') }}" class="w-full py-2.5 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 rounded-xl border border-gray-200 transition-colors flex items-center justify-center">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Form Fields (8 cols on desktop) --}}
                <div class="lg:col-span-8 space-y-4">
                    
                    {{-- 1. Account Information Card --}}
                    <div class="card-solid p-5 space-y-4 bg-white">
                        <div class="flex items-center gap-2 border-b border-gray-150 pb-3">
                            <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                                    <path d="M6 21C6 17.134 9.13401 14 13 14H11C7.13401 14 4 17.134 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-dark">Informasi Akun</h3>
                                <p class="text-[11px] text-gray-400">Data login dan kontak pengguna</p>
                            </div>
                        </div>

                        <div class="space-y-3.5">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-600 mb-1">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <div class="input-group-solid">
                                    <span class="input-prefix">
                                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                                            <path d="M6 21C6 17.134 9.13401 14 13 14H11C7.13401 14 4 17.134 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </span>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required 
                                           placeholder="Nama pengguna"
                                           class="form-input-solid">
                                </div>
                                @error('name')
                                    <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 2 Cols: Email & Phone --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- Email --}}
                                <div>
                                    <label for="email" class="block text-xs font-semibold text-gray-600 mb-1">
                                        Alamat Email (Login) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="input-group-solid">
                                        <span class="input-prefix">
                                            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M3 8L10.89 13.26C11.56 13.71 12.44 13.71 13.11 13.26L21 8M5 19H19C20.1 19 21 18.1 21 17V7C21 5.9 20.1 5 19 5H5C3.9 5 3 5.9 3 7V17C3 18.1 3.9 19 5 19Z" fill="currentColor"/>
                                                <path d="M3 8L10.89 13.26C11.56 13.71 12.44 13.71 13.11 13.26L21 8M5 19H19C20.1 19 21 18.1 21 17V7C21 5.9 20.1 5 19 5H5C3.9 5 3 5.9 3 7V17C3 18.1 3.9 19 5 19Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required 
                                               placeholder="nama@email.com"
                                               class="form-input-solid">
                                    </div>
                                    @error('email')
                                        <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Phone --}}
                                <div>
                                    <label for="phone" class="block text-xs font-semibold text-gray-600 mb-1">
                                        Nomor WhatsApp / Telepon
                                    </label>
                                    <div class="input-group-solid">
                                        <span class="input-prefix">
                                            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M3 5c0-1.1.9-2 2-2h3.5c.55 0 1.05.3 1.25.8l1.2 3c.2.5.05 1.1-.35 1.5L8.7 10.2c1.2 2.1 2.9 3.8 5 5l1.9-1.9c.4-.4 1-.55 1.5-.35l3 1.2c.5.2.8.7.8 1.25V19c0 1.1-.9 2-2 2-9.4 0-17-7.6-17-17z" fill="currentColor"/>
                                                <path d="M3 5c0-1.1.9-2 2-2h3.5c.55 0 1.05.3 1.25.8l1.2 3c.2.5.05 1.1-.35 1.5L8.7 10.2c1.2 2.1 2.9 3.8 5 5l1.9-1.9c.4-.4 1-.55 1.5-.35l3 1.2c.5.2.8.7.8 1.25V19c0 1.1-.9 2-2 2-9.4 0-17-7.6-17-17z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <input type="text" 
                                               name="phone" 
                                               id="phone" 
                                               value="{{ old('phone', $user->phone) }}" 
                                               placeholder="08123456789"
                                               class="form-input-solid">
                                    </div>
                                    @error('phone')
                                        <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Role & Access Rights Card --}}
                    <div class="card-solid p-5 space-y-4 bg-white">
                        <div class="flex items-center gap-2 border-b border-gray-150 pb-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 2L4 5V11.09C4 16.14 7.41 20.85 12 22C16.59 20.85 20 16.14 20 11.09V5L12 2Z" fill="currentColor"/>
                                    <path d="M12 2L4 5V11.09C4 16.14 7.41 20.85 12 22C16.59 20.85 20 16.14 20 11.09V5L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-dark">Peran & Hak Akses</h3>
                                <p class="text-[11px] text-gray-400">Tentukan wewenang pengguna dalam aplikasi</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="kasir" class="peer sr-only" {{ old('role', $user->role) === 'kasir' ? 'checked' : '' }} {{ auth()->id() === $user->id && $user->role === 'admin' ? 'disabled' : '' }}>
                                    <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50/40 transition-all text-left h-full flex flex-col justify-between">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-dark flex items-center gap-1.5">
                                                <span>🛒</span>
                                                <span>Kasir</span>
                                            </span>
                                            <span class="w-4 h-4 rounded-full border border-gray-300 peer-checked:border-emerald-600 flex items-center justify-center">
                                                <span class="w-2 h-2 rounded-full bg-emerald-600 hidden peer-checked:block"></span>
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 leading-relaxed">Melayani kasir penjualan POS, catat barang masuk, cek inventaris barang.</p>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="admin" class="peer sr-only" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }}>
                                    <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-blue-600 peer-checked:bg-blue-50/40 transition-all text-left h-full flex flex-col justify-between">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-dark flex items-center gap-1.5">
                                                <span>👑</span>
                                                <span>Administrator</span>
                                            </span>
                                            <span class="w-4 h-4 rounded-full border border-gray-300 peer-checked:border-blue-600 flex items-center justify-center">
                                                <span class="w-2 h-2 rounded-full bg-blue-600 hidden peer-checked:block"></span>
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 leading-relaxed">Akses penuh mencakup laporan laba rugi, stok, data kas, dan kelola pengguna.</p>
                                    </div>
                                </label>
                            </div>
                            @if(auth()->id() === $user->id && $user->role === 'admin')
                                <p class="text-[10px] text-amber-600 font-medium pt-1">⚠️ Anda tidak dapat menurunkan peran akun Anda sendiri menjadi Kasir.</p>
                            @endif
                            @error('role')
                                <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 3. Password Management Card --}}
                    <div class="card-solid p-5 space-y-4 bg-white" x-data="{ changePassword: false }">
                        <div class="flex items-center justify-between border-b border-gray-150 pb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" fill="currentColor"/>
                                        <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-dark">Ubah Kata Sandi</h3>
                                    <p class="text-[11px] text-gray-400">Kosongkan jika tidak ingin mengubah password akun</p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="changePassword = !changePassword" 
                                    class="text-xs font-bold px-3 py-1.5 rounded-xl border border-primary-200 bg-primary-50 text-primary-700 hover:bg-primary-100 transition-colors shadow-sm">
                                <span x-text="changePassword ? 'Batal Ubah' : 'Ganti Password'"></span>
                            </button>
                        </div>

                        <div x-show="changePassword" x-transition class="space-y-3 pt-1" style="display: none;">
                            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="block text-xs font-semibold text-gray-600 mb-1">
                                        Kata Sandi Baru
                                    </label>
                                    <div class="input-group-solid">
                                        <span class="input-prefix">
                                            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" fill="currentColor"/>
                                                <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <input type="password" 
                                               name="password" 
                                               id="password" 
                                               placeholder="Minimal 8 karakter"
                                               class="form-input-solid">
                                    </div>
                                    @error('password')
                                        <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-600 mb-1">
                                        Ulangi Kata Sandi Baru
                                    </label>
                                    <div class="input-group-solid">
                                        <span class="input-prefix">
                                            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" fill="currentColor"/>
                                                <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <input type="password" 
                                               name="password_confirmation" 
                                               id="password_confirmation" 
                                               placeholder="Konfirmasi password baru"
                                               class="form-input-solid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Submit Button --}}
                    <div class="lg:hidden pt-2">
                        <button type="submit" class="btn-primary w-full py-3.5 font-bold rounded-xl shadow-lg transition-transform active:scale-[0.98] text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

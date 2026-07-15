<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('suppliers.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Tambah Tengkulak</h2>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 max-w-lg mx-auto">
        <div class="card-solid p-4">
            <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 mb-1">Nama Tengkulak</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: User -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                                <path d="M6 21C6 17.134 9.13401 14 13 14H11C7.13401 14 4 17.134 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                        <input type="text" name="name" id="name" required placeholder="Nama tengkulak..."
                               class="form-input-solid">
                    </div>
                </div>
                <div>
                    <label for="phone" class="block text-xs font-semibold text-gray-500 mb-1">Nomor Telepon</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Phone -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M3 5c0-1.1.9-2 2-2h3.5c.55 0 1.05.3 1.25.8l1.2 3c.2.5.05 1.1-.35 1.5L8.7 10.2c1.2 2.1 2.9 3.8 5 5l1.9-1.9c.4-.4 1-.55 1.5-.35l3 1.2c.5.2.8.7.8 1.25V19c0 1.1-.9 2-2 2-9.4 0-17-7.6-17-17z" fill="currentColor"/>
                                <path d="M3 5c0-1.1.9-2 2-2h3.5c.55 0 1.05.3 1.25.8l1.2 3c.2.5.05 1.1-.35 1.5L8.7 10.2c1.2 2.1 2.9 3.8 5 5l1.9-1.9c.4-.4 1-.55 1.5-.35l3 1.2c.5.2.8.7.8 1.25V19c0 1.1-.9 2-2 2-9.4 0-17-7.6-17-17z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input type="text" name="phone" id="phone" placeholder="081..."
                               class="form-input-solid">
                    </div>
                </div>
                <div>
                    <label for="address" class="block text-xs font-semibold text-gray-500 mb-1">Alamat</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Map Pin -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="currentColor"/>
                                <circle cx="12" cy="9" r="3" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <textarea name="address" id="address" rows="3" placeholder="Alamat lengkap..."
                                  class="form-input-solid"></textarea>
                    </div>
                </div>
                <div>
                    <label for="notes" class="block text-xs font-semibold text-gray-500 mb-1">Catatan</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Message/Chat -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" fill="currentColor"/>
                                <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <textarea name="notes" id="notes" rows="2" placeholder="Catatan opsional..."
                                  class="form-input-solid"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full py-3 font-bold rounded-lg transition-transform active:scale-[0.98]">Simpan Tengkulak</button>
            </form>
        </div>
    </div>
</x-app-layout>

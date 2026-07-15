<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('units.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Edit Satuan</h2>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 max-w-lg mx-auto">
        <div class="card-solid p-4">
            <form action="{{ route('units.update', $unit) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 mb-1">Nama Satuan</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Tag -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M12 2l9 9-9 9-9-9 9-9z" fill="currentColor"/>
                                <path d="M12 2l9 9-9 9-9-9 9-9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="7" r="1" fill="currentColor"/>
                            </svg>
                        </span>
                        <input type="text" name="name" id="name" value="{{ old('name', $unit->name) }}" required placeholder="Contoh: Kilogram, Karung..."
                               class="form-input-solid">
                    </div>
                </div>
                <div>
                    <label for="symbol" class="block text-xs font-semibold text-gray-500 mb-1">Simbol / Singkatan</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Hashtag -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M4 9H20M4 15H20" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                <path d="M9 3L7 21M17 3L15 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input type="text" name="symbol" id="symbol" value="{{ old('symbol', $unit->symbol) }}" required placeholder="Contoh: kg, karung, l..."
                               class="form-input-solid">
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full py-3 font-bold rounded-lg transition-transform active:scale-[0.98]">Perbarui Satuan</button>
            </form>
        </div>
    </div>
</x-app-layout>

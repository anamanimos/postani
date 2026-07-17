<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Satuan Barang</h2>
    </x-slot>

    <div class="py-5 pb-24 space-y-6 max-w-lg mx-auto">
        {{-- Form Tambah Satuan --}}
        <div class="card-solid p-4">
            <h3 class="text-sm font-semibold text-dark mb-3">Tambah Satuan Baru</h3>
            <form action="{{ route('units.store') }}" method="POST" class="space-y-4">
                @csrf
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
                        <input type="text" name="name" id="name" required placeholder="Contoh: Kilogram, Karung..."
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
                        <input type="text" name="symbol" id="symbol" required placeholder="Contoh: kg, karung, l..."
                               class="form-input-solid">
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full py-3 font-bold rounded-lg transition-transform active:scale-[0.98]">Simpan Satuan</button>
            </form>
        </div>

        {{-- Daftar Satuan --}}
        <div class="card-solid overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-150">
                <h3 class="text-sm font-semibold text-dark">Daftar Satuan</h3>
            </div>
            <div class="divide-y divide-gray-150">
                @forelse($units as $unit)
                <div class="px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-dark">{{ $unit->name }}</p>
                        <p class="text-xs text-gray-400">Simbol: <span class="font-bold text-primary-600">{{ $unit->symbol }}</span></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('units.edit', $unit) }}" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
                            ✏️
                        </a>
                        <form action="{{ route('units.destroy', $unit) }}" method="POST" class="confirm-delete" data-confirm="Hapus satuan ini?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors">
                                🗑️
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-400 text-sm">
                    Belum ada satuan.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>


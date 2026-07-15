<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Satuan Barang</h2>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-6">
        {{-- Form Tambah Satuan --}}
        <div class="glass-card p-4">
            <h3 class="text-sm font-semibold text-dark mb-3">Tambah Satuan Baru</h3>
            <form action="{{ route('units.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 mb-1">Nama Satuan</label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: Kilogram, Karung..."
                           class="form-input-glass">
                </div>
                <div>
                    <label for="symbol" class="block text-xs font-semibold text-gray-500 mb-1">Simbol / Singkatan</label>
                    <input type="text" name="symbol" id="symbol" required placeholder="Contoh: kg, karung, l..."
                           class="form-input-glass">
                </div>
                <button type="submit" class="btn-primary w-full">Simpan Satuan</button>
            </form>
        </div>

        {{-- Daftar Satuan --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Daftar Satuan</h3>
            </div>
            <div class="divide-y divide-gray-100">
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

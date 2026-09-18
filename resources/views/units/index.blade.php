<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-dark">Satuan Barang (Unit)</h2>
                <p class="text-xs text-gray-400 hidden sm:block">Kelola satuan beli dan jual untuk konversi stok barang</p>
            </div>
            <span class="text-xs font-semibold text-primary-700 bg-primary-50 border border-primary-200 px-3 py-1 rounded-full">
                {{ $units->count() }} Satuan
            </span>
        </div>
    </x-slot>

    <div class="py-4 pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- LEFT COLUMN: Form Tambah Satuan (Sticky on Desktop) --}}
            <div class="lg:col-span-5 lg:sticky lg:top-20">
                <div class="card-solid p-5">
                    <h3 class="text-sm font-bold text-dark mb-3 pb-2 border-b border-gray-150">Tambah Satuan Baru</h3>
                    <form action="{{ route('units.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-xs font-semibold text-gray-500 mb-1">Nama Satuan</label>
                            <div class="input-group-solid">
                                <span class="input-prefix">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M12 2l9 9-9 9-9-9 9-9z" fill="currentColor"/>
                                        <path d="M12 2l9 9-9 9-9-9 9-9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="12" cy="7" r="1" fill="currentColor"/>
                                    </svg>
                                </span>
                                <input type="text" name="name" id="name" required placeholder="Contoh: Kilogram, Karung, Botol, Liter..."
                                       class="form-input-solid">
                            </div>
                        </div>
                        <div>
                            <label for="symbol" class="block text-xs font-semibold text-gray-500 mb-1">Simbol / Singkatan</label>
                            <div class="input-group-solid">
                                <span class="input-prefix">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M4 9H20M4 15H20" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                        <path d="M9 3L7 21M17 3L15 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <input type="text" name="symbol" id="symbol" required placeholder="Contoh: kg, btl, ltr, sak, pcs..."
                                       class="form-input-solid">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full py-2.5 font-bold rounded-xl shadow-xs active:scale-[0.98] transition-transform">
                            + Simpan Satuan
                        </button>
                    </form>
                </div>
            </div>

            {{-- RIGHT COLUMN: Daftar Satuan --}}
            <div class="lg:col-span-7">
                <div class="card-solid overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-150 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-dark">Daftar Satuan Terdaftar</h3>
                        <span class="text-xs text-gray-400 font-medium">Total: {{ $units->count() }}</span>
                    </div>
                    <div class="divide-y divide-gray-150">
                        @forelse($units as $unit)
                        <div class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                            <div class="space-y-0.5">
                                <p class="text-sm font-bold text-dark">{{ $unit->name }}</p>
                                <p class="text-xs text-gray-400">Simbol: <span class="font-mono font-bold text-primary-600 bg-primary-50 px-1.5 py-0.2 rounded border border-primary-200">{{ $unit->symbol }}</span></p>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <a href="{{ route('units.edit', $unit) }}" 
                                   class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 text-gray-600 transition-colors"
                                   title="Edit Satuan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('units.destroy', $unit) }}" method="POST" class="confirm-delete" data-confirm="Hapus satuan '{{ $unit->name }}'? Produk yang menggunakan satuan ini dapat terpengaruh.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors"
                                            title="Hapus Satuan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center text-gray-400 text-sm">
                            Belum ada satuan barang.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

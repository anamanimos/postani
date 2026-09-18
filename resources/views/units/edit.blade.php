<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('units.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform hover:bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-dark">Edit Satuan</h2>
                    <p class="text-xs text-gray-400 hidden sm:block">Perbarui nama satuan dan simbol/singkatan</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('units.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 rounded-xl border border-gray-200 transition-colors shadow-2xs">
                    Batal
                </a>
                <button type="submit" form="unit-edit-form" class="btn-primary py-2 px-4 text-xs font-bold rounded-xl shadow-xs active:scale-95 transition-transform flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-5 pb-24 max-w-xl mx-auto">
        <div class="card-solid p-5 sm:p-6">
            <form id="unit-edit-form" action="{{ route('units.update', $unit) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-500 mb-1">Nama Satuan <span class="text-red-500">*</span></label>
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
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="symbol" class="block text-xs font-semibold text-gray-500 mb-1">Simbol / Singkatan <span class="text-red-500">*</span></label>
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
                        @error('symbol')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3 border-t border-gray-150">
                    <a href="{{ route('units.index') }}" class="px-5 py-2.5 text-xs font-semibold text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="btn-primary py-2.5 px-6 font-bold text-xs rounded-xl shadow-xs transition-transform active:scale-[0.98]">
                        Perbarui Satuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>


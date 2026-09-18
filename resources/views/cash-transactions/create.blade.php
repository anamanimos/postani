<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('cash-transactions.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform hover:bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-dark">Catat Kas Manual</h2>
                    <p class="text-xs text-gray-400 hidden sm:block">Pencatatan arus kas masuk atau kas keluar operasional</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('cash-transactions.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 rounded-xl border border-gray-200 transition-colors shadow-2xs">
                    Batal
                </a>
                <button type="submit" form="cash-create-form" class="btn-primary py-2 px-4 text-xs font-bold rounded-xl shadow-xs active:scale-95 transition-transform flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Transaksi</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-5 pb-24 max-w-2xl mx-auto">
        <div class="card-solid p-5 sm:p-6">
            <form id="cash-create-form" action="{{ route('cash-transactions.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Tipe Transaksi Kas <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="border border-gray-200 rounded-xl p-3.5 flex items-center justify-center gap-2.5 cursor-pointer bg-white hover:bg-emerald-50/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/30 transition-all shadow-2xs">
                            <input type="radio" name="type" value="in" checked required class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs sm:text-sm font-bold text-emerald-700">🟢 Uang Masuk (+)</span>
                        </label>
                        <label class="border border-gray-200 rounded-xl p-3.5 flex items-center justify-center gap-2.5 cursor-pointer bg-white hover:bg-red-50/50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30 transition-all shadow-2xs">
                            <input type="radio" name="type" value="out" required class="text-red-600 focus:ring-red-500">
                            <span class="text-xs sm:text-sm font-bold text-red-700">🔴 Uang Keluar (-)</span>
                        </label>
                    </div>
                    @error('type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Nominal <span class="text-red-500">*</span></label>
                        <div class="input-group-solid">
                            <span class="input-prefix font-bold text-gray-500">Rp</span>
                            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1" placeholder="0" class="form-input-solid font-semibold">
                        </div>
                        @error('amount')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-xs font-semibold text-gray-500 mb-1">Kategori Kas <span class="text-red-500">*</span></label>
                        <div class="input-group-solid">
                            <span class="input-prefix">
                                <!-- Duotone Icon: Tag -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 2l9 9-9 9-9-9 9-9z" fill="currentColor"/>
                                    <path d="M12 2l9 9-9 9-9-9 9-9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="7" r="1" fill="currentColor"/>
                                </svg>
                            </span>
                            <input type="text" name="category" id="category" value="{{ old('category') }}" required placeholder="Contoh: Listrik, Bensin, Prive, Modal..."
                                   class="form-input-solid">
                        </div>
                        @error('category')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="transaction_date" class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Calendar -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.3" x="3" y="6" width="18" height="15" rx="2" fill="currentColor"/>
                                <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <rect x="3" y="6" width="18" height="15" rx="2" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                        <input type="date" name="transaction_date" id="transaction_date" required value="{{ old('transaction_date', date('Y-m-d')) }}"
                               class="form-input-solid">
                    </div>
                    @error('transaction_date')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-gray-500 mb-1">Keterangan / Catatan Transaksi</label>
                    <div class="input-group-solid">
                        <span class="input-prefix !h-auto pt-3 items-start">
                            <!-- Duotone Icon: Message/Chat -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" fill="currentColor"/>
                                <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <textarea name="description" id="description" rows="3" placeholder="Rincian atau keterangan tambahan transaksi kas..." class="form-input-solid">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex items-center justify-end gap-3 border-t border-gray-150">
                    <a href="{{ route('cash-transactions.index') }}" class="px-5 py-2.5 text-xs font-semibold text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="btn-primary py-2.5 px-6 font-bold text-xs rounded-xl shadow-xs transition-transform active:scale-[0.98]">
                        Simpan Transaksi Kas
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>


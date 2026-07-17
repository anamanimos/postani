<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('cash-transactions.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Catat Kas Manual</h2>
        </div>
    </x-slot>

    <div class="py-5 pb-24">
        <div class="glass-card p-4">
            <form action="{{ route('cash-transactions.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tipe Transaksi</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="border rounded-xl p-3 flex items-center justify-center gap-2 cursor-pointer bg-white">
                            <input type="radio" name="type" value="in" checked required class="text-primary-600 focus:ring-primary-500">
                            <span class="text-sm font-semibold text-green-600">Uang Masuk (+)</span>
                        </label>
                        <label class="border rounded-xl p-3 flex items-center justify-center gap-2 cursor-pointer bg-white">
                            <input type="radio" name="type" value="out" required class="text-red-600 focus:ring-red-500">
                            <span class="text-sm font-semibold text-red-600">Uang Keluar (-)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="amount" class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Uang (Rp)</label>
                    <input type="number" name="amount" id="amount" required placeholder="0" class="form-input-glass">
                </div>

                <div>
                    <label for="category" class="block text-xs font-semibold text-gray-500 mb-1">Kategori Kas</label>
                    <input type="text" name="category" id="category" required placeholder="Contoh: Listrik, Gaji, Operasional, Prive..."
                           class="form-input-glass">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Transaksi</label>
                    <input type="text" name="transaction_date" id="transaction_date" required value="{{ date('Y-m-d') }}"
                           class="datepicker form-input-glass">
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-gray-500 mb-1">Deskripsi / Keterangan</label>
                    <textarea name="description" id="description" rows="3" placeholder="Detail transaksi kas..." class="form-input-glass"></textarea>
                </div>

                <button type="submit" class="btn-primary w-full py-3 font-bold">Simpan Transaksi</button>
            </form>
        </div>
    </div>
</x-app-layout>


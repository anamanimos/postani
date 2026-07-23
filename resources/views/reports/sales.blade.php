<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Laporan Penjualan</h2>
            <div class="flex gap-1 text-[10px]">
                <a href="{{ route('reports.sales') }}" class="px-2 py-1 rounded bg-primary-600 text-white font-semibold">Penjualan</a>
                <a href="{{ route('reports.purchases') }}" class="px-2 py-1 rounded bg-white text-gray-600 border border-gray-200">Pembelian</a>
                <a href="{{ route('reports.profit') }}" class="px-2 py-1 rounded bg-white text-gray-600 border border-gray-200">Laba Rugi</a>
                <a href="{{ route('reports.debts') }}" class="px-2 py-1 rounded bg-white text-gray-600 border border-gray-200">Hutang</a>
            </div>
        </div>
    </x-slot>

    <div class="py-5 pb-24 space-y-4">
        {{-- Filter --}}
        <div class="glass-card p-4">
            <form action="{{ route('reports.sales') }}" method="GET" class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Mulai</label>
                    <input type="text" name="date_from" value="{{ request('date_from', date('Y-m-d')) }}" class="datepicker form-input-glass py-1.5 px-3">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Sampai</label>
                    <input type="text" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}" class="datepicker form-input-glass py-1.5 px-3">
                </div>
                <div class="col-span-2 pt-2 flex gap-2">
                    <button type="submit" class="btn-primary py-2 text-xs flex-1">Filter</button>
                    <a href="{{ route('reports.sales') }}" class="btn-secondary py-2 text-xs text-center flex-1">Reset</a>
                </div>
            </form>
        </div>

        {{-- Summary Card --}}
        <div class="glass-card p-4 flex items-center justify-between bg-primary-50/10">
            <div>
                <p class="text-xs text-gray-500">Total Pendapatan Penjualan</p>
                <p class="text-lg font-bold text-primary-600">Rp {{ number_format($totalSalesAmount ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">Jumlah Transaksi</p>
                <p class="text-base font-bold text-dark">{{ $totalTransactions ?? 0 }}</p>
            </div>
        </div>

        {{-- List --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Rincian Transaksi</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($sales as $sale)
                <div class="p-3 flex items-center justify-between hover:bg-white/40 text-xs">
                    <a href="{{ route('sales.show', $sale) }}" class="flex-1">
                        <p class="font-semibold text-dark">{{ $sale->invoice_number }}</p>
                        <p class="text-gray-400">{{ $sale->sale_date->format('d/m/Y H:i') }} · {{ $sale->customer->name ?? 'Walk-in (Umum)' }}</p>
                    </a>
                    <div class="text-right flex items-center gap-2">
                        <div>
                            <p class="font-bold text-dark">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-gray-400 capitalize">{{ $sale->payment_method }}</p>
                        </div>
                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi {{ $sale->invoice_number }}? Stok produk akan dikembalikan secara otomatis.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Penjualan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">
                    Tidak ada transaksi penjualan pada periode ini.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>


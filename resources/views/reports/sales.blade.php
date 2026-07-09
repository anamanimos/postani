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

    <div class="px-4 py-5 pb-24 space-y-4">
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
                <a href="{{ route('sales.show', $sale) }}" class="p-3 flex items-center justify-between hover:bg-white/40 block text-xs">
                    <div>
                        <p class="font-semibold text-dark">{{ $sale->invoice_number }}</p>
                        <p class="text-gray-400">{{ $sale->sale_date->format('d/m/Y H:i') }} · {{ $sale->customer->name ?? 'Walk-in (Umum)' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-dark">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-gray-400 capitalize">{{ $sale->payment_method }}</p>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">
                    Tidak ada transaksi penjualan pada periode ini.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

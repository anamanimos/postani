<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Riwayat Penjualan</h2>
            <a href="{{ route('sales.create') }}" class="btn-primary flex items-center gap-2 text-xs py-2 px-4 rounded-full">
                <span>➕ Jual Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-5 pb-24 space-y-4">
        {{-- Filter Form --}}
        <div class="glass-card p-4">
            <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Mulai Tanggal</label>
                    <input type="text" name="date_from" value="{{ request('date_from') }}" class="datepicker form-input-glass py-1.5 px-3">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Sampai Tanggal</label>
                    <input type="text" name="date_to" value="{{ request('date_to') }}" class="datepicker form-input-glass py-1.5 px-3">
                </div>
                <div class="col-span-2 pt-2 flex gap-2">
                    <button type="submit" class="btn-primary py-2 text-xs flex-1">Filter</button>
                    <a href="{{ route('sales.index') }}" class="btn-secondary py-2 text-xs text-center flex-1">Reset</a>
                </div>
            </form>
        </div>

        {{-- Sales List --}}
        <div class="glass-card overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse($sales as $sale)
                <a href="{{ route('sales.show', $sale) }}" class="p-4 flex items-center justify-between hover:bg-white/40 transition-colors block">
                    <div>
                        <p class="text-sm font-semibold text-dark">{{ $sale->invoice_number }}</p>
                        <p class="text-xs text-gray-400">Pelanggan: <span class="font-medium text-dark">{{ $sale->customer->name ?? 'Walk-in (Umum)' }}</span></p>
                        <p class="text-xs text-gray-400">{{ $sale->sale_date->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-dark">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                        @if($sale->payment_status === 'paid')
                            <span class="badge-paid">Lunas</span>
                        @elseif($sale->payment_status === 'partial')
                            <span class="badge-partial">Sebagian</span>
                        @else
                            <span class="badge-unpaid">Hutang</span>
                        @endif
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">
                    Belum ada transaksi penjualan.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        @if($sales->hasPages())
        <div class="mt-4">
            {{ $sales->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</x-app-layout>


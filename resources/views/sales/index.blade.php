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
                <div class="p-4 flex items-center justify-between hover:bg-white/40 transition-colors">
                    <a href="{{ route('sales.show', $sale) }}" class="flex-1">
                        <p class="text-sm font-semibold text-dark">{{ $sale->invoice_number }}</p>
                        <p class="text-xs text-gray-400">Pelanggan: <span class="font-medium text-dark">{{ $sale->customer->name ?? 'Walk-in (Umum)' }}</span></p>
                        <p class="text-xs text-gray-400">{{ $sale->sale_date->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                    </a>
                    <div class="text-right flex items-center gap-2">
                        <div>
                            <p class="text-sm font-bold text-dark">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                            @if($sale->payment_status === 'paid')
                                <span class="badge-paid">Lunas</span>
                            @elseif($sale->payment_status === 'partial')
                                <span class="badge-partial">Sebagian</span>
                            @else
                                <span class="badge-unpaid">Hutang</span>
                            @endif
                        </div>
                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi {{ $sale->invoice_number }}? Stok produk akan dikembalikan secara otomatis.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Penjualan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
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


<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Riwayat Pembelian</h2>
    </x-slot>

    <div class="py-5 pb-24 space-y-4">
        {{-- Filter Form --}}
        <div class="glass-card p-4">
            <form action="{{ route('purchases.index') }}" method="GET" class="grid grid-cols-2 gap-2">
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
                    <a href="{{ route('purchases.index') }}" class="btn-secondary py-2 text-xs text-center flex-1">Reset</a>
                </div>
            </form>
        </div>

        {{-- Purchase List --}}
        <div class="glass-card overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse($purchases as $purchase)
                <a href="{{ route('purchases.show', $purchase) }}" class="p-4 flex items-center justify-between hover:bg-white/40 transition-colors block">
                    <div>
                        <p class="text-sm font-semibold text-dark">{{ $purchase->invoice_number }}</p>
                        <p class="text-xs text-gray-400">Tengkulak: <span class="font-medium text-dark">{{ $purchase->supplier->name }}</span></p>
                        <p class="text-xs text-gray-400">{{ $purchase->purchase_date->locale('id')->isoFormat('D MMM Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-dark">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</p>
                        @if($purchase->payment_status === 'paid')
                            <span class="badge-paid">Lunas</span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="badge-partial">Sebagian</span>
                        @else
                            <span class="badge-unpaid">Belum Lunas</span>
                        @endif
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">
                    Belum ada transaksi pembelian.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        @if($purchases->hasPages())
        <div class="mt-4">
            {{ $purchases->appends(request()->query())->links() }}
        </div>
        @endif
        
        {{-- Floating add button (placed at bottom-24) --}}
        <div class="fixed bottom-24 left-0 right-0 z-40 px-5 pointer-events-none">
            <div class="max-w-lg mx-auto flex justify-end">
                <a href="{{ route('purchases.create') }}" 
                   class="w-12 h-12 rounded-full bg-white/80 backdrop-blur-md border border-gray-200/80 text-primary-600 flex items-center justify-center shadow-lg active:scale-90 hover:bg-white transition-all transform hover:-translate-y-0.5 duration-150 pointer-events-auto"
                   title="Beli Barang Baru">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>
</x-app-layout>


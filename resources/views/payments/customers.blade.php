<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Piutang Pelanggan (Petani)</h2>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-4">
        {{-- List of outstanding sales grouped by customer --}}
        <div class="space-y-4">
            @forelse($sales as $customerId => $items)
                @php
                    $customer = $items->first()->customer;
                    $totalDue = $items->sum('due_amount');
                @endphp
                <div class="glass-card overflow-hidden" x-data="{ open: false }">
                    <div class="px-4 py-4 flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div>
                            <h3 class="text-sm font-bold text-dark">{{ $customer->name ?? 'Walk-in (Umum)' }}</h3>
                            <p class="text-xs text-gray-400">Total Piutang: <span class="text-accent-600 font-bold">Rp {{ number_format($totalDue, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-accent-100 text-accent-700 font-medium">{{ $items->count() }} nota</span>
                            <span x-text="open ? '▲' : '▼'" class="text-[10px] text-gray-400"></span>
                        </div>
                    </div>

                    <div x-show="open" x-transition class="border-t border-gray-100 divide-y divide-gray-50 bg-white/40">
                        @foreach($items as $sale)
                        <div class="p-3 flex items-center justify-between text-xs">
                            <div>
                                <p class="font-semibold text-dark">{{ $sale->invoice_number }}</p>
                                <p class="text-gray-400">{{ $sale->sale_date->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                                <p class="text-gray-400">Sisa: <span class="font-bold text-accent-600">Rp {{ number_format($sale->due_amount, 0, ',', '.') }}</span></p>
                            </div>
                            <a href="{{ route('sales.show', $sale) }}" class="btn-accent py-1 px-3 text-[10px] rounded-lg">Terima Bayar</a>
                        </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="glass-card p-8 text-center text-gray-400 text-sm">
                    ✅ Tidak ada piutang outstanding saat ini.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

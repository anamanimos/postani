<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Laporan Hutang & Piutang</h2>
            <div class="flex gap-1 text-[10px]">
                <a href="{{ route('reports.sales') }}" class="px-2 py-1 rounded bg-white text-gray-600 border border-gray-200">Penjualan</a>
                <a href="{{ route('reports.purchases') }}" class="px-2 py-1 rounded bg-white text-gray-600 border border-gray-200">Pembelian</a>
                <a href="{{ route('reports.profit') }}" class="px-2 py-1 rounded bg-white text-gray-600 border border-gray-200">Laba Rugi</a>
                <a href="{{ route('reports.debts') }}" class="px-2 py-1 rounded bg-primary-600 text-white font-semibold">Hutang</a>
            </div>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-4">
        {{-- Summaries --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="glass-card p-4 border-l-4 border-red-500">
                <p class="text-[10px] text-gray-400 mb-0.5">Total Hutang (Toko)</p>
                <p class="text-sm font-extrabold text-red-600">Rp {{ number_format($totalPayable ?? 0, 0, ',', '.') }}</p>
                <a href="{{ route('payments.suppliers') }}" class="inline-block mt-2 text-[9px] text-red-500 font-semibold underline">Detail Hutang →</a>
            </div>
            <div class="glass-card p-4 border-l-4 border-accent-500">
                <p class="text-[10px] text-gray-400 mb-0.5">Total Piutang (Petani)</p>
                <p class="text-sm font-extrabold text-accent-600">Rp {{ number_format($totalReceivable ?? 0, 0, ',', '.') }}</p>
                <a href="{{ route('payments.customers') }}" class="inline-block mt-2 text-[9px] text-accent-500 font-semibold underline">Detail Piutang →</a>
            </div>
        </div>

        {{-- Top Debts --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Hutang Toko ke Tengkulak</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($suppliersWithDebt as $supplier)
                <div class="px-4 py-3 flex items-center justify-between text-xs">
                    <span>{{ $supplier->name }}</span>
                    <span class="font-bold text-red-600">Rp {{ number_format($supplier->total_due, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="p-4 text-center text-gray-400 text-xs">
                    Tidak ada hutang ke tengkulak.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Top Receivables --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Piutang Pelanggan (Petani)</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($customersWithDebt as $customer)
                <div class="px-4 py-3 flex items-center justify-between text-xs">
                    <span>{{ $customer->name }}</span>
                    <span class="font-bold text-accent-600">Rp {{ number_format($customer->total_due, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="p-4 text-center text-gray-400 text-xs">
                    Tidak ada piutang pelanggan.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

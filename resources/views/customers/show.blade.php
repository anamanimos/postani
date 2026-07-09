<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('customers.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="text-lg font-bold text-dark">Detail Pelanggan</h2>
            </div>
            <a href="{{ route('customers.edit', $customer) }}" class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg active:scale-95 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-4">
        {{-- Profile --}}
        <div class="glass-card p-4 space-y-3">
            <div>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-semibold uppercase">Pelanggan</span>
                <h1 class="text-xl font-bold text-dark mt-1">{{ $customer->name }}</h1>
            </div>
            @if($customer->phone)
                <p class="text-sm text-gray-600">📞 <span class="font-medium">{{ $customer->phone }}</span></p>
            @endif
            @if($customer->address)
                <p class="text-sm text-gray-600">📍 {{ $customer->address }}</p>
            @endif
        </div>

        {{-- Piutang summary --}}
        @php
            $totalDue = $customer->sales->sum('due_amount');
        @endphp
        <div class="glass-card p-4 flex items-center justify-between border-l-4 {{ $totalDue > 0 ? 'border-accent-500 bg-accent-50/10' : 'border-green-500 bg-green-50/10' }}">
            <div>
                <p class="text-xs text-gray-500">Total Piutang Toko Ke Pelanggan Ini</p>
                <p class="text-lg font-bold {{ $totalDue > 0 ? 'text-accent-600' : 'text-green-600' }}">Rp {{ number_format($totalDue, 0, ',', '.') }}</p>
            </div>
            @if($totalDue > 0)
                <a href="{{ route('payments.customers') }}" class="btn-accent text-xs py-1.5 px-3 rounded-lg font-medium">Terima Cicilan</a>
            @endif
        </div>

        {{-- Sales history --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Riwayat Transaksi</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($sales as $sale)
                <div class="p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-dark">{{ $sale->invoice_number }}</p>
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
                    </div>
                    @if($sale->due_amount > 0)
                        <div class="flex items-center justify-between text-xs pt-1 border-t border-dashed border-gray-100 text-gray-500">
                            <span>Sisa Hutang Pelanggan:</span>
                            <span class="font-bold text-accent-600">Rp {{ number_format($sale->due_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">
                    Belum ada riwayat transaksi dengan pelanggan ini.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Delete Button --}}
        <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full py-3 border-2 border-red-200 text-red-600 font-medium rounded-glass text-sm hover:bg-red-50 transition-colors">
                Hapus Pelanggan
            </button>
        </form>
    </div>
</x-app-layout>

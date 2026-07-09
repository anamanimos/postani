<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Buku Kas Manual</h2>
            <a href="{{ route('cash-transactions.create') }}" class="btn-primary flex items-center gap-2 text-xs py-2 px-4 rounded-full">
                <span>➕ Transaksi Kas</span>
            </a>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-4">
        {{-- Balance summary --}}
        <div class="glass-card p-4 flex items-center justify-between bg-primary-50/10 border-l-4 border-primary-500">
            <div>
                <p class="text-xs text-gray-500">Saldo Kas Saat Ini</p>
                <p class="text-lg font-bold text-primary-600">Rp {{ number_format($balance ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <span>🏦</span>
            </div>
        </div>

        {{-- Transactions List --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Riwayat Kas</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($transactions as $transaction)
                <div class="p-3 flex items-center justify-between text-xs">
                    <div>
                        <p class="font-semibold text-dark">{{ $transaction->category }}</p>
                        <p class="text-gray-400">{{ $transaction->transaction_date->locale('id')->isoFormat('D MMM Y') }} · {{ $transaction->creator->name }}</p>
                        @if($transaction->description)
                            <p class="text-gray-400 italic mt-0.5">{{ $transaction->description }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        @if($transaction->type === 'in')
                            <span class="font-bold text-green-600">+ Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                        @else
                            <span class="font-bold text-red-600">- Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">
                    Belum ada transaksi kas manual.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

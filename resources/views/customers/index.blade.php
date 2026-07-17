<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Daftar Pelanggan (Petani)</h2>
            <a href="{{ route('customers.create') }}" class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg active:scale-95 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </a>
        </div>
    </x-slot>

    <div class="py-5 pb-24 space-y-4">
        <div class="glass-card overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                <a href="{{ route('customers.show', $customer) }}" class="px-4 py-4 flex items-center justify-between hover:bg-white/40 transition-colors block">
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-dark">{{ $customer->name }}</p>
                        @if($customer->phone)
                            <p class="text-xs text-gray-500">📞 {{ $customer->phone }}</p>
                        @endif
                        @if($customer->address)
                            <p class="text-xs text-gray-400 line-clamp-1">📍 {{ $customer->address }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        @php
                            $totalDue = $customer->sales->sum('due_amount');
                        @endphp
                        @if($totalDue > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-accent-100 text-accent-700 font-semibold whitespace-nowrap">
                                Piutang: Rp {{ number_format($totalDue, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold whitespace-nowrap">
                                Lunas
                            </span>
                        @endif
                        <p class="text-[10px] text-gray-400 mt-1">Detail →</p>
                    </div>
                </a>
                @empty
                <div class="px-4 py-8 text-center text-gray-400 text-sm">
                    Belum ada pelanggan terdaftar.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>


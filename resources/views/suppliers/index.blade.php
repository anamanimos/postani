<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Daftar Tengkulak</h2>
            <a href="{{ route('suppliers.create') }}" class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg active:scale-95 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </a>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-4">
        {{-- Search or List --}}
        <div class="glass-card overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse($suppliers as $supplier)
                <a href="{{ route('suppliers.show', $supplier) }}" class="px-4 py-4 flex items-center justify-between hover:bg-white/40 transition-colors block">
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-dark">{{ $supplier->name }}</p>
                        @if($supplier->phone)
                            <p class="text-xs text-gray-500">📞 {{ $supplier->phone }}</p>
                        @endif
                        @if($supplier->address)
                            <p class="text-xs text-gray-400 line-clamp-1">📍 {{ $supplier->address }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        @php
                            $totalDue = $supplier->purchases->sum('due_amount');
                        @endphp
                        @if($totalDue > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold whitespace-nowrap">
                                Hutang: Rp {{ number_format($totalDue, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold whitespace-nowrap">
                                Lunas / Aman
                            </span>
                        @endif
                        <p class="text-[10px] text-gray-400 mt-1">Detail →</p>
                    </div>
                </a>
                @empty
                <div class="px-4 py-8 text-center text-gray-400 text-sm">
                    Belum ada tengkulak/supplier.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

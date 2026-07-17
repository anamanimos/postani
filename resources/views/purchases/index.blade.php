<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Riwayat Pembelian</h2>
    </x-slot>

    <div class="py-5 pb-24 space-y-4" x-data="{ showFilterModal: false }">

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

        {{-- Floating Filter Button (placed at top-right, top-3) --}}
        <div class="fixed top-3 left-0 right-0 z-40 px-5 pointer-events-none">
            <div class="max-w-lg mx-auto flex justify-end">
                <button type="button" 
                        @click="showFilterModal = true"
                        class="w-12 h-12 rounded-full bg-white/80 backdrop-blur-md border border-gray-200/80 text-primary-600 flex items-center justify-center shadow-lg active:scale-90 hover:bg-white transition-all transform hover:-translate-y-0.5 duration-150 pointer-events-auto relative"
                        title="Filter Tanggal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    
                    {{-- Active indicator dot --}}
                    @if(request('date_from') || request('date_to'))
                        <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 rounded-full bg-accent-500 shadow-sm animate-pulse"></span>
                    @endif
                </button>
            </div>
        </div>

        <!-- Modal Filter Tanggal -->
        <div x-show="showFilterModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
             {{-- Backdrop --}}
             <div class="fixed inset-0 bg-black/40 backdrop-blur-sm pointer-events-auto" @click="showFilterModal = false"></div>
             
             {{-- Modal content wrapper (aligned to bottom for mobile look) --}}
             <div class="flex items-end justify-center min-h-screen p-4 sm:items-center">
                  <div class="bg-white rounded-t-2xl sm:rounded-2xl max-w-sm w-full p-5 shadow-2xl relative z-10 transform transition-all duration-300 ease-out border border-gray-150 max-h-[70vh] flex flex-col pointer-events-auto"
                       x-show="showFilterModal"
                       x-transition:enter="transition ease-out duration-300 transform"
                       x-transition:enter-start="translate-y-full sm:scale-95"
                       x-transition:enter-end="translate-y-0 sm:scale-100"
                       x-transition:leave="transition ease-in duration-200 transform"
                       x-transition:leave-start="translate-y-0 sm:scale-100"
                       x-transition:leave-end="translate-y-full sm:scale-95">
                       
                       <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4 flex-shrink-0">
                            <h3 class="text-sm font-bold text-dark">Filter Riwayat Pembelian</h3>
                            <button @click="showFilterModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                       </div>
                       
                       <form action="{{ route('purchases.index') }}" method="GET" class="space-y-4">
                           <div class="grid grid-cols-2 gap-3">
                               <div>
                                   <label class="block text-[10px] font-semibold text-gray-500 mb-1">Mulai Tanggal</label>
                                   <input type="text" name="date_from" value="{{ request('date_from') }}" class="datepicker form-input-glass py-1.5 px-3">
                               </div>
                               <div>
                                   <label class="block text-[10px] font-semibold text-gray-500 mb-1">Sampai Tanggal</label>
                                   <input type="text" name="date_to" value="{{ request('date_to') }}" class="datepicker form-input-glass py-1.5 px-3">
                               </div>
                           </div>
                           
                           <div class="pt-2 flex gap-2">
                               <button type="submit" class="btn-primary py-2 text-xs flex-1">Terapkan Filter</button>
                               <a href="{{ route('purchases.index') }}" class="btn-secondary py-2 text-xs text-center flex-1">Reset</a>
                           </div>
                       </form>
                  </div>
             </div>
        </div>

    </div>
</x-app-layout>


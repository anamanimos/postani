<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-dark">Selamat Datang! 👋</h2>
                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </x-slot>

    <div class="py-5 pb-24 space-y-5">
        {{-- Sliding Stat Cards --}}
        <div x-data="{ 
                 activeSlide: 0, 
                 totalSlides: 4,
                 touchStart: 0,
                 touchEnd: 0,
                 handleTouchStart(e) {
                     this.touchStart = e.changedTouches[0].screenX;
                 },
                 handleTouchEnd(e) {
                     this.touchEnd = e.changedTouches[0].screenX;
                     this.handleSwipe();
                 },
                 handleSwipe() {
                     const threshold = 50;
                     if (this.touchStart - this.touchEnd > threshold) {
                         if (this.activeSlide < this.totalSlides - 1) this.activeSlide++;
                     } else if (this.touchEnd - this.touchStart > threshold) {
                         if (this.activeSlide > 0) this.activeSlide--;
                     }
                 }
             }"
             @touchstart="handleTouchStart($event)"
             @touchend="handleTouchEnd($event)"
             class="relative overflow-hidden w-full select-none py-2 -mx-3">
             
             <!-- Slide container -->
             <div class="flex transition-transform duration-300 ease-out animate-fadeIn" :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
                 
                 {{-- Slide 1: Penjualan Hari Ini --}}
                 <div class="w-full flex-shrink-0 px-3">
                     <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/75 to-emerald-50/40 relative overflow-hidden" style="backdrop-filter: blur(12px);">
                         <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-emerald-500/10 blur-xl"></div>
                         <div class="flex items-center justify-between mb-3">
                             <span class="text-xs font-bold text-emerald-700 tracking-wide uppercase">Penjualan Hari Ini</span>
                             <div class="w-9 h-9 rounded-xl bg-emerald-100/80 flex items-center justify-center shadow-sm">
                                 <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                             </div>
                         </div>
                         <p class="text-2xl font-extrabold text-dark tracking-tight">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</p>
                         <p class="text-[10px] text-gray-400 mt-2">Akumulasi omset dari penjualan yang diselesaikan hari ini</p>
                     </div>
                 </div>

                 {{-- Slide 2: Transaksi Hari Ini --}}
                 <div class="w-full flex-shrink-0 px-3">
                     <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/75 to-blue-50/40 relative overflow-hidden" style="backdrop-filter: blur(12px);">
                         <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-blue-500/10 blur-xl"></div>
                         <div class="flex items-center justify-between mb-3">
                             <span class="text-xs font-bold text-blue-700 tracking-wide uppercase">Transaksi Hari Ini</span>
                             <div class="w-9 h-9 rounded-xl bg-blue-100/80 flex items-center justify-center shadow-sm">
                                 <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                             </div>
                         </div>
                         <p class="text-2xl font-extrabold text-dark tracking-tight">{{ $todayTransactions ?? 0 }} <span class="text-sm font-normal text-gray-400">Nota</span></p>
                         <p class="text-[10px] text-gray-400 mt-2">Jumlah nota kasir yang berhasil diproses hari ini</p>
                     </div>
                 </div>

                 {{-- Slide 3: Total Piutang --}}
                 <div class="w-full flex-shrink-0 px-3">
                     <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/75 to-orange-50/40 relative overflow-hidden" style="backdrop-filter: blur(12px);">
                         <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-orange-500/10 blur-xl"></div>
                         <div class="flex items-center justify-between mb-3">
                             <span class="text-xs font-bold text-orange-700 tracking-wide uppercase">Total Piutang (Receivables)</span>
                             <div class="w-9 h-9 rounded-xl bg-orange-100/80 flex items-center justify-center shadow-sm">
                                 <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                             </div>
                         </div>
                         <p class="text-2xl font-extrabold text-orange-600 tracking-tight">Rp {{ number_format($totalReceivables ?? 0, 0, ',', '.') }}</p>
                         <p class="text-[10px] text-gray-400 mt-2">Tagihan piutang dari pelanggan yang belum dibayar</p>
                     </div>
                 </div>

                 {{-- Slide 4: Total Hutang --}}
                 <div class="w-full flex-shrink-0 px-3">
                     <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/75 to-red-50/40 relative overflow-hidden" style="backdrop-filter: blur(12px);">
                         <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-red-500/10 blur-xl"></div>
                         <div class="flex items-center justify-between mb-3">
                             <span class="text-xs font-bold text-red-700 tracking-wide uppercase">Total Hutang (Payables)</span>
                             <div class="w-9 h-9 rounded-xl bg-red-100/80 flex items-center justify-center shadow-sm">
                                 <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                             </div>
                         </div>
                         <p class="text-2xl font-extrabold text-red-600 tracking-tight">Rp {{ number_format($totalPayables ?? 0, 0, ',', '.') }}</p>
                         <p class="text-[10px] text-gray-400 mt-2">Tunggakan hutang pembelian ke supplier/tengkulak</p>
                     </div>
                 </div>
                 
             </div>

             <!-- Indicator Dots -->
             <div class="flex justify-center gap-1.5 mt-3.5">
                 <template x-for="i in totalSlides" :key="i - 1">
                     <button @click="activeSlide = i - 1" 
                             class="h-1.5 rounded-full transition-all duration-200"
                             :class="activeSlide === (i - 1) ? 'w-4 bg-primary-600' : 'w-1.5 bg-gray-300'"></button>
                 </template>
             </div>
        </div>

        {{-- Low Stock Alert --}}
        @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
        <div class="rounded-glass border border-white/40 shadow-glass overflow-hidden" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
            <div class="px-4 py-3 border-b border-white/30 flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-dark">Stok Menipis</h3>
                <span class="ml-auto text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium">{{ $lowStockProducts->count() }}</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($lowStockProducts as $product)
                <div class="px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                            @else
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-dark">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400">Min: {{ $product->min_stock }} {{ $product->sellUnit->symbol ?? '' }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-red-600">{{ $product->stock }} <span class="text-xs font-normal">{{ $product->sellUnit->symbol ?? '' }}</span></span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recent Sales --}}
        <div class="rounded-glass border border-white/40 shadow-glass overflow-hidden" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
            <div class="px-4 py-3 border-b border-white/30 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-dark">Penjualan Terbaru</h3>
                <a href="{{ route('sales.index') }}" class="text-xs text-primary-600 font-medium">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentSales ?? [] as $sale)
                <a href="{{ route('sales.show', $sale) }}" class="px-4 py-3 flex items-center justify-between hover:bg-white/40 transition-colors">
                    <div>
                        <p class="text-sm font-medium text-dark">{{ $sale->invoice_number }}</p>
                        <p class="text-xs text-gray-400">{{ $sale->created_at->locale('id')->diffForHumans() }}
                            @if($sale->customer)
                                · {{ $sale->customer->name }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-dark">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                        @if($sale->payment_status === 'paid')
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-primary-100 text-primary-700">Lunas</span>
                        @elseif($sale->payment_status === 'partial')
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700">Sebagian</span>
                        @else
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">Belum Bayar</span>
                        @endif
                    </div>
                </a>
                @empty
                <div class="px-4 py-8 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm text-gray-400">Belum ada penjualan hari ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>


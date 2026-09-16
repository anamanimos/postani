@push('styles')
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-dark">Selamat Datang!</h2>
                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </x-slot>

    <div class="py-5 pb-24 space-y-5">
        {{-- Mobile: Sliding Stat Cards using Native CSS Scroll Snap (Hidden on desktop) --}}
        <div class="md:hidden">
            <div x-data="{ 
                     activeSlide: 0, 
                     totalSlides: 4,
                     autoplayInterval: null,
                     isScrolling: false,
                     startAutoplay() {
                         this.stopAutoplay();
                         this.autoplayInterval = setInterval(() => {
                             const next = (this.activeSlide + 1) % this.totalSlides;
                             this.selectSlide(next);
                         }, 4000);
                     },
                     stopAutoplay() {
                         if (this.autoplayInterval) {
                             clearInterval(this.autoplayInterval);
                             this.autoplayInterval = null;
                         }
                     },
                     selectSlide(index) {
                         this.stopAutoplay();
                         this.activeSlide = index;
                         const el = this.$refs.slider;
                         if (el) {
                             const slideEl = el.querySelector('.snap-center');
                             if (slideEl) {
                                 const slideWidth = slideEl.offsetWidth;
                                 this.isScrolling = true;
                                 el.scrollTo({ left: index * slideWidth, behavior: 'smooth' });
                                 setTimeout(() => { this.isScrolling = false; }, 400);
                             }
                         }
                         this.startAutoplay();
                     },
                     updateActiveSlide() {
                         if (this.isScrolling) return;
                         const el = this.$refs.slider;
                         if (el) {
                             const slideEl = el.querySelector('.snap-center');
                             if (slideEl) {
                                 const slideWidth = slideEl.offsetWidth;
                                 const index = Math.round(el.scrollLeft / slideWidth);
                                 if (this.activeSlide !== index && index >= 0 && index < this.totalSlides) {
                                     this.activeSlide = index;
                                 }
                             }
                         }
                     }
                 }"
                 x-init="startAutoplay()"
                 class="w-full relative select-none">
                 
                 <!-- Scrollable snap-x Container -->
                 <div x-ref="slider"
                      @scroll="updateActiveSlide()"
                      @touchstart="stopAutoplay()"
                      @touchend="startAutoplay()"
                      class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth py-2 -mx-3 no-scrollbar"
                      style="scrollbar-width: none; -ms-overflow-style: none;">
                      
                      <div class="shrink-0" style="width: 2%"></div>
                      
                      {{-- Slide 1: Penjualan Hari Ini --}}
                      <div class="shrink-0 snap-center px-2" style="width: 96%">
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
                      <div class="shrink-0 snap-center px-2" style="width: 96%">
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
                      <div class="shrink-0 snap-center px-2" style="width: 96%">
                          <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/75 to-orange-50/40 relative overflow-hidden" style="backdrop-filter: blur(12px);">
                              <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-orange-500/10 blur-xl"></div>
                              <div class="flex items-center justify-between mb-3">
                                  <span class="text-xs font-bold text-orange-700 tracking-wide uppercase">Total Piutang</span>
                                  <div class="w-9 h-9 rounded-xl bg-orange-100/80 flex items-center justify-center shadow-sm">
                                      <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                  </div>
                              </div>
                              <p class="text-2xl font-extrabold text-orange-600 tracking-tight">Rp {{ number_format($totalReceivables ?? 0, 0, ',', '.') }}</p>
                              <p class="text-[10px] text-gray-400 mt-2">Tagihan piutang dari pelanggan yang belum dibayar</p>
                          </div>
                      </div>

                      {{-- Slide 4: Total Hutang --}}
                      <div class="shrink-0 snap-center px-2" style="width: 96%">
                          <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/75 to-red-50/40 relative overflow-hidden" style="backdrop-filter: blur(12px);">
                              <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-red-500/10 blur-xl"></div>
                              <div class="flex items-center justify-between mb-3">
                                  <span class="text-xs font-bold text-red-700 tracking-wide uppercase">Total Hutang</span>
                                  <div class="w-9 h-9 rounded-xl bg-red-100/80 flex items-center justify-center shadow-sm">
                                      <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                  </div>
                              </div>
                              <p class="text-2xl font-extrabold text-red-600 tracking-tight">Rp {{ number_format($totalPayables ?? 0, 0, ',', '.') }}</p>
                              <p class="text-[10px] text-gray-400 mt-2">Tunggakan hutang pembelian ke supplier/tengkulak</p>
                          </div>
                      </div>
                      
                      <div class="shrink-0" style="width: 2%"></div>
                 </div>

                 <!-- Indicator Dots -->
                 <div class="flex justify-center gap-1.5 mt-3.5">
                     <template x-for="i in totalSlides" :key="i - 1">
                         <button @click="selectSlide(i - 1)" 
                                 class="h-1.5 rounded-full transition-all duration-200"
                                 :class="activeSlide === (i - 1) ? 'w-4 bg-primary-600' : 'w-1.5 bg-gray-300'"></button>
                     </template>
                 </div>
            </div>
        </div>

        {{-- Desktop: 4-Column Stat Cards Grid (Hidden on mobile < 768px, visible on md: and up) --}}
        <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Penjualan Hari Ini --}}
            <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/80 to-emerald-50/40 relative overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md" style="backdrop-filter: blur(12px);">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-emerald-500/10 blur-xl"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-emerald-700 tracking-wide uppercase">Penjualan Hari Ini</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-100/80 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-extrabold text-dark tracking-tight">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</p>
                <p class="text-[11px] text-gray-400 mt-2">Akumulasi omset dari penjualan yang diselesaikan hari ini</p>
            </div>

            {{-- Card 2: Transaksi Hari Ini --}}
            <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/80 to-blue-50/40 relative overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md" style="backdrop-filter: blur(12px);">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-blue-500/10 blur-xl"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-blue-700 tracking-wide uppercase">Transaksi Hari Ini</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-100/80 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-extrabold text-dark tracking-tight">{{ $todayTransactions ?? 0 }} <span class="text-sm font-normal text-gray-400">Nota</span></p>
                <p class="text-[11px] text-gray-400 mt-2">Jumlah nota kasir yang berhasil diproses hari ini</p>
            </div>

            {{-- Card 3: Total Piutang --}}
            <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/80 to-orange-50/40 relative overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md" style="backdrop-filter: blur(12px);">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-orange-500/10 blur-xl"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-orange-700 tracking-wide uppercase">Total Piutang</span>
                    <div class="w-9 h-9 rounded-xl bg-orange-100/80 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-extrabold text-orange-600 tracking-tight">Rp {{ number_format($totalReceivables ?? 0, 0, ',', '.') }}</p>
                <p class="text-[11px] text-gray-400 mt-2">Tagihan piutang dari pelanggan yang belum dibayar</p>
            </div>

            {{-- Card 4: Total Hutang --}}
            <div class="p-5 rounded-glass border border-white/40 shadow-glass bg-gradient-to-br from-white/80 to-red-50/40 relative overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md" style="backdrop-filter: blur(12px);">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-red-500/10 blur-xl"></div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-red-700 tracking-wide uppercase">Total Hutang</span>
                    <div class="w-9 h-9 rounded-xl bg-red-100/80 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-extrabold text-red-600 tracking-tight">Rp {{ number_format($totalPayables ?? 0, 0, ',', '.') }}</p>
                <p class="text-[11px] text-gray-400 mt-2">Tunggakan hutang pembelian ke supplier/tengkulak</p>
            </div>
        </div>

        {{-- Desktop Multi-column Layout / Mobile Stacked Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Left Column: Recent Sales (2 cols on desktop) --}}
            <div class="lg:col-span-2 space-y-5">
                {{-- Recent Sales --}}
                <div class="rounded-glass border border-white/40 shadow-glass overflow-hidden" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(12px);">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-primary-100 flex items-center justify-center text-primary-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-dark">Penjualan Terbaru</h3>
                        </div>
                        <a href="{{ route('sales.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-semibold flex items-center gap-1">
                            <span>Lihat Semua</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentSales ?? [] as $sale)
                        <a href="{{ route('sales.show', $sale) }}" class="px-5 py-3.5 flex items-center justify-between hover:bg-white/50 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-dark">{{ $sale->invoice_number }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $sale->created_at->locale('id')->diffForHumans() }}
                                    @if($sale->customer)
                                        · <span class="font-medium text-gray-600">{{ $sale->customer->name }}</span>
                                    @else
                                        · <span class="text-gray-400">Umum</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-dark">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                                @if($sale->payment_status === 'paid')
                                    <span class="badge-paid text-[10px] px-2 py-0.5 mt-0.5">Lunas</span>
                                @elseif($sale->payment_status === 'partial')
                                    <span class="badge-partial text-[10px] px-2 py-0.5 mt-0.5">Sebagian</span>
                                @else
                                    <span class="badge-unpaid text-[10px] px-2 py-0.5 mt-0.5">Belum Bayar</span>
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

            {{-- Right Column: Low Stock Alert & Quick Links (1 col on desktop) --}}
            <div class="space-y-5">
                {{-- Low Stock Alert --}}
                @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                <div class="rounded-glass border border-white/40 shadow-glass overflow-hidden" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(12px);">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-dark">Stok Menipis</h3>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-bold">{{ $lowStockProducts->count() }}</span>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                        @foreach($lowStockProducts as $product)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-dark">{{ $product->name }}</p>
                                    <p class="text-[10px] text-gray-400">Min: {{ $product->min_stock }} {{ $product->sellUnit->symbol ?? '' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-red-600">{{ $product->stock }} <span class="text-[10px] font-normal text-gray-500">{{ $product->sellUnit->symbol ?? '' }}</span></span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Desktop Quick Actions Card --}}
                <div class="hidden md:block rounded-glass border border-white/40 shadow-glass p-5" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(12px);">
                    <h3 class="text-sm font-bold text-dark mb-3">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('sales.create') }}" class="p-3 rounded-xl bg-primary-50/80 hover:bg-primary-100/80 text-primary-800 transition-colors border border-primary-100 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center text-primary-600 mb-1.5">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M5.4 5L7 13H17L21 5H5.4Z" fill="currentColor"/>
                                    <path d="M3 3H5.4L7 13H17L21 5H5.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="9" cy="20" r="1.5" fill="currentColor"/>
                                    <circle cx="17" cy="20" r="1.5" fill="currentColor"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold">Kasir Jual</span>
                        </a>
                        <a href="{{ route('purchases.create') }}" class="p-3 rounded-xl bg-blue-50/80 hover:bg-blue-100/80 text-blue-800 transition-colors border border-blue-100 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 mb-1.5">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                                    <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold">Beli Stok</span>
                        </a>
                        <a href="{{ route('products.create') }}" class="p-3 rounded-xl bg-emerald-50/80 hover:bg-emerald-100/80 text-emerald-800 transition-colors border border-emerald-100 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 mb-1.5">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle opacity="0.3" cx="12" cy="12" r="10" fill="currentColor"/>
                                    <path d="M12 8V16M8 12H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold">Tambah Produk</span>
                        </a>
                        <a href="{{ route('reports.sales') }}" class="p-3 rounded-xl bg-orange-50/80 hover:bg-orange-100/80 text-orange-800 transition-colors border border-orange-100 flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 mb-1.5">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 4C3 3.44772 3.44772 3 4 3H20C20.5523 3 21 3.44772 21 4V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V4Z" fill="currentColor"/>
                                    <path d="M3 20H21M7 16V12M12 16V8M17 16V5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold">Laporan Omset</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


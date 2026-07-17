<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Produk</h2>
    </x-slot>

    <div class="py-3 pb-28 space-y-3" x-data="productList()" @scroll.window="checkScroll()">

        {{-- Category Filter Chips --}}
        @if(isset($categories) && $categories->count() > 0)
        <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-hide">
            <a href="{{ route('products.index') }}"
                class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-medium transition-all {{ !request('category') ? 'bg-primary-600 text-white shadow-lg' : 'bg-white/60 text-gray-600 border border-white/40' }}">
                Semua
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('products.index', ['category' => $cat->id, 'search' => request('search')]) }}"
                class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-medium transition-all whitespace-nowrap {{ request('category') == $cat->id ? 'bg-primary-600 text-white shadow-lg' : 'bg-white/60 text-gray-600 border border-white/40' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- Active Search Indicator --}}
        <div x-show="activeSearch" x-transition class="glass-card px-4 py-2.5 flex items-center justify-between text-xs font-semibold" style="display: none;">
            <div class="flex items-center gap-1 text-gray-500">
                <span>Hasil pencarian:</span>
                <span class="bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded text-[10px]" x-text="'&quot;' + activeSearch + '&quot;'"></span>
            </div>
            <a :href="clearSearchUrl" class="text-[11px] font-bold text-red-500 hover:text-red-700 transition-colors flex-shrink-0">Reset</a>
        </div>

        {{-- Product Grid (Infinite Scroll) --}}
        <div class="grid grid-cols-2 gap-3">
            <template x-for="product in items" :key="product.id">
                <a :href="product.show_url" class="card-solid overflow-hidden block active:scale-[0.98] transition-transform">
                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                        <template x-if="product.image">
                            <img :src="'/storage/' + product.image" class="w-full h-full object-cover" :alt="product.name" loading="lazy">
                        </template>
                        <template x-if="!product.image">
                            <div class="w-full h-full flex items-center justify-center">
                                <!-- Duotone Icon: Package -->
                                <svg class="w-12 h-12 text-gray-300" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                                    <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </template>
                        {{-- Stock Badge --}}
                        <template x-if="product.stock <= product.min_stock">
                            <span class="absolute top-2 right-2 text-[10px] px-1.5 py-0.5 rounded-full bg-red-500 text-white font-medium">Stok Rendah</span>
                        </template>
                        <template x-if="!product.is_active">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <span class="text-xs text-white font-medium bg-black/60 px-2 py-1 rounded">Nonaktif</span>
                            </div>
                        </template>
                    </div>
                    <div class="p-3">
                        <p class="text-xs text-gray-400 mb-0.5" x-text="product.category_name"></p>
                        <p class="text-sm font-semibold text-dark leading-tight line-clamp-2 mb-1" x-text="product.name"></p>
                        <p class="text-sm font-bold text-primary-600" x-text="formatRupiah(product.selling_price)"></p>
                        <p class="text-xs text-gray-400 mt-1" x-text="'Stok: ' + formatNumber(product.stock) + ' ' + product.sell_unit_symbol"></p>
                    </div>
                </a>
            </template>

            {{-- Empty State (only when no items after initial load) --}}
            <template x-if="items.length === 0 && !loading">
                <div class="col-span-2 py-12 text-center">
                    <!-- Duotone Icon: Package empty -->
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                        <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="text-sm text-gray-400">Belum ada produk</p>
                    <a href="{{ route('products.create') }}" class="inline-block mt-3 px-4 py-2 bg-primary-600 text-white text-sm rounded-full">+ Tambah Produk</a>
                </div>
            </template>
        </div>

        {{-- Loading spinner --}}
        <div x-show="loading" class="flex justify-center py-6" style="display: none;">
            <svg class="w-6 h-6 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>

        {{-- Floating Action Buttons --}}
        {{-- 1. Floating add button (bottom-40) --}}
        <div x-show="showFloatingButtons" x-transition:enter="transition ease-out duration-150 transform"
             x-transition:enter-start="opacity-0 translate-y-10 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-100 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-10 scale-95"
             class="fixed bottom-40 left-0 right-0 z-40 px-5 pointer-events-none" style="display: none;">
            <div class="max-w-lg mx-auto flex justify-end">
                <a href="{{ route('products.create') }}"
                   class="w-12 h-12 rounded-full bg-white/80 backdrop-blur-md border border-gray-200/80 text-primary-600 flex items-center justify-center shadow-lg active:scale-90 hover:bg-white transition-all transform hover:-translate-y-0.5 duration-150 pointer-events-auto"
                   title="Tambah Produk Baru">
                    <!-- Duotone Icon: Plus -->
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle opacity="0.3" cx="12" cy="12" r="10" fill="currentColor"/>
                        <path d="M12 8V16M8 12H16" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- 2. Floating search button (bottom-24) --}}
        <div class="fixed bottom-24 left-0 right-0 z-40 px-5 pointer-events-none">
            <div class="max-w-lg mx-auto relative flex justify-end h-12">
                <div class="absolute right-0 top-0 bg-white/95 backdrop-blur-md border border-gray-200/80 shadow-lg rounded-full overflow-hidden transition-all duration-300 ease-out pointer-events-auto"
                     :class="openFloatingSearch ? 'w-full h-12' : 'w-12 h-12'">
                     
                     {{-- Collapsed Button --}}
                     <button type="button" x-show="!openFloatingSearch"
                             @click="openFloatingSearch = true; $nextTick(() => $refs.floatSearchInput.focus())"
                             class="w-full h-full flex items-center justify-center text-primary-600 active:scale-95 transition-transform duration-150"
                             title="Cari Produk">
                         <!-- Duotone Icon: Search -->
                         <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <circle opacity="0.3" cx="11" cy="11" r="7" fill="currentColor"/>
                             <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                             <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                         </svg>
                     </button>
                     
                     {{-- Expanded Form --}}
                     <form x-show="openFloatingSearch" @submit.prevent="submitSearch()"
                           class="w-full h-full flex items-center px-4 gap-2.5" style="display: none;">
                         <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                         </svg>
                         <input type="text" x-model="searchQuery" x-ref="floatSearchInput"
                                placeholder="Cari nama produk..."
                                @keydown.escape="cancelSearch()"
                                class="flex-1 bg-transparent border-0 outline-none text-xs font-semibold text-gray-700 placeholder-gray-400 focus:ring-0 p-0">
                         <button type="submit" class="text-xs text-primary-600 hover:text-primary-700 font-bold flex-shrink-0 px-1 py-1">Cari</button>
                         <button type="button" 
                                 @click="cancelSearch()"
                                 class="text-xs text-gray-400 hover:text-gray-600 font-bold flex-shrink-0 px-1 py-1">
                             Batal
                         </button>
                     </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function productList() {
            const initialItems = {!! json_encode($products->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'selling_price' => (float) $p->selling_price,
                'stock' => (float) $p->stock,
                'min_stock' => (float) ($p->min_stock ?? 0),
                'is_active' => (bool) $p->is_active,
                'image' => $p->image,
                'category_name' => $p->category->name ?? 'Tanpa Kategori',
                'sell_unit_symbol' => $p->sellUnit->symbol ?? '',
                'show_url' => route('products.show', $p),
            ])) !!};
            const nextPageUrl = {!! json_encode($products->nextPageUrl()) !!};

            return {
                items: initialItems,
                nextPageUrl: nextPageUrl,
                loading: false,
                showFloatingButtons: false,
                openFloatingSearch: false,
                searchQuery: '{{ request('search', '') }}',
                activeSearch: '{{ request('search', '') }}',
                activeCategory: '{{ request('category', '') }}',

                get clearSearchUrl() {
                    let params = new URLSearchParams(window.location.search);
                    params.delete('search');
                    const qs = params.toString();
                    return '{{ route('products.index') }}' + (qs ? '?' + qs : '');
                },

                checkScroll() {
                    this.showFloatingButtons = window.scrollY > 120;

                    if (this.loading || !this.nextPageUrl) return;

                    const threshold = 300;
                    const scrollPosition = window.innerHeight + window.scrollY;
                    const documentHeight = document.documentElement.scrollHeight;

                    if (documentHeight - scrollPosition < threshold) {
                        this.loadMore();
                    }
                },

                loadMore() {
                    if (!this.nextPageUrl || this.loading) return;
                    this.loading = true;

                    fetch(this.nextPageUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.items.push(...data.data);
                        this.nextPageUrl = data.next_page_url;
                        this.loading = false;
                    })
                    .catch(err => {
                        console.error('Load more error:', err);
                        this.loading = false;
                    });
                },

                submitSearch() {
                    let params = new URLSearchParams();
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.activeCategory) params.set('category', this.activeCategory);
                    const qs = params.toString();
                    window.location.href = '{{ route('products.index') }}' + (qs ? '?' + qs : '');
                },

                cancelSearch() {
                    this.openFloatingSearch = false;
                    this.searchQuery = this.activeSearch;
                },

                formatRupiah(n) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
                },

                formatNumber(n) {
                    return new Intl.NumberFormat('id-ID').format(n);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

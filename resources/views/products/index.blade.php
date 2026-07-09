<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Produk</h2>
            <a href="{{ route('products.create') }}" class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg active:scale-95 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </a>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-4" x-data="{
        search: '{{ request('search', '') }}',
        category: '{{ request('category', '') }}',
        get filterUrl() {
            let params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            if (this.category) params.set('category', this.category);
            return '{{ route('products.index') }}?' + params.toString();
        }
    }">
        {{-- Search Bar --}}
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" @input.debounce.400ms="window.location.href = filterUrl" placeholder="Cari produk..." autofocus
                class="w-full pl-10 pr-4 py-3 rounded-glass border border-white/40 shadow-glass text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
        </div>

        {{-- Category Filter --}}
        @if(isset($categories) && $categories->count() > 0)
        <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-hide">
            <button @click="category = ''; window.location.href = filterUrl"
                :class="category === '' ? 'bg-primary-600 text-white shadow-lg' : 'bg-white/60 text-gray-600 border border-white/40'"
                class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-medium transition-all">
                Semua
            </button>
            @foreach($categories as $cat)
            <button @click="category = '{{ $cat->id }}'; window.location.href = filterUrl"
                :class="category == '{{ $cat->id }}' ? 'bg-primary-600 text-white shadow-lg' : 'bg-white/60 text-gray-600 border border-white/40'"
                class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-medium transition-all whitespace-nowrap">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>
        @endif

        {{-- Product Grid --}}
        <div class="grid grid-cols-2 gap-3">
            @forelse($products as $product)
            <a href="{{ route('products.show', $product) }}" class="rounded-glass border border-white/40 shadow-glass overflow-hidden block active:scale-[0.98] transition-transform" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
                <div class="aspect-square bg-gray-100 relative overflow-hidden">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                    @endif
                    {{-- Stock Badge --}}
                    @if($product->stock <= ($product->min_stock ?? 0))
                        <span class="absolute top-2 right-2 text-[10px] px-1.5 py-0.5 rounded-full bg-red-500 text-white font-medium">Stok Rendah</span>
                    @endif
                    @if(!$product->is_active)
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <span class="text-xs text-white font-medium bg-black/60 px-2 py-1 rounded">Nonaktif</span>
                        </div>
                    @endif
                </div>
                <div class="p-3">
                    <p class="text-xs text-gray-400 mb-0.5">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                    <p class="text-sm font-semibold text-dark leading-tight line-clamp-2 mb-1">{{ $product->name }}</p>
                    <p class="text-sm font-bold text-primary-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">Stok: {{ $product->stock }} {{ $product->sellUnit->symbol ?? '' }}</p>
                </div>
            </a>
            @empty
            <div class="col-span-2 py-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-sm text-gray-400">Belum ada produk</p>
                <a href="{{ route('products.create') }}" class="inline-block mt-3 px-4 py-2 bg-primary-600 text-white text-sm rounded-full">+ Tambah Produk</a>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
        <div class="mt-4">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('products.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="text-lg font-bold text-dark">Detail Produk</h2>
            </div>
            <a href="{{ route('products.edit', $product) }}" class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg active:scale-95 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
        </div>
    </x-slot>

    <div class="pb-24">
        {{-- Product Image --}}
        <div class="aspect-video bg-gray-100 relative overflow-hidden">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            @endif
            @if(!$product->is_active)
                <div class="absolute top-3 right-3 px-2 py-1 bg-red-500 text-white text-xs rounded-full font-medium">Nonaktif</div>
            @endif
            @if($product->stock <= ($product->min_stock ?? 0))
                <div class="absolute top-3 left-3 px-2 py-1 bg-red-500 text-white text-xs rounded-full font-medium">Stok Rendah</div>
            @endif
        </div>

        <div class="px-4 py-5 space-y-4">
            {{-- Name & Price --}}
            <div class="rounded-glass border border-white/40 shadow-glass p-4" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
                <span class="text-xs px-2 py-0.5 rounded-full bg-primary-100 text-primary-700 font-medium">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
                <h1 class="text-xl font-bold text-dark mt-2">{{ $product->name }}</h1>
                @if($product->sku)
                    <p class="text-xs text-gray-400 mt-0.5">SKU: {{ $product->sku }}</p>
                @endif
                <div class="mt-3 flex items-end gap-2">
                    <span class="text-2xl font-bold text-primary-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                    <span class="text-sm text-gray-400 mb-0.5">/ {{ $product->sellUnit->symbol ?? 'unit' }}</span>
                </div>
            </div>

            {{-- Stock & Unit Info --}}
            <div class="rounded-glass border border-white/40 shadow-glass p-4" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
                <h3 class="text-sm font-semibold text-dark mb-3">Informasi Stok & Satuan</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400">Stok Saat Ini</p>
                        <p class="text-lg font-bold {{ $product->stock <= ($product->min_stock ?? 0) ? 'text-red-600' : 'text-dark' }}">{{ $product->stock }} <span class="text-xs font-normal">{{ $product->sellUnit->symbol ?? '' }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Stok Minimum</p>
                        <p class="text-lg font-bold text-dark">{{ $product->min_stock ?? 0 }} <span class="text-xs font-normal">{{ $product->sellUnit->symbol ?? '' }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Satuan Beli</p>
                        <p class="text-sm font-medium text-dark">{{ $product->buyUnit->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Satuan Jual</p>
                        <p class="text-sm font-medium text-dark">{{ $product->sellUnit->name ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-400">Konversi</p>
                        <p class="text-sm font-medium text-dark">1 {{ $product->buyUnit->symbol ?? 'sat. beli' }} = {{ $product->conversion_factor }} {{ $product->sellUnit->symbol ?? 'sat. jual' }}</p>
                    </div>
                </div>
            </div>

            {{-- Purchase History --}}
            <div class="rounded-glass border border-white/40 shadow-glass overflow-hidden" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
                <div class="px-4 py-3 border-b border-white/30">
                    <h3 class="text-sm font-semibold text-dark">Riwayat Pembelian</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($product->purchaseItems ?? [] as $item)
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-dark">{{ $item->purchase->supplier->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $item->purchase->date ? \Carbon\Carbon::parse($item->purchase->date)->locale('id')->isoFormat('D MMM Y') : '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-dark">{{ $item->quantity }} {{ $product->buyUnit->symbol ?? '' }}</p>
                                <p class="text-xs text-gray-400">@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-4 py-8 text-center">
                        <p class="text-sm text-gray-400">Belum ada riwayat pembelian</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Delete Button --}}
            <form action="{{ route('products.destroy', $product) }}" method="POST" class="confirm-delete" data-confirm="Yakin ingin menghapus produk ini?">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-3 border-2 border-red-200 text-red-600 font-medium rounded-glass text-sm hover:bg-red-50 transition-colors">
                    Hapus Produk
                </button>
            </form>
        </div>
    </div>
</x-app-layout>

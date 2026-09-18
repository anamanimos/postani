<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('products.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform hover:bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-dark">Detail Produk</h2>
                    <p class="text-xs text-gray-400 hidden sm:block">Kelola informasi stok, harga, dan pergerakan produk</p>
                </div>
            </div>
            
            {{-- Header Actions --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('products.edit', $product) }}" 
                   class="btn-primary py-2 px-3.5 sm:px-4 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Produk</span>
                </a>
                
                {{-- Quick Delete in header for desktop/tablet --}}
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="confirm-delete hidden sm:inline-block" data-confirm="Yakin ingin menghapus produk '{{ $product->name }}'? Seluruh data terkait akan terpengaruh.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs px-3 py-2 rounded-xl font-bold transition-colors active:scale-95 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="pb-24 pt-1" x-data="{ imgPreviewOpen: false }">
        {{-- Responsive Grid: 1 col on mobile, 12 cols on desktop --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-start">
            
            {{-- LEFT COLUMN: Product Image & Quick Summary & Actions --}}
            <div class="lg:col-span-5 space-y-4">
                {{-- Image Card --}}
                <div class="card-solid p-3 sm:p-4 relative">
                    <div class="w-full aspect-square max-h-[380px] sm:max-h-[420px] rounded-xl bg-gray-50/80 border border-gray-150 overflow-hidden relative flex items-center justify-center group {{ $product->image ? 'cursor-pointer' : '' }}"
                         @if($product->image) @click="imgPreviewOpen = true" @endif>
                        
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-300" 
                                 alt="{{ $product->name }}">
                            
                            {{-- Hover Zoom Badge --}}
                            <div class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5 pointer-events-none">
                                <span class="bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                    <span>Perbesar Foto</span>
                                </span>
                            </div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs text-gray-400 mt-2">Tidak ada foto produk</span>
                            </div>
                        @endif

                        {{-- Floating Badges on Image --}}
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10 pointer-events-none">
                            @if(!$product->is_active)
                                <span class="px-2.5 py-1 bg-red-600/90 backdrop-blur-sm text-white text-[11px] rounded-lg font-bold shadow-sm">
                                    Nonaktif
                                </span>
                            @endif

                            @if($product->stock <= 0)
                                <span class="px-2.5 py-1 bg-red-500/90 backdrop-blur-sm text-white text-[11px] rounded-lg font-bold shadow-sm">
                                    Stok Habis
                                </span>
                            @elseif($product->stock <= ($product->min_stock ?? 0))
                                <span class="px-2.5 py-1 bg-amber-500/90 backdrop-blur-sm text-white text-[11px] rounded-lg font-bold shadow-sm">
                                    Stok Rendah
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-emerald-600/90 backdrop-blur-sm text-white text-[11px] rounded-lg font-bold shadow-sm">
                                    Stok Aman
                                </span>
                            @endif
                        </div>

                        @if($product->image)
                            <div class="absolute bottom-2.5 right-2.5 z-10 pointer-events-none">
                                <span class="text-[10px] text-gray-500 bg-white/80 backdrop-blur-md px-2 py-0.5 rounded-md border border-gray-200/60 shadow-2xs font-medium">
                                    🔍 Klik untuk zoom
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Meta Card on Left for Desktop --}}
                <div class="card-solid p-4 hidden lg:block space-y-3">
                    <div class="flex items-center justify-between text-xs pb-2 border-b border-gray-150">
                        <span class="text-gray-500 font-semibold">Status Produk</span>
                        <span class="inline-flex items-center gap-1.5 font-bold {{ $product->is_active ? 'text-emerald-600' : 'text-red-500' }}">
                            <span class="w-2 h-2 rounded-full {{ $product->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></span>
                            {{ $product->is_active ? 'Aktif Dijual' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs pb-2 border-b border-gray-150">
                        <span class="text-gray-500 font-semibold">Kategori</span>
                        <span class="font-bold text-dark">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs pb-2 border-b border-gray-150">
                        <span class="text-gray-500 font-semibold">Satuan Jual</span>
                        <span class="font-bold text-dark">{{ $product->sellUnit->name ?? '-' }} ({{ $product->sellUnit->symbol ?? '' }})</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500 font-semibold">Satuan Beli</span>
                        <span class="font-bold text-dark">{{ $product->buyUnit->name ?? '-' }} ({{ $product->buyUnit->symbol ?? '' }})</span>
                    </div>
                </div>

                {{-- Action Buttons on Desktop under Image --}}
                <div class="hidden lg:grid grid-cols-2 gap-3 pt-1">
                    <a href="{{ route('products.edit', $product) }}" 
                       class="btn-primary py-2.5 px-4 text-xs font-bold rounded-xl flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit Produk</span>
                    </a>
                    
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="confirm-delete w-full" data-confirm="Yakin ingin menghapus produk '{{ $product->name }}'? Seluruh riwayat transaksi terkait akan terpengaruh.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 px-4 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-bold rounded-xl transition-colors active:scale-95 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Produk</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- RIGHT COLUMN: Product Info, Stock details, Movements, Purchases --}}
            <div class="lg:col-span-7 space-y-5">
                
                {{-- Card 1: Main Product Header & Pricing --}}
                <div class="card-solid p-5 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-primary-50 text-primary-700 border border-primary-200 font-bold">
                                    {{ $product->category->name ?? 'Tanpa Kategori' }}
                                </span>
                                @if($product->sku)
                                    <span class="text-xs px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 font-mono font-medium">
                                        SKU: {{ $product->sku }}
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-xl sm:text-2xl font-black text-dark tracking-tight">{{ $product->name }}</h1>
                        </div>
                    </div>

                    {{-- Pricing Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-gray-150">
                        <div class="bg-primary-50/50 border border-primary-100 rounded-xl p-3">
                            <p class="text-[11px] font-semibold text-primary-800">Harga Jual (Kasir)</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-xl sm:text-2xl font-black text-primary-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                <span class="text-xs text-gray-500 font-medium">/ {{ $product->sellUnit->symbol ?? 'unit' }}</span>
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-3">
                            <p class="text-[11px] font-semibold text-gray-500">Harga Beli Terakhir</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-base sm:text-lg font-bold text-dark">
                                    @if($product->last_purchase_price > 0)
                                        Rp {{ number_format($product->last_purchase_price, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </span>
                                @if($product->last_purchase_price > 0)
                                    <span class="text-[10px] text-gray-400">/ {{ $product->buyUnit->symbol ?? '' }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-3">
                            <p class="text-[11px] font-semibold text-gray-500">Rata-rata Harga Beli</p>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-base sm:text-lg font-bold text-dark">
                                    @if($product->avg_purchase_price > 0)
                                        Rp {{ number_format($product->avg_purchase_price, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </span>
                                @if($product->avg_purchase_price > 0)
                                    <span class="text-[10px] text-gray-400">/ {{ $product->buyUnit->symbol ?? '' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Stock & Unit Info --}}
                <div class="card-solid p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-150">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h3 class="text-sm font-bold text-dark">Informasi Stok & Konversi Satuan</h3>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/60">
                            <p class="text-[11px] text-gray-500 font-medium">Stok Saat Ini</p>
                            <p class="text-xl font-black mt-1 {{ $product->stock <= ($product->min_stock ?? 0) ? 'text-red-600' : 'text-dark' }}">
                                {{ $product->stock }} <span class="text-xs font-normal text-gray-500">{{ $product->sellUnit->symbol ?? '' }}</span>
                            </p>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/60">
                            <p class="text-[11px] text-gray-500 font-medium">Stok Minimum</p>
                            <p class="text-xl font-black mt-1 text-dark">
                                {{ $product->min_stock ?? 0 }} <span class="text-xs font-normal text-gray-500">{{ $product->sellUnit->symbol ?? '' }}</span>
                            </p>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/60">
                            <p class="text-[11px] text-gray-500 font-medium">Satuan Beli</p>
                            <p class="text-sm font-bold mt-1 text-dark truncate">{{ $product->buyUnit->name ?? '-' }}</p>
                            <p class="text-[10px] text-gray-400">Simbol: {{ $product->buyUnit->symbol ?? '-' }}</p>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/60">
                            <p class="text-[11px] text-gray-500 font-medium">Satuan Jual</p>
                            <p class="text-sm font-bold mt-1 text-dark truncate">{{ $product->sellUnit->name ?? '-' }}</p>
                            <p class="text-[10px] text-gray-400">Simbol: {{ $product->sellUnit->symbol ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50/80 rounded-xl p-3 border border-gray-200/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                        <span class="text-gray-500 font-semibold">Rumus Konversi Satuan:</span>
                        <span class="font-bold text-dark bg-white px-2.5 py-1 rounded-lg border border-gray-200 shadow-2xs self-start sm:self-auto">
                            1 {{ $product->buyUnit->name ?? 'Satuan Beli' }} ({{ $product->buyUnit->symbol ?? '' }}) = {{ $product->conversion_factor }} {{ $product->sellUnit->name ?? 'Satuan Jual' }} ({{ $product->sellUnit->symbol ?? '' }})
                        </span>
                    </div>
                </div>

                {{-- Card 3: Riwayat Pergerakan Stok --}}
                <div class="card-solid overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-150 flex items-center justify-between bg-gray-50/50">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <h3 class="text-sm font-bold text-dark">Riwayat Pergerakan Stok Terakhir</h3>
                        </div>
                        <a href="{{ route('reports.stock_movements', ['product_id' => $product->id]) }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 hover:underline flex items-center gap-1">
                            <span>Lihat Semua</span>
                            <span>➔</span>
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                        @forelse($product->stockMovements ?? [] as $movement)
                        <div class="px-5 py-3 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center justify-between text-xs gap-3">
                                <div class="space-y-0.5 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-bold text-dark truncate">{{ $movement->notes }}</span>
                                        @if(in_array($movement->type, ['purchase', 'sale_delete']))
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">
                                                +{{ number_format($movement->quantity, 2) }}
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                                                {{ number_format($movement->quantity, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-400 text-[10px]">
                                        {{ $movement->created_at->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                                        @if($movement->creator)
                                            · {{ $movement->creator->name }}
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-[10px] text-gray-400">Sisa Stok</p>
                                    <p class="font-bold text-dark">
                                        <span class="text-gray-400 font-normal">{{ number_format($movement->stock_before, 2) }}</span> ➔ <span class="text-primary-600">{{ number_format($movement->stock_after, 2) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center">
                            <p class="text-xs text-gray-400">Belum ada riwayat pergerakan stok untuk produk ini</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Card 4: Riwayat Pembelian --}}
                <div class="card-solid overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-150 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <h3 class="text-sm font-bold text-dark">Riwayat Pembelian dari Tengkulak</h3>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                        @forelse($product->purchaseItems ?? [] as $item)
                        <div class="px-5 py-3 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold text-dark">{{ $item->purchase->supplier->name ?? 'Tengkulak' }}</p>
                                    <p class="text-[10px] text-gray-400">
                                        {{ $item->purchase->purchase_date ? \Carbon\Carbon::parse($item->purchase->purchase_date)->locale('id')->isoFormat('D MMMM Y') : '-' }}
                                        @if($item->purchase->invoice_number)
                                            · {{ $item->purchase->invoice_number }}
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs font-bold text-dark">{{ $item->quantity }} {{ $product->buyUnit->symbol ?? '' }}</p>
                                    <p class="text-[11px] text-gray-500">@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center">
                            <p class="text-xs text-gray-400">Belum ada riwayat pembelian dari tengkulak untuk produk ini</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Mobile Delete Button (visible only on mobile screens < 1024px) --}}
                <div class="lg:hidden pt-2">
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="confirm-delete" data-confirm="Yakin ingin menghapus produk '{{ $product->name }}'? Seluruh riwayat transaksi terkait akan terpengaruh.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 border border-red-200 bg-red-50 text-red-600 font-bold rounded-xl text-xs hover:bg-red-100 active:scale-[0.98] transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Produk Ini</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>

        {{-- Alpine Lightbox Modal for Product Photo --}}
        @if($product->image)
        <div x-show="imgPreviewOpen" 
             x-transition.opacity 
             class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4"
             @click="imgPreviewOpen = false"
             @keydown.escape.window="imgPreviewOpen = false"
             style="display: none;">
            
            {{-- Close button --}}
            <button type="button" @click="imgPreviewOpen = false" 
                    class="absolute top-5 right-5 w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition-colors text-lg z-50">
                ✕
            </button>
            
            {{-- Full size Image --}}
            <div class="max-w-4xl max-h-[85vh] p-2 bg-white/5 rounded-2xl flex items-center justify-center" @click.stop>
                <img src="{{ asset('storage/' . $product->image) }}" 
                     alt="{{ $product->name }}" 
                     class="max-w-full max-h-[80vh] object-contain rounded-xl shadow-2xl">
            </div>
        </div>
        @endif
    </div>
</x-app-layout>

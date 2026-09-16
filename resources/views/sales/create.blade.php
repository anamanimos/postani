<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Kasir Jual Barang</h2>
    </x-slot>

    <div class="py-3 pb-28 max-w-lg mx-auto lg:max-w-none lg:pb-8" x-data="posSystem()">
        <div class="lg:flex lg:gap-6 lg:items-start">
            {{-- Left Column: Products & Controls --}}
            <div class="lg:flex-1 min-w-0">
                {{-- Tanggal Penjualan & Desktop Search Control Bar --}}
                <div class="card-solid p-3 mb-3 bg-white flex items-center justify-between gap-3 shadow-sm rounded-xl">
                    <div class="flex items-center gap-2 text-dark font-bold text-xs flex-shrink-0">
                        <!-- Duotone Icon: Calendar -->
                        <svg class="w-4 h-4 text-primary-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M3 6C3 4.89543 3.89543 4 5 4H19C20.1046 4 21 4.89543 21 6V20C21 21.1046 20.1046 22 19 22H5C3.89543 22 3 21.1046 3 20V6Z" fill="currentColor"/>
                            <path d="M3 10H21M8 2V6M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span>Tgl Penjualan:</span>
                    </div>
                    <input type="datetime-local" x-model="saleDate" class="form-input-solid !text-xs !py-1 !px-2 max-w-[190px] font-semibold text-gray-700">

                    {{-- Desktop Quick Search Bar --}}
                    <div class="hidden lg:flex items-center flex-1 max-w-xs relative ml-auto">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="searchQuery" x-ref="desktopSearchInput"
                               placeholder="Cari barang (Ctrl+F)..."
                               class="form-input-solid !text-xs !py-1.5 !pl-9 !pr-7 w-full font-medium text-gray-700 rounded-lg">
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 text-gray-400 hover:text-gray-600 text-xs font-bold leading-none">×</button>
                    </div>
                </div>

                {{-- Product Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        <div @click="addToCart(product)"
                              class="card-solid overflow-hidden block active:scale-95 transition-all duration-150 cursor-pointer select-none bg-white hover:shadow-md hover:border-primary-200">
                            <div class="aspect-square bg-gray-100 relative">
                                <template x-if="product.image">
                                    <img :src="'/storage/' + product.image" class="w-full h-full object-cover" :alt="product.name">
                                </template>
                                <template x-if="!product.image">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <!-- Duotone Icon: Package -->
                                        <svg class="w-10 h-10 text-gray-300" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                                            <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </template>
                                {{-- Category Overlay Badge --}}
                                <span class="absolute top-2 right-2 bg-black/60 backdrop-blur-sm text-white text-[9px] px-1.5 py-0.5 rounded font-semibold tracking-wide"
                                      x-text="product.category"></span>
                                {{-- Stock Badge --}}
                                <span :class="product.stock <= 0 ? 'bg-red-500 text-white' : 'bg-primary-100 text-primary-700'"
                                      class="absolute bottom-2 left-2 text-[10px] px-1.5 py-0.5 rounded-full font-semibold"
                                      x-text="'Stok: ' + formatNumber(product.stock / product.conversion_factor) + ' ' + product.sell_unit"></span>
                            </div>
                            <div class="p-3 space-y-1">
                                <h4 class="text-xs font-bold text-dark line-clamp-2 leading-tight h-8" x-text="product.name"></h4>
                                <p class="text-sm font-extrabold text-primary-600" x-text="formatRupiah(product.selling_price)"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Right Column: Persistent Desktop Cart Panel (Desktop Only >= lg) --}}
            <div class="hidden lg:block lg:w-[380px] xl:w-[420px] lg:sticky lg:top-20 flex-shrink-0">
                <div class="card-solid bg-white overflow-hidden flex flex-col shadow-sm rounded-2xl border border-gray-150">
                    {{-- Header --}}
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/60">
                        <div class="flex items-center gap-2">
                            <!-- Duotone Icon: Cart -->
                            <svg class="w-4 h-4 text-primary-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M5.4 5L7 13H17L21 5H5.4Z" fill="currentColor"/>
                                <path d="M3 3H5.4L7 13H17L21 5H5.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="9" cy="20" r="1.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                                <circle cx="17" cy="20" r="1.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                            </svg>
                            <h3 class="text-sm font-bold text-dark">Keranjang Belanja</h3>
                        </div>
                        <span class="text-[11px] font-bold text-primary-700 bg-primary-50 border border-primary-200/60 px-2 py-0.5 rounded-full" x-text="cart.length + ' jenis (' + cartCount() + ' pcs)'"></span>
                    </div>

                    {{-- Empty State Desktop --}}
                    <div x-show="cart.length === 0" class="p-8 text-center flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-gray-500">Keranjang Masih Kosong</p>
                        <p class="text-[11px] text-gray-400 mt-1">Klik item produk di samping untuk memasukkan ke keranjang kasir.</p>
                    </div>

                    {{-- Cart Items List Desktop --}}
                    <div x-show="cart.length > 0" class="max-h-[38vh] overflow-y-auto divide-y divide-gray-100">
                        <template x-for="(item, idx) in cart" :key="'desk-' + item.id">
                            <div class="px-4 py-2.5">
                                <div class="flex items-start justify-between gap-2 mb-1.5">
                                    <h4 class="text-xs font-bold text-dark leading-tight flex-1" x-text="item.name"></h4>
                                    <button @click="removeFromCart(idx)" 
                                            class="flex-shrink-0 w-6 h-6 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-100 transition-all"
                                            title="Hapus">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M6 7H18L17 21H7L6 7Z" fill="currentColor"/>
                                            <path d="M6 7H18L17 21H7L6 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M4 7H20M10 3H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="input-group-solid">
                                            <span class="input-prefix text-[11px]">Rp</span>
                                            <input type="number" step="any" x-model.number="item.selling_price" 
                                                   class="form-input-solid !text-xs !h-7 !py-0.5"
                                                   @input="validatePrice(idx)">
                                        </div>
                                    </div>
                                    <div class="qty-stepper flex-shrink-0">
                                        <button type="button" @click="decQty(idx)" class="!w-7 !h-7 text-xs">−</button>
                                        <input type="number" step="any" x-model.number="item.quantity" @input="validateQty(idx)" class="!w-9 !h-7 !text-xs">
                                        <button type="button" @click="incQty(idx)" class="!w-7 !h-7 text-xs">+</button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-[10px] text-gray-400" x-text="'per ' + item.sell_unit"></span>
                                    <span class="text-xs font-bold text-primary-600" x-text="formatRupiah((item.selling_price || 0) * item.quantity)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Checkout Form Desktop --}}
                    <div class="p-4 border-t border-gray-100 space-y-3 bg-gray-50/40">
                        {{-- Total --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500">Total Tagihan</span>
                            <span class="text-lg font-extrabold text-primary-600" x-text="formatRupiah(totalCart())"></span>
                        </div>

                        {{-- Payment Method --}}
                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-gray-500">Metode Pembayaran</label>
                            <div class="grid grid-cols-4 gap-1.5">
                                <template x-for="method in ['cash', 'qris', 'transfer', 'credit']">
                                    <button type="button" @click="paymentMethod = method"
                                            :class="paymentMethod === method ? 'bg-primary-600 text-white shadow-sm border-primary-600' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                                            class="py-1.5 rounded-lg text-[10px] font-bold transition-all uppercase"
                                            x-text="method === 'credit' ? 'Hutang' : method"></button>
                                </template>
                            </div>
                        </div>

                        {{-- Customer Selection --}}
                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-gray-500">
                                Pelanggan <span class="text-accent-600" x-show="paymentMethod === 'credit'">*Wajib</span>
                            </label>
                            <select x-model="customerId" class="form-input-solid !text-xs !py-1.5 w-full bg-white">
                                <option value="">-- Pelanggan Umum --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Submit Button --}}
                        <button type="button" @click="submitSale()" :disabled="submitting || cart.length === 0"
                                class="btn-primary w-full py-2.5 text-xs font-extrabold shadow-md transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" x-show="!submitting" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M4 4C4 2.89543 4.89543 2 6 2H18C19.1046 2 20 2.89543 20 4V22L17 20L14 22L12 20L10 22L7 20L4 22V4Z" fill="currentColor"/>
                                    <path d="M8 8H16M8 12H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <svg class="w-4 h-4 animate-spin" x-show="submitting" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-text="submitting ? 'Menyimpan...' : 'Selesai & Cetak Struk'"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cart Slide-Up Sheet (Mobile Only) --}}
        <div x-show="openCart" x-transition.opacity class="bottom-sheet-overlay lg:hidden" @click="openCart = false"></div>
        <div x-show="openCart" x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="bottom-sheet lg:hidden">
            <div class="bottom-sheet-handle"></div>
            
            {{-- Header --}}
            <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <!-- Duotone Icon: Cart -->
                    <svg class="w-4 h-4 text-primary-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.3" d="M5.4 5L7 13H17L21 5H5.4Z" fill="currentColor"/>
                        <path d="M3 3H5.4L7 13H17L21 5H5.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="20" r="1.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                        <circle cx="17" cy="20" r="1.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                    </svg>
                    <h3 class="text-sm font-bold text-dark">Keranjang</h3>
                    <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full" x-text="cart.length + ' item'"></span>
                </div>
                <button @click="openCart = false" class="text-xs font-semibold text-gray-400 hover:text-gray-600 transition-colors">Tutup</button>
            </div>

            {{-- Cart Item List --}}
            <div class="max-h-[40vh] overflow-y-auto">
                <template x-for="(item, idx) in cart" :key="item.id">
                    <div class="px-4 py-3 border-b border-gray-100/80">
                        {{-- Row 1: Product name + delete --}}
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h4 class="text-xs font-bold text-dark leading-tight flex-1" x-text="item.name"></h4>
                            <button @click="removeFromCart(idx)" 
                                    class="flex-shrink-0 w-7 h-7 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-100 transition-all active:scale-90"
                                    title="Hapus">
                                <!-- Duotone Icon: Trash -->
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M6 7H18L17 21H7L6 7Z" fill="currentColor"/>
                                    <path d="M6 7H18L17 21H7L6 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 7H20M10 3H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Row 2: Price input + Qty stepper (side by side) --}}
                        <div class="flex items-center gap-2">
                            {{-- Editable Price --}}
                            <div class="flex-1 min-w-0">
                                <div class="input-group-solid">
                                    <span class="input-prefix text-[11px]">Rp</span>
                                    <input type="number" step="any" x-model.number="item.selling_price" 
                                           class="form-input-solid !text-xs !h-8 !py-1"
                                           @input="validatePrice(idx)">
                                </div>
                            </div>

                            {{-- Qty Stepper --}}
                            <div class="qty-stepper flex-shrink-0">
                                <button type="button" @click="decQty(idx)" class="!w-8 !h-8 text-sm">−</button>
                                <input type="number" step="any" x-model.number="item.quantity" @input="validateQty(idx)" class="!w-10 !h-8 !text-xs">
                                <button type="button" @click="incQty(idx)" class="!w-8 !h-8 text-sm">+</button>
                            </div>
                        </div>

                        {{-- Row 3: Per-unit & Subtotal --}}
                        <div class="flex items-center justify-between mt-1.5">
                            <span class="text-[10px] text-gray-400" x-text="'per ' + item.sell_unit"></span>
                            <span class="text-xs font-bold text-primary-600" x-text="formatRupiah((item.selling_price || 0) * item.quantity)"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Checkout Form --}}
            <div class="p-4 space-y-3">
                {{-- Total --}}
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500">Total Belanja</span>
                    <span class="text-base font-extrabold text-primary-600" x-text="formatRupiah(totalCart())"></span>
                </div>

                {{-- Payment Method --}}
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-semibold text-gray-500">Metode Pembayaran</label>
                    <div class="grid grid-cols-4 gap-1.5">
                        <template x-for="method in ['cash', 'qris', 'transfer', 'credit']">
                            <button type="button" @click="paymentMethod = method"
                                    :class="paymentMethod === method ? 'bg-primary-600 text-white shadow-md border-primary-600' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                                    class="py-2 rounded-lg text-[10px] font-bold transition-all uppercase active:scale-95"
                                    x-text="method === 'credit' ? 'Hutang' : method"></button>
                        </template>
                    </div>
                </div>

                {{-- Customer Selection --}}
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-semibold text-gray-500">
                        Pelanggan <span class="text-accent-600" x-show="paymentMethod === 'credit'">*Wajib</span>
                    </label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: User -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                                <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M6 21C6 17.134 9.13401 14 13 14H11C7.13401 14 4 17.134 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <div class="flex-1 min-w-0">
                            <select x-init="
                                        $($el).select2({ width: '100%' }).on('change', (e) => {
                                            customerId = e.target.value;
                                        });
                                    "
                                    class="form-input-solid">
                                <option value="">-- Pelanggan Umum --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Tanggal Transaksi --}}
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-semibold text-gray-500">Tanggal Transaksi</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Calendar -->
                            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M3 6C3 4.89543 3.89543 4 5 4H19C20.1046 4 21 4.89543 21 6V20C21 21.1046 20.1046 22 19 22H5C3.89543 22 3 21.1046 3 20V6Z" fill="currentColor"/>
                                <path d="M3 10H21M8 2V6M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input type="datetime-local" x-model="saleDate" class="form-input-solid !text-xs !py-2">
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="button" @click="submitSale()" :disabled="submitting || cart.length === 0"
                        class="btn-primary w-full py-3 text-sm font-extrabold shadow-float transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="flex items-center justify-center gap-2">
                        <!-- Duotone Icon: Receipt / Check -->
                        <svg class="w-4 h-4" x-show="!submitting" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M4 4C4 2.89543 4.89543 2 6 2H18C19.1046 2 20 2.89543 20 4V22L17 20L14 22L12 20L10 22L7 20L4 22V4Z" fill="currentColor"/>
                            <path d="M8 8H16M8 12H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <svg class="w-4 h-4 animate-spin" x-show="submitting" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Selesai & Cetak Struk'"></span>
                    </span>
                </button>
            </div>
        </div>

        {{-- Floating Action Buttons (Cart & Search - Mobile Only) --}}
        <div class="fixed bottom-24 left-0 right-0 z-40 px-5 pointer-events-none lg:hidden">
            <div class="max-w-lg mx-auto flex flex-col items-end gap-3">
                
                {{-- Floating Cart Button (stacked directly above Search) --}}
                <button type="button" @click="openCart = true" x-show="cart.length > 0" x-transition
                        class="w-12 h-12 rounded-full bg-white/95 backdrop-blur-md border border-gray-200/80 text-primary-600 flex items-center justify-center shadow-lg active:scale-90 hover:bg-white transition-all transform hover:-translate-y-0.5 duration-150 pointer-events-auto"
                        title="Lihat Keranjang">
                    <div class="relative">
                        <!-- Duotone Icon: Shopping Cart -->
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M5.4 5L7 13H17L21 5H5.4Z" fill="currentColor"/>
                            <path d="M3 3H5.4L7 13H17L21 5H5.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="9" cy="20" r="1.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                            <circle cx="17" cy="20" r="1.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                        </svg>
                        <span class="absolute -top-2 -right-2 bg-accent-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow-sm"
                              x-text="cartCount()"></span>
                    </div>
                </button>

                {{-- Floating Search Button --}}
                <div class="relative flex justify-end h-12 w-full">
                    <div class="absolute right-0 top-0 bg-white/95 backdrop-blur-md border border-gray-200/80 shadow-lg rounded-full overflow-hidden transition-all duration-300 ease-out pointer-events-auto"
                         :class="openFloatingSearch ? 'w-full h-12' : 'w-12 h-12'">
                         
                         {{-- Collapsed Button --}}
                         <button type="button" x-show="!openFloatingSearch"
                                 @click="openFloatingSearch = true; $nextTick(() => $refs.floatSearchInput.focus())"
                                 class="w-full h-full flex items-center justify-center text-primary-600 active:scale-95 transition-transform duration-150"
                                 title="Cari Barang">
                             <!-- Duotone Icon: Search -->
                             <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <circle opacity="0.3" cx="11" cy="11" r="7" fill="currentColor"/>
                                 <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                                 <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                             </svg>
                         </button>
                         
                         {{-- Expanded Form --}}
                         <div x-show="openFloatingSearch" class="w-full h-full flex items-center px-4 gap-2.5" style="display: none;">
                             <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                             </svg>
                             <input type="text" x-model="searchQuery" x-ref="floatSearchInput"
                                    placeholder="Cari nama barang..."
                                    @keydown.escape="openFloatingSearch = false"
                                    class="flex-1 bg-transparent border-0 outline-none text-xs font-semibold text-gray-700 placeholder-gray-400 focus:ring-0 p-0">
                             <button type="button" 
                                     @click="openFloatingSearch = false; searchQuery = ''"
                                     class="text-xs text-gray-400 hover:text-gray-600 font-bold flex-shrink-0 px-1 py-1">
                                 Batal
                             </button>
                         </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function posSystem() {
            return {
                products: {!! json_encode($products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'selling_price' => (float)$p->selling_price,
                    'stock' => (float)$p->stock,
                    'conversion_factor' => (float)$p->conversion_factor,
                    'sell_unit' => $p->sellUnit->symbol ?? 'unit',
                    'category' => $p->category->name ?? 'Umum',
                    'image' => $p->image
                ])) !!},
                searchQuery: '',
                openFloatingSearch: false,
                cart: [],
                openCart: false,
                paymentMethod: 'cash',
                customerId: '',
                saleDate: (function() {
                    const d = new Date();
                    const pad = (n) => String(n).padStart(2, '0');
                    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                })(),
                submitting: false,

                init() {
                    window.addEventListener('keydown', (e) => {
                        if ((e.ctrlKey || e.metaKey) && (e.key === 'f' || e.key === 'F')) {
                            e.preventDefault();
                            if (window.innerWidth >= 1024 && this.$refs.desktopSearchInput) {
                                this.$refs.desktopSearchInput.focus();
                                this.$refs.desktopSearchInput.select();
                            } else {
                                this.openFloatingSearch = true;
                                this.$nextTick(() => {
                                    if (this.$refs.floatSearchInput) {
                                        this.$refs.floatSearchInput.focus();
                                        this.$refs.floatSearchInput.select();
                                    }
                                });
                            }
                        }
                    });
                },

                filteredProducts() {
                    if (!this.searchQuery) return this.products;
                    const query = this.searchQuery.toLowerCase();
                    return this.products.filter(p => p.name.toLowerCase().includes(query));
                },

                addToCart(product) {
                    // Check if already in cart
                    const idx = this.cart.findIndex(item => item.id === product.id);
                    if (idx > -1) {
                        this.incQty(idx);
                    } else {
                        // Check stock first
                        const buyUnitQty = 1 * product.conversion_factor;
                        if (product.stock < buyUnitQty) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Stok Tidak Mencukupi',
                                text: `Stok tidak mencukupi untuk ${product.name}! Tersedia: ${product.stock / product.conversion_factor}`,
                                customClass: { popup: 'rounded-2xl font-sans shadow-lg' }
                            });
                            return;
                        }
                        this.cart.push({
                            ...product,
                            quantity: 1
                        });
                    }
                },

                incQty(idx) {
                    const item = this.cart[idx];
                    const nextQty = item.quantity + 1;
                    const buyUnitQty = nextQty * item.conversion_factor;
                    if (item.stock < buyUnitQty) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stok Tidak Mencukupi',
                            text: `Stok tidak mencukupi! Tersedia: ${item.stock / item.conversion_factor}`,
                            customClass: { popup: 'rounded-2xl font-sans shadow-lg' }
                        });
                        return;
                    }
                    item.quantity = nextQty;
                },

                decQty(idx) {
                    const item = this.cart[idx];
                    if (item.quantity > 1) {
                        item.quantity -= 1;
                    } else {
                        this.removeFromCart(idx);
                    }
                },

                validateQty(idx) {
                    const item = this.cart[idx];
                    if (item.quantity <= 0) {
                        this.removeFromCart(idx);
                        return;
                    }
                    const buyUnitQty = item.quantity * item.conversion_factor;
                    if (item.stock < buyUnitQty) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stok Tidak Mencukupi',
                            text: `Stok tidak mencukupi! Tersedia: ${item.stock / item.conversion_factor}`,
                            customClass: { popup: 'rounded-2xl font-sans shadow-lg' }
                        });
                        item.quantity = Math.floor(item.stock / item.conversion_factor);
                        if (item.quantity <= 0) {
                            this.removeFromCart(idx);
                        }
                    }
                },

                removeFromCart(idx) {
                    this.cart.splice(idx, 1);
                    if (this.cart.length === 0) {
                        this.openCart = false;
                    }
                },

                validatePrice(idx) {
                    const item = this.cart[idx];
                    if (item.selling_price === '' || isNaN(item.selling_price)) {
                        return;
                    }
                    if (item.selling_price < 0) {
                        item.selling_price = 0;
                    }
                },

                cartCount() {
                    return this.cart.reduce((sum, item) => sum + item.quantity, 0);
                },

                totalCart() {
                    return this.cart.reduce((sum, item) => sum + ((item.selling_price || 0) * item.quantity), 0);
                },

                submitSale() {
                    if (this.cart.length === 0) return;

                    // Validate that all items have valid prices
                    for (let i = 0; i < this.cart.length; i++) {
                        const item = this.cart[i];
                        if (item.selling_price === '' || isNaN(item.selling_price) || item.selling_price < 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Harga Tidak Valid',
                                text: `Harga jual untuk ${item.name} tidak valid!`,
                                customClass: { popup: 'rounded-2xl font-sans shadow-lg' }
                            });
                            return;
                        }
                    }

                    if (this.paymentMethod === 'credit' && !this.customerId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pelanggan Wajib Dipilih',
                            text: 'Silakan pilih pelanggan untuk transaksi Hutang/Kredit!',
                            customClass: { popup: 'rounded-2xl font-sans shadow-lg' }
                        });
                        return;
                    }

                    this.submitting = true;

                    const payload = {
                        customer_id: this.customerId || null,
                        sale_date: this.saleDate || null,
                        payment_method: this.paymentMethod,
                        items: this.cart.map(item => ({
                            product_id: item.id,
                            quantity: item.quantity,
                            unit_price: item.selling_price
                        }))
                    };

                    // Open new tab synchronously during user gesture (prevents popup blockers)
                    let receiptWindow = null;
                    try {
                        receiptWindow = window.open('', '_blank');
                        if (receiptWindow) {
                            receiptWindow.document.write('<!DOCTYPE html><html><head><title>Struk Penjualan</title><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body style="font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f9fafb;"><div style="text-align:center;"><p style="font-size:14px;color:#4b5563;font-weight:600;">Memuat Struk Penjualan...</p></div></body></html>');
                        }
                    } catch (e) {
                        receiptWindow = null;
                    }

                    fetch('{{ route('sales.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.submitting = false;
                        if (data.redirect) {
                            // Navigate new tab to PDF receipt
                            if (receiptWindow && !receiptWindow.closed) {
                                receiptWindow.location.href = data.redirect;
                            } else {
                                const a = document.createElement('a');
                                a.href = data.redirect;
                                a.target = '_blank';
                                a.rel = 'noopener noreferrer';
                                document.body.appendChild(a);
                                a.click();
                                a.remove();
                            }

                            // Wait 800ms before reloading cashier page so new tab navigation completes
                            setTimeout(() => {
                                window.location.reload();
                            }, 800);
                        } else {
                            if (receiptWindow && !receiptWindow.closed) receiptWindow.close();
                            if (data.message) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Transaksi',
                                    text: data.message,
                                    customClass: { popup: 'rounded-2xl font-sans shadow-lg' }
                                });
                            }
                        }
                    })
                    .catch(err => {
                        if (receiptWindow && !receiptWindow.closed) receiptWindow.close();
                        this.submitting = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Terjadi kesalahan saat menyimpan transaksi.',
                            customClass: { popup: 'rounded-2xl font-sans shadow-lg' }
                        });
                    });
                }
            };
        }
    </script>
    @endpush

</x-app-layout>

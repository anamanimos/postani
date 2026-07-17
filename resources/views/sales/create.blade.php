<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Kasir Jual Barang</h2>
    </x-slot>

    <div class="py-3 pb-28 max-w-lg mx-auto" x-data="posSystem()">
        {{-- Product Grid --}}
        <div class="grid grid-cols-2 gap-3">
            <template x-for="product in filteredProducts()" :key="product.id">
                <div @click="addToCart(product)"
                      class="card-solid overflow-hidden block active:scale-95 transition-all duration-150 cursor-pointer select-none bg-white">
                    <div class="aspect-square bg-gray-100 relative">
                        <template x-if="product.image">
                            <img :src="'/storage/' + product.image" class="w-full h-full object-cover" :alt="product.name">
                        </template>
                        <template x-if="!product.image">
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
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

        {{-- Floating Cart Button --}}
        <button @click="openCart = true" x-show="cart.length > 0" x-transition
                class="w-12 h-12 rounded-full bg-white/80 backdrop-blur-md border border-gray-200/80 text-primary-600 flex items-center justify-center shadow-lg active:scale-90 hover:bg-white transition-all transform hover:-translate-y-0.5 duration-150 fixed bottom-40 right-5 z-40">
            <div class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="absolute -top-2 -right-2 bg-accent-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow-sm"
                      x-text="cartCount()"></span>
            </div>
        </button>

        {{-- Cart Slide-Up Sheet --}}
        <div x-show="openCart" x-transition.opacity class="bottom-sheet-overlay" @click="openCart = false"></div>
        <div x-show="openCart" x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="bottom-sheet">
            <div class="bottom-sheet-handle"></div>
            
            <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-dark">Keranjang Belanja</h3>
                <button @click="openCart = false" class="text-xs font-semibold text-gray-400">Tutup</button>
            </div>

            {{-- Cart Item List --}}
            <div class="divide-y divide-gray-100 max-h-[40vh] overflow-y-auto px-4">
                <template x-for="(item, idx) in cart" :key="item.id">
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-dark leading-tight" x-text="item.name"></h4>
                            <div class="flex items-center gap-1.5 mt-1 text-xs">
                                <span class="text-gray-400 font-medium">Harga: Rp</span>
                                <input type="number" step="any" x-model.number="item.selling_price" 
                                       class="w-24 px-2 py-0.5 rounded-lg border border-gray-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 text-xs font-bold text-dark bg-white/80 transition-colors"
                                       @input="validatePrice(idx)">
                                <span class="text-gray-400">/ <span x-text="item.sell_unit"></span></span>
                            </div>
                            <p class="text-xs font-semibold text-primary-600 mt-1" x-text="formatRupiah((item.selling_price || 0) * item.quantity)"></p>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            {{-- Stepper --}}
                            <div class="qty-stepper">
                                <button type="button" @click="decQty(idx)">-</button>
                                <input type="number" step="any" x-model.number="item.quantity" @input="validateQty(idx)">
                                <button type="button" @click="incQty(idx)">+</button>
                            </div>
                            
                            {{-- Delete --}}
                            <button @click="removeFromCart(idx)" class="text-sm">🗑️</button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Checkout Form --}}
            <div class="p-4 bg-gray-50 border-t border-gray-100 space-y-4">
                <div class="flex items-center justify-between text-base font-bold text-dark">
                    <span>Total Belanja</span>
                    <span class="text-lg text-primary-600" x-text="formatRupiah(totalCart())"></span>
                </div>

                {{-- Payment Method Selection --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-500">Metode Pembayaran</label>
                    <div class="grid grid-cols-4 gap-1.5">
                        <template x-for="method in ['cash', 'qris', 'transfer', 'credit']">
                            <button type="button" @click="paymentMethod = method"
                                    :class="paymentMethod === method ? 'bg-primary-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200'"
                                    class="py-2.5 rounded-xl text-xs font-bold transition-all uppercase"
                                    x-text="method === 'credit' ? 'Hutang' : method"></button>
                        </template>
                    </div>
                </div>

                {{-- Customer selection --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-500">
                        Pilih Pelanggan (Petani) <span class="text-accent-600 text-[10px]" x-show="paymentMethod === 'credit'">*Wajib</span>
                    </label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                                <path d="M6 21C6 17.134 9.13401 14 13 14H11C7.13401 14 4 17.134 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                        <div class="flex-1 min-w-0">
                            <select x-init="
                                        $($el).select2({ width: '100%' }).on('change', (e) => {
                                            customerId = e.target.value;
                                        });
                                    "
                                    class="form-input-solid">
                                <option value="">-- Pelanggan Umum (Walk-in) --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Submit button --}}
                <button type="button" @click="submitSale()" :disabled="submitting"
                        class="btn-primary w-full py-3.5 text-base font-extrabold shadow-float"
                        x-text="submitting ? 'Menyimpan...' : 'Selesai & Cetak Struk'"></button>
            </div>
        </div>

        {{-- Floating Search Button (placed at bottom-24, below bottom-40 cart button) --}}
        <div class="fixed bottom-24 left-0 right-0 z-40 px-5 pointer-events-none">
            <div class="max-w-lg mx-auto relative flex justify-end h-12">
                <div class="absolute right-0 top-0 bg-white/95 backdrop-blur-md border border-gray-200/80 shadow-lg rounded-full overflow-hidden transition-all duration-300 ease-out pointer-events-auto"
                     :class="openFloatingSearch ? 'w-full h-12' : 'w-12 h-12'">
                     
                     {{-- Collapsed Button --}}
                     <button type="button" x-show="!openFloatingSearch"
                             @click="openFloatingSearch = true; $nextTick(() => $refs.floatSearchInput.focus())"
                             class="w-full h-full flex items-center justify-center text-primary-600 active:scale-95 transition-transform duration-150"
                             title="Cari Barang">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
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
                submitting: false,

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
                            alert(`Stok tidak mencukupi untuk ${product.name}! Stok: ${product.stock / product.conversion_factor}`);
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
                        alert(`Stok tidak mencukupi! Stok: ${item.stock / item.conversion_factor}`);
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
                        alert(`Stok tidak mencukupi! Stok: ${item.stock / item.conversion_factor}`);
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
                            alert(`Harga jual untuk ${item.name} tidak valid!`);
                            return;
                        }
                    }

                    if (this.paymentMethod === 'credit' && !this.customerId) {
                        alert('Silakan pilih pelanggan untuk transaksi Hutang/Kredit!');
                        return;
                    }

                    this.submitting = true;

                    const payload = {
                        customer_id: this.customerId || null,
                        payment_method: this.paymentMethod,
                        items: this.cart.map(item => ({
                            product_id: item.id,
                            quantity: item.quantity,
                            unit_price: item.selling_price
                        }))
                    };

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
                            // Redirect to PDF Receipt
                            window.location.href = data.redirect;
                        } else if (data.message) {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        this.submitting = false;
                        alert('Terjadi kesalahan saat menyimpan transaksi.');
                    });
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

@push('styles')
<style>
    /* Card Solid Styling */
    .card-solid {
        background-color: #FFFFFF !important;
        border: 1px solid #D1D5DB !important; /* gray-300 solid border */
        border-radius: 12px !important; /* not too rounded */
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
    }
    
    /* Input Group Container */
    .input-group-solid {
        display: flex !important;
        align-items: stretch !important;
        width: 100% !important;
        background-color: #F3F4F6 !important; /* default gray-100 background */
        border: 1px solid #D1D5DB !important; /* gray-300 solid border */
        border-radius: 8px !important; /* rounded-lg */
        overflow: hidden !important;
        transition: all 0.2s ease-in-out !important;
    }
    
    /* Solid Prefix */
    .input-group-solid .input-prefix {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 42px !important;
        background-color: transparent !important; /* inherits container background */
        border-right: none !important; /* no vertical separator divider */
        color: #4B5563 !important; /* gray-600 default */
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        flex-shrink: 0 !important;
        transition: all 0.2s ease-in-out !important;
    }
    
    /* Input Solid Styling */
    .form-input-solid {
        flex: 1 !important;
        min-width: 0 !important;
        width: 100% !important;
        background-color: transparent !important; /* inherits container background */
        border: none !important; /* no border of its own */
        color: #1F2937 !important;
        font-size: 0.875rem !important;
        padding: 0.5rem 0.75rem !important;
        height: 36px !important; /* inner input height */
        outline: none !important;
        box-shadow: none !important;
        transition: all 0.2s ease-in-out !important;
    }
    
    textarea.form-input-solid {
        height: auto !important;
        min-height: 60px !important;
    }
    
    /* Group Focus State */
    .input-group-solid:focus-within {
        border-color: #16A34A !important; /* container border turns green */
        background-color: #FFFFFF !important; /* container background turns white */
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15) !important; /* container gets shadow */
    }
    
    .input-group-solid:focus-within .input-prefix {
        color: #16A34A !important; /* icon turns green */
    }
    
    /* Select2 Solid Override */
    .input-group-solid .select2-container--default .select2-selection--single {
        background-color: transparent !important; /* inherits container background */
        border: none !important; /* no border of its own */
        border-radius: 0 8px 8px 0 !important;
        height: 36px !important;
        display: flex !important;
        align-items: center !important;
        backdrop-filter: none !important;
        transition: all 0.2s ease-in-out !important;
        width: 100% !important;
    }
    .input-group-solid .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        padding-left: 8px !important;
        color: #1F2937 !important;
    }
    .input-group-solid .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
    }
    
    .select2-dropdown {
        background-color: #FFFFFF !important;
        border: 1px solid #D1D5DB !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        backdrop-filter: none !important;
    }
    
    /* Readonly state */
    .input-group-solid.bg-gray-50 {
        background-color: #F9FAFB !important;
    }
    
</style>
@endpush
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('purchases.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Input Pembelian Tengkulak</h2>
        </div>
    </x-slot>

    <div class="py-5 pb-24" x-data="purchaseForm()">
        <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data" 
              class="space-y-4">
            @csrf

            {{-- Detail Barang --}}
            <div class="card-solid p-4 space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-150 pb-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <h3 class="text-sm font-bold text-dark">Item Pembelian</h3>
                </div>

                {{-- Item List --}}
                <div class="pt-2">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="space-y-3" :class="index > 0 ? 'border-t border-gray-150 mt-4 pt-4' : ''">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-500" x-text="'Item #' + (index + 1)"></span>
                                <button type="button" @click="removeItem(index)" class="text-xs text-red-500 hover:text-red-700 font-semibold bg-red-50 px-2 py-1 rounded-lg border border-red-150 transition-colors">
                                    Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-3">
                                {{-- Product select --}}
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Produk</label>
                                    <div class="input-group-solid">
                                        <span class="input-prefix">
                                            <!-- Duotone Icon: Package -->
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                                                <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <select :name="'items['+index+'][product_id]'" 
                                                    x-init="
                                                        $nextTick(() => {
                                                            $($el).select2({ width: '100%' }).on('change', (e) => {
                                                                item.productId = e.target.value;
                                                                const selectedOpt = e.target.options[e.target.selectedIndex];
                                                                item.buyUnit = selectedOpt ? (selectedOpt.getAttribute('data-buy-unit') || '') : '';
                                                                productChanged(index);
                                                            });
                                                        });
                                                    "
                                                    required
                                                    class="form-input-solid">
                                                <option value="">-- Pilih Produk --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-buy-unit="{{ $product->buyUnit->symbol }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    {{-- Quantity --}}
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-1" x-text="'Jumlah (' + (item.buyUnit || 'satuan') + ')'"></label>
                                        <div class="input-group-solid">
                                            <span class="input-prefix">
                                                <!-- Duotone Icon: Hashtag -->
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M4 9H20M4 15H20" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                                    <path d="M9 3L7 21M17 3L15 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </span>
                                            <input type="number" step="any" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" @input="calculateSubtotal(index)" required
                                                   class="form-input-solid">
                                        </div>
                                    </div>
                                    {{-- Unit Price --}}
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">Harga Beli Satuan</label>
                                        <div class="input-group-solid">
                                            <span class="input-prefix">Rp</span>
                                            <input type="number" :name="'items['+index+'][unit_price]'" x-model.number="item.unitPrice" @input="calculateSubtotal(index)" required
                                                   class="form-input-solid">
                                        </div>
                                    </div>
                                </div>

                                {{-- Price history chips --}}
                                <div x-show="item.priceHistory.length > 0" class="flex flex-wrap gap-1 items-center mt-1">
                                    <span class="text-[9px] text-gray-400 mr-1">Histori Harga:</span>
                                    <template x-for="hist in item.priceHistory">
                                        <button type="button" @click="item.unitPrice = hist.unit_price; calculateSubtotal(index)"
                                                class="text-[9px] px-1.5 py-0.5 rounded bg-primary-50 text-primary-700 hover:bg-primary-100 font-medium border border-primary-200">
                                            Rp <span x-text="formatNumber(hist.unit_price)"></span> (<span x-text="hist.purchase_date"></span>)
                                        </button>
                                    </template>
                                </div>

                                {{-- Subtotal --}}
                                <div class="flex items-center justify-between pt-1 text-xs text-gray-500">
                                    <span>Subtotal:</span>
                                    <span class="font-bold text-dark" x-text="formatRupiah(item.subtotal)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Tombol Tambah Item di Bawah List --}}
                <div class="pt-2 border-t border-gray-150">
                    <button type="button" @click="addItem()" class="w-full inline-flex items-center justify-center gap-1.5 text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-5 py-2.5 rounded-lg border border-primary-200 border-dashed transition-all active:scale-[0.98]">
                        <!-- Duotone Icon: Plus Circle -->
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2Z" fill="currentColor"/>
                            <path d="M12 8V16M8 12H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Tambah Item Belanja
                    </button>
                </div>
            </div>

            {{-- Informasi Pembelian --}}
            <div class="card-solid p-4 space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-150 pb-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <h3 class="text-sm font-bold text-dark">Informasi Pembelian</h3>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tengkulak / Supplier</label>
                    <div class="flex gap-2 items-center">
                        <div class="input-group-solid flex-1">
                            <span class="input-prefix">
                                <!-- Duotone Icon: User -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"/>
                                    <path d="M6 21C6 17.134 9.13401 14 13 14H11C7.13401 14 4 17.134 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <select name="supplier_id" id="supplier-select"
                                        x-init="
                                            $($el).select2({ width: '100%' }).on('change', (e) => {
                                                supplierId = e.target.value;
                                                fetchPriceHistoryForAll();
                                            });
                                        "
                                        required
                                        class="form-input-solid">
                                    <option value="">-- Pilih Tengkulak --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="button" @click="quickSupplierOpen = true" 
                                class="w-10 h-10 shrink-0 bg-primary-50 hover:bg-primary-100 border border-primary-200 text-primary-600 rounded-lg flex items-center justify-center transition-colors active:scale-95 shadow-sm text-lg font-bold"
                                title="Tambah Tengkulak Baru">
                            +
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Pembelian</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Calendar -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.3" x="3" y="6" width="18" height="15" rx="2" fill="currentColor"/>
                                <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <rect x="3" y="6" width="18" height="15" rx="2" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                        <input type="text" name="purchase_date" required autocomplete="off"
                               x-init="
                                   flatpickr($el, {
                                       locale: 'id',
                                       dateFormat: 'Y-m-d',
                                       altInput: true,
                                       altFormat: 'd F Y',
                                       defaultDate: purchaseDate,
                                       disableMobile: true,
                                       onChange: (selectedDates, dateStr) => {
                                           purchaseDate = dateStr;
                                       }
                                   });
                               "
                               class="form-input-solid">
                    </div>
                </div>

                <div class="space-y-3 pt-2 border-t border-gray-150">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Nomor Nota Tengkulak (Opsional)</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">
                                <!-- Duotone Icon: Document/Receipt -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3 10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H13C16.7712 2 18.6569 2 19.8284 3.17157C21 4.34315 21 6.22876 21 10V14C21 17.7712 21 19.6569 19.8284 20.8284C18.6569 22 16.7712 22 13 22H11C7.22876 22 5.34315 22 4.17157 20.8284C3 19.6569 3 17.7712 3 14V10Z" fill="currentColor"/>
                                    <path d="M8 7H16M8 12H16M8 17H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input type="text" name="supplier_invoice_number" placeholder="Masukkan nomor nota dari tengkulak..."
                                   class="form-input-solid">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Foto Nota Fisik (Opsional)</label>
                        <input type="hidden" name="gallery_filepath" x-model="galleryFilepath">
                        
                        <div @dragover.prevent="dragging = true"
                             @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; if ($event.dataTransfer.files.length) { $refs.fileInput.files = $event.dataTransfer.files; handleFile($event.dataTransfer.files[0]); }"
                             @paste.window="
                                 const items = ($event.clipboardData || window.clipboardData).items;
                                 if (items) {
                                     for (let i = 0; i < items.length; i++) {
                                         if (items[i].type.indexOf('image') !== -1) {
                                             const file = items[i].getAsFile();
                                             if (file) {
                                                 $event.preventDefault();
                                                 handleFile(file);
                                                 break;
                                             }
                                         }
                                     }
                                 }
                             "
                             class="relative border border-dashed rounded-lg p-4 flex flex-col items-center justify-center transition-all duration-200 border-gray-300 hover:border-primary-400 bg-gray-50/50"
                             :class="dragging ? 'border-primary-500 bg-primary-50/20' : ''">
                             
                             <input type="file" name="invoice_image" x-ref="fileInput" accept="image/*" class="hidden"
                                    @change="handleFile($event.target.files[0])">
                                    
                             <template x-if="!imagePreview">
                                 <div class="text-center cursor-pointer py-2 w-full" @click="$refs.fileInput.click()">
                                     <svg class="mx-auto h-8 w-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                     </svg>
                                     <span class="block text-xs font-semibold text-gray-600">Tarik & Lepas Foto di sini</span>
                                     <span class="block text-[10px] text-gray-400 mt-0.5">atau klik untuk mencari foto</span>
                                 </div>
                             </template>
                             
                             <template x-if="imagePreview">
                                 <div class="relative w-full flex flex-col items-center justify-center py-1">
                                     <img :src="imagePreview" @click.stop="previewModalOpen = true" 
                                          class="w-full max-h-[300px] sm:max-h-[400px] rounded-lg object-contain border border-gray-200 shadow-sm bg-white cursor-zoom-in hover:brightness-95 active:scale-95 transition-all duration-200"
                                          title="Klik untuk memperbesar">
                                 </div>
                             </template>
                        </div>
                        
                        <div class="flex gap-2 mt-2">
                            <button type="button" @click="openGalleryModal()" 
                                    class="flex-1 py-2 text-xs font-semibold text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors border border-primary-200">
                                🖼️ Pilih dari Galeri
                            </button>
                            <button type="button" x-show="imagePreview || galleryFilepath" @click="clearImage()" 
                                    class="px-3 py-2 text-xs font-semibold text-red-500 bg-red-50/50 rounded-lg hover:bg-red-100/50 transition-colors border border-red-200">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary & Payment --}}
            <div class="card-solid p-4 space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-150 pb-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <h3 class="text-sm font-bold text-dark">Ringkasan & Pembayaran</h3>
                </div>

                <div class="flex items-center justify-between text-sm font-semibold border-b border-gray-150 pb-2">
                    <span>Total Pembelian</span>
                    <span class="text-base font-bold text-primary-600" x-text="formatRupiah(totalAmount)"></span>
                </div>

                <div class="grid grid-cols-2 gap-2 border-b border-gray-150 pb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Biaya Tambahan</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">Rp</span>
                            <input type="number" name="additional_cost" x-model.number="additionalCost" @input="calculateTotal"
                                   placeholder="Contoh: 5000" class="form-input-solid">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan Biaya</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">
                                <!-- Duotone Icon: Pencil/Edit -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" fill="currentColor"/>
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input type="text" name="additional_cost_notes" x-model="additionalCostNotes"
                                   placeholder="Contoh: Parkir & Beli Bensin" class="form-input-solid">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Status Pembayaran</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">
                                <!-- Duotone Icon: Credit Card -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.3" x="3" y="5" width="18" height="14" rx="2" fill="currentColor"/>
                                    <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </span>
                            <select name="payment_status" x-model="paymentStatus" required class="form-input-solid">
                                <option value="unpaid">Hutang (Kredit)</option>
                                <option value="partial">Bayar Sebagian</option>
                                <option value="paid">Lunas (Paid)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Metode Pembayaran</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">
                                <!-- Duotone Icon: Wallet -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M4 5C4 3.89543 4.89543 3 6 3H16C17.1046 3 18 3.89543 18 5V7H20C21.1046 7 22 7.89543 22 9V17C22 18.1046 21.1046 19 20 19H18V20C18 21.1046 17.1046 22 16 22H6C4.89543 22 4 21.1046 4 20V5Z" fill="currentColor"/>
                                    <path d="M18 10H22M15 13H15.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <rect x="2" y="7" width="16" height="12" rx="2" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </span>
                            <select name="payment_method" x-model="paymentMethod" required class="form-input-solid">
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div x-show="paymentStatus !== 'paid'" class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Dibayar (Rp)</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">Rp</span>
                            <input type="number" name="paid_amount" x-model.number="paidAmount" @input="updateDueAmount"
                                   class="form-input-solid">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Sisa Hutang (Rp)</label>
                        <div class="input-group-solid bg-gray-50">
                            <span class="input-prefix">Rp</span>
                            <input type="text" readonly :value="formatRupiah(dueAmount)"
                                   class="form-input-solid font-bold text-red-600">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Message/Chat -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" fill="currentColor"/>
                                <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <textarea name="notes" rows="2" placeholder="Catatan transaksi..." class="form-input-solid h-auto py-2"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full py-3 font-bold rounded-lg transition-transform active:scale-[0.98]">Simpan Transaksi</button>
            </div>

            <!-- Modal Galeri Picker -->
            <div x-show="galleryModalOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                 
                 <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                     <div class="fixed inset-0 transition-opacity bg-gray-500/75 backdrop-blur-sm" @click="galleryModalOpen = false"></div>
                     
                     <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                     
                     <div class="inline-block align-bottom bg-white/95 backdrop-blur-xl border border-white/50 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-4 w-full">
                         <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                             <h3 class="text-sm font-bold text-dark">Pilih Gambar dari Galeri</h3>
                             <button type="button" @click="galleryModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
                         </div>
                         
                         <!-- Search input inside modal -->
                         <div class="mt-3">
                             <input type="text" x-model="searchQuery" @input.debounce.300ms="loadGalleryImages()" 
                                    placeholder="Cari nama gambar..." 
                                    class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                         </div>
                         
                         <!-- Gallery images list -->
                         <div class="mt-3 max-h-80 overflow-y-auto">
                             <div x-show="isLoading" class="text-center py-8 text-xs text-gray-400">Memuat gambar...</div>
                             
                             <div x-show="!isLoading && galleryImages.length === 0" class="text-center py-8 text-xs text-gray-400">Galeri kosong atau gambar tidak ditemukan.</div>
                             
                             <div x-show="!isLoading && galleryImages.length > 0" class="grid grid-cols-3 gap-2">
                                 <template x-for="image in galleryImages" :key="image.id">
                                     <div class="relative group cursor-pointer border border-gray-150 rounded-lg overflow-hidden aspect-square bg-gray-50 hover:border-primary-400 transition-colors"
                                          @click="selectGalleryImage(image)">
                                         <img :src="image.url" class="w-full h-full object-cover">
                                         <div class="absolute bottom-0 inset-x-0 bg-black/60 text-white text-[8px] truncate p-1" :title="image.filename" x-text="image.filename"></div>
                                     </div>
                                 </template>
                             </div>
                         </div>
                     </div>
                 </div>
            </div>

             <!-- Modal Quick Add Supplier -->
             <div x-show="quickSupplierOpen" 
                  class="fixed inset-0 z-50 overflow-y-auto" 
                  style="display: none;"
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-200"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0">
                  
                  <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                      <div class="fixed inset-0 transition-opacity bg-gray-500/75 backdrop-blur-sm" @click="closeQuickSupplierModal()"></div>
                      
                      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                      
                      <div class="inline-block align-bottom bg-white/95 backdrop-blur-xl border border-white/50 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-5 w-full">
                          <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                              <h3 class="text-sm font-bold text-dark">Tambah Tengkulak Baru</h3>
                              <button type="button" @click="closeQuickSupplierModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
                          </div>
                          
                          <div class="mt-4 space-y-4">
                              <div>
                                  <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Tengkulak *</label>
                                  <input type="text" x-model="newSupplierName" placeholder="Contoh: Haji Anwar"
                                         class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                              </div>
                              <div>
                                  <label class="block text-xs font-semibold text-gray-500 mb-1">Nomor Telepon</label>
                                  <input type="text" x-model="newSupplierPhone" placeholder="Contoh: 08123456789"
                                         class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                              </div>
                              <div>
                                  <label class="block text-xs font-semibold text-gray-500 mb-1">Alamat</label>
                                  <textarea x-model="newSupplierAddress" placeholder="Alamat lengkap..." rows="2"
                                            class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white"></textarea>
                              </div>
                              <div>
                                  <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan</label>
                                  <input type="text" x-model="newSupplierNotes" placeholder="Catatan khusus..."
                                         class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                              </div>
                          </div>
                          
                          <div class="mt-6 flex justify-end gap-2">
                              <button type="button" @click="closeQuickSupplierModal()" 
                                      class="px-4 py-2 text-xs font-semibold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors border border-gray-200">
                                  Batal
                              </button>
                              <button type="button" @click="saveQuickSupplier()" :disabled="isSavingSupplier || !newSupplierName.trim()"
                                      class="px-4 py-2 text-xs font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-colors shadow flex items-center justify-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                  <span x-show="isSavingSupplier">Menyimpan...</span>
                                  <span x-show="!isSavingSupplier">Simpan</span>
                              </button>
                          </div>
             <!-- Modal Preview Foto Besar -->
             <div x-show="previewModalOpen" 
                  class="fixed inset-0 z-50 overflow-y-auto" 
                  style="display: none;"
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-200"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  @keydown.escape.window="previewModalOpen = false">
                  
                  <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                      <div class="fixed inset-0 transition-opacity bg-black/80 backdrop-blur-sm" @click="previewModalOpen = false"></div>
                      
                      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                      
                      <div class="inline-block align-bottom bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-3xl w-full p-2 relative">
                          <div class="absolute top-4 right-4 z-10">
                              <button type="button" @click="previewModalOpen = false" 
                                      class="w-8 h-8 rounded-full bg-black/50 text-white hover:bg-black/70 flex items-center justify-center font-bold text-lg transition-colors">
                                  &times;
                              </button>
                          </div>
                          <img :src="imagePreview" class="w-full max-h-[80vh] object-contain rounded-xl">
                      </div>
                  </div>
             </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function purchaseForm() {
            return {
                supplierId: '',
                purchaseDate: (() => {
                    const d = new Date();
                    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
                })(),
                items: [],
                paymentStatus: 'paid',
                paymentMethod: 'cash',
                paidAmount: 0,
                dueAmount: 0,
                totalAmount: 0,
                additionalCost: 0,
                additionalCostNotes: '',
                previewModalOpen: false,
                
                // Quick Supplier State
                quickSupplierOpen: false,
                newSupplierName: '',
                newSupplierPhone: '',
                newSupplierAddress: '',
                newSupplierNotes: '',
                isSavingSupplier: false,
                
                // Gallery State
                imagePreview: null,
                dragging: false,
                galleryFilepath: '',
                galleryModalOpen: false,
                galleryImages: [],
                searchQuery: '',
                isLoading: false,

                handleFile(file) {
                    if (!file) return;
                    window.cropImage(file, (croppedBlob) => {
                        const croppedFile = new File([croppedBlob], file.name, { type: file.type });
                        const dt = new DataTransfer();
                        dt.items.add(croppedFile);
                        if (this.$refs.fileInput) {
                            this.$refs.fileInput.files = dt.files;
                        }
                        this.galleryFilepath = '';
                        this.imagePreview = URL.createObjectURL(croppedBlob);
                    }, (originalFile) => {
                        this.galleryFilepath = '';
                        this.imagePreview = URL.createObjectURL(originalFile);
                    }, () => {
                        if (this.$refs.fileInput) {
                            this.$refs.fileInput.value = '';
                        }
                    });
                },
                closeQuickSupplierModal() {
                    this.quickSupplierOpen = false;
                    this.newSupplierName = '';
                    this.newSupplierPhone = '';
                    this.newSupplierAddress = '';
                    this.newSupplierNotes = '';
                },
                saveQuickSupplier() {
                    if (!this.newSupplierName.trim()) return;
                    this.isSavingSupplier = true;
                    
                    fetch('{{ route('suppliers.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.newSupplierName,
                            phone: this.newSupplierPhone,
                            address: this.newSupplierAddress,
                            notes: this.newSupplierNotes
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingSupplier = false;
                        if (data.success && data.supplier) {
                            // Add new option to native select
                            const newOption = new Option(data.supplier.name, data.supplier.id, true, true);
                            $('#supplier-select').append(newOption).trigger('change');
                            this.supplierId = data.supplier.id;
                            
                            // Close modal
                            this.closeQuickSupplierModal();
                            
                            // Show success SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Tengkulak baru berhasil ditambahkan.',
                                timer: 2000,
                                showConfirmButton: false,
                                timerProgressBar: true,
                                customClass: {
                                    popup: 'rounded-2xl font-sans'
                                }
                            });
                        } else {
                            throw new Error(data.message || 'Gagal menambahkan supplier.');
                        }
                    })
                    .catch(err => {
                        this.isSavingSupplier = false;
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: err.message || 'Terjadi kesalahan saat menambahkan supplier.',
                            customClass: {
                                popup: 'rounded-2xl font-sans border-0'
                            }
                        });
                    });
                },
                openGalleryModal() {
                    this.galleryModalOpen = true;
                    this.loadGalleryImages();
                },
                loadGalleryImages() {
                    this.isLoading = true;
                    fetch('{{ route('api.galleries') }}?search=' + encodeURIComponent(this.searchQuery))
                        .then(res => res.json())
                        .then(data => {
                            this.galleryImages = data;
                            this.isLoading = false;
                        })
                        .catch(err => {
                            console.error(err);
                            this.isLoading = false;
                        });
                },
                selectGalleryImage(image) {
                    this.galleryFilepath = image.filepath;
                    this.imagePreview = image.url;
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = '';
                    }
                    this.galleryModalOpen = false;
                },
                clearImage() {
                    this.imagePreview = null;
                    this.galleryFilepath = '';
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = '';
                    }
                },

                init() {
                    this.addItem();
                    this.$watch('paymentStatus', value => {
                        if (value === 'paid') {
                            this.paidAmount = this.totalAmount;
                            this.dueAmount = 0;
                        } else if (value === 'unpaid') {
                            this.paidAmount = 0;
                            this.dueAmount = this.totalAmount;
                        } else {
                            this.paidAmount = 0;
                            this.dueAmount = this.totalAmount;
                        }
                    });
                },

                addItem() {
                    this.items.push({
                        productId: '',
                        quantity: 1,
                        unitPrice: 0,
                        subtotal: 0,
                        buyUnit: '',
                        priceHistory: []
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    }
                },

                productChanged(index) {
                    const item = this.items[index];
                    if (!item.productId) {
                        item.buyUnit = '';
                        item.priceHistory = [];
                        return;
                    }

                    // Fetch price history
                    this.fetchPriceHistory(index);
                },

                fetchPriceHistory(index) {
                    const item = this.items[index];
                    if (!this.supplierId || !item.productId) {
                        item.priceHistory = [];
                        return;
                    }

                    fetch(`{{ route('purchases.price-history') }}?supplier_id=${this.supplierId}&product_id=${item.productId}`)
                        .then(res => res.json())
                        .then(data => {
                            item.priceHistory = data;
                            if (data.length > 0 && item.unitPrice === 0) {
                                // Default unit price to last purchase price if currently 0
                                item.unitPrice = data[0].unit_price;
                                this.calculateSubtotal(index);
                            }
                        });
                },

                fetchPriceHistoryForAll() {
                    this.items.forEach((item, index) => {
                        this.fetchPriceHistory(index);
                    });
                },

                calculateSubtotal(index) {
                    const item = this.items[index];
                    item.subtotal = item.quantity * item.unitPrice;
                    this.calculateTotal();
                },

                calculateTotal() {
                    const itemsSubtotal = this.items.reduce((sum, item) => sum + item.subtotal, 0);
                    const additional = parseFloat(this.additionalCost || 0);
                    this.totalAmount = itemsSubtotal + additional;

                    if (this.paymentStatus === 'paid') {
                        this.paidAmount = this.totalAmount;
                        this.dueAmount = 0;
                    } else if (this.paymentStatus === 'unpaid') {
                        this.paidAmount = 0;
                        this.dueAmount = this.totalAmount;
                    } else {
                        this.updateDueAmount();
                    }
                },

                updateDueAmount() {
                    this.dueAmount = Math.max(0, this.totalAmount - this.paidAmount);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>


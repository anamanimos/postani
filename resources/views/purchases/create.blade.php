@push('styles')
<style>
    /* Card Solid Styling */
    .card-solid {
        background-color: #FFFFFF !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    
    /* Input Solid Styling */
    .form-input-solid {
        width: 100%;
        background-color: #F9FAFB !important;
        border: 1.5px solid #E5E7EB !important;
        color: #1F2937 !important;
        border-radius: 12px !important;
        font-size: 0.875rem !important;
        padding: 0.625rem 1rem !important;
        transition: all 0.2s ease-in-out !important;
    }
    .form-input-solid:focus {
        outline: none !important;
        background-color: #FFFFFF !important;
        border-color: #16A34A !important;
        box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.12) !important;
    }
    
    /* Select2 Solid Override */
    .select2-container--default .select2-selection--single {
        background-color: #F9FAFB !important;
        border: 1.5px solid #E5E7EB !important;
        border-radius: 12px !important;
        height: 44px !important;
        display: flex !important;
        align-items: center !important;
        backdrop-filter: none !important;
        transition: all 0.2s ease-in-out !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--single:focus-within {
        background-color: #FFFFFF !important;
        border-color: #16A34A !important;
        box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.12) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .select2-dropdown {
        background-color: #FFFFFF !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        backdrop-filter: none !important;
    }
    
    /* Desktop Max-width Layout Override */
    @media (min-width: 1024px) {
        main.max-w-lg {
            max-width: 72rem !important; /* xl container */
        }
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

    <div class="px-4 py-5 pb-24" x-data="purchaseForm()">
        <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data" 
              class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            @csrf

            {{-- Kolom Kiri: Detail Barang (Lebar) --}}
            <div class="lg:col-span-2 space-y-5">
                {{-- Detail Barang --}}
                <div class="card-solid p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                        <div class="flex items-center gap-2">
                            <!-- Duotone Icon: Package/Shopping Bag -->
                            <svg class="w-5 h-5 text-primary-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M19.5 9.5L12 3.5L4.5 9.5V18.5C4.5 19.6046 5.39543 20.5 6.5 20.5H17.5C18.6046 20.5 19.5 19.6046 19.5 18.5V9.5Z" fill="currentColor"/>
                                <path d="M9 22V12H15V22M12 3.5V20.5" stroke="currentColor" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                            </svg>
                            <h3 class="text-sm font-bold text-dark">Item Pembelian</h3>
                        </div>
                        <button type="button" @click="addItem()" class="text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 px-3 py-1.5 rounded-lg border border-primary-200 transition-colors">
                            ➕ Tambah Item
                        </button>
                    </div>

                    {{-- Item List --}}
                    <div class="space-y-4 divide-y divide-gray-150 pt-1">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="pt-4 pb-3 px-4 rounded-xl bg-gray-50/60 border border-gray-150 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-500" x-text="'Item #' + (index + 1)"></span>
                                    <button type="button" @click="removeItem(index)" class="text-xs text-red-500 hover:text-red-700 font-semibold bg-red-50 px-2.5 py-1.5 rounded-lg border border-red-150 transition-colors">
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-2">
                                    {{-- Product select --}}
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Produk</label>
                                        <select :name="'items['+index+'][product_id]'" 
                                                x-init="
                                                    $nextTick(() => {
                                                        $($el).select2({ width: '100%' }).on('change', (e) => {
                                                            item.productId = e.target.value;
                                                            productChanged(index);
                                                        });
                                                    });
                                                "
                                                required
                                                class="form-input-solid py-1.5 px-3">
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-buy-unit="{{ $product->buyUnit->symbol }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        {{-- Quantity --}}
                                        <div>
                                            <label class="block text-[10px] font-semibold text-gray-500 mb-0.5" x-text="'Jumlah (' + (item.buyUnit || 'satuan') + ')'"></label>
                                            <input type="number" step="any" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" @input="calculateSubtotal(index)" required
                                                   class="form-input-solid py-1.5 px-3">
                                        </div>
                                        {{-- Unit Price --}}
                                        <div>
                                            <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Harga Beli Satuan (Rp)</label>
                                            <input type="number" :name="'items['+index+'][unit_price]'" x-model.number="item.unitPrice" @input="calculateSubtotal(index)" required
                                                   class="form-input-solid py-1.5 px-3">
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
                                    <div class="flex items-center justify-between pt-1.5 text-xs text-gray-500">
                                        <span>Subtotal:</span>
                                        <span class="font-bold text-dark" x-text="formatRupiah(item.subtotal)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Informasi Pembelian & Ringkasan Pembayaran --}}
            <div class="lg:col-span-1 space-y-5">
                {{-- Informasi Pembelian Card --}}
                <div class="card-solid p-5 space-y-4">
                    <div class="flex items-center gap-2 border-b border-gray-100 pb-2.5">
                        <!-- Duotone Icon: Supplier/Document -->
                        <svg class="w-5 h-5 text-primary-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M3 10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H13C16.7712 2 18.6569 2 19.8284 3.17157C21 4.34315 21 6.22876 21 10V14C21 17.7712 21 19.6569 19.8284 20.8284C18.6569 22 16.7712 22 13 22H11C7.22876 22 5.34315 22 4.17157 20.8284C3 19.6569 3 17.7712 3 14V10Z" fill="currentColor"/>
                            <path d="M8 7H16M8 12H16M8 17H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <h3 class="text-sm font-bold text-dark">Informasi Pembelian</h3>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Tengkulak / Supplier</label>
                        <div class="flex gap-2 items-center">
                            <div class="flex-1">
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
                            <button type="button" @click="quickSupplierOpen = true" 
                                    class="w-10 h-10 shrink-0 bg-primary-50 hover:bg-primary-100 border border-primary-200 text-primary-600 rounded-xl flex items-center justify-center transition-colors active:scale-95 shadow-sm text-lg font-bold"
                                    title="Tambah Tengkulak Baru">
                                +
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Pembelian</label>
                        <input type="text" name="purchase_date" required 
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
                               class="datepicker form-input-solid">
                    </div>

                    <div class="space-y-3 pt-2 border-t border-gray-150">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Nomor Nota Tengkulak (Opsional)</label>
                            <input type="text" name="supplier_invoice_number" placeholder="Masukkan nomor nota dari tengkulak..."
                                   class="form-input-solid">
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
                                 class="relative border-2 border-dashed rounded-xl p-4 flex flex-col items-center justify-center transition-all duration-200"
                                 :class="dragging ? 'border-primary-500 bg-primary-50/20' : 'border-gray-200 hover:border-primary-400 bg-gray-50/40'">
                                 
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
                                              class="max-h-36 rounded-lg object-contain border border-gray-150 shadow-sm bg-white cursor-zoom-in hover:brightness-95 active:scale-95 transition-all duration-200"
                                              title="Klik untuk memperbesar">
                                     </div>
                                 </template>
                            </div>
                            
                            <div class="flex gap-2 mt-2">
                                <button type="button" @click="openGalleryModal()" 
                                        class="flex-1 py-2 text-xs font-semibold text-primary-600 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors border border-primary-200">
                                    🖼️ Pilih dari Galeri
                                </button>
                                <button type="button" x-show="imagePreview || galleryFilepath" @click="clearImage()" 
                                        class="px-3 py-2 text-xs font-semibold text-red-500 bg-red-50/50 rounded-xl hover:bg-red-100/50 transition-colors border border-red-200">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary & Payment Card --}}
                <div class="card-solid p-5 space-y-4">
                    <div class="flex items-center gap-2 border-b border-gray-100 pb-2.5">
                        <!-- Duotone Icon: Wallet/Cash Register -->
                        <svg class="w-5 h-5 text-primary-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.3" d="M2 8.5C2 5.46243 4.46243 3 7.5 3H16.5C19.5376 3 22 5.46243 22 8.5V15.5C22 18.5376 19.5376 21 16.5 21H7.5C4.46243 21 2 18.5376 2 15.5V8.5Z" fill="currentColor"/>
                            <path d="M22 10H17C15.3431 10 14 11.3431 14 13C14 14.6569 15.3431 16 17 16H22M18 13H18.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <h3 class="text-sm font-bold text-dark">Ringkasan & Pembayaran</h3>
                    </div>

                    <div class="flex items-center justify-between text-sm font-semibold border-b border-gray-100 pb-2.5">
                        <span>Total Pembelian</span>
                        <span class="text-lg font-extrabold text-primary-600" x-text="formatRupiah(totalAmount)"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100/50 pb-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Biaya Tambahan (Parkir/Bensin) (Rp)</label>
                            <input type="number" name="additional_cost" x-model.number="additionalCost" @input="calculateTotal"
                                   placeholder="Contoh: 5000" class="form-input-solid">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan Biaya Tambahan</label>
                            <input type="text" name="additional_cost_notes" x-model="additionalCostNotes"
                                   placeholder="Contoh: Parkir & Beli Bensin" class="form-input-solid">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Status Pembayaran</label>
                            <select name="payment_status" x-model="paymentStatus" required class="form-input-solid">
                                <option value="unpaid">Hutang (Kredit)</option>
                                <option value="partial">Bayar Sebagian</option>
                                <option value="paid">Lunas (Paid)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Metode Pembayaran</label>
                            <select name="payment_method" x-model="paymentMethod" required class="form-input-solid">
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div x-show="paymentStatus !== 'paid'" class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Dibayar (Rp)</label>
                            <input type="number" name="paid_amount" x-model.number="paidAmount" @input="updateDueAmount"
                                   class="form-input-solid">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Sisa Hutang (Rp)</label>
                            <input type="text" readonly :value="formatRupiah(dueAmount)"
                                   class="form-input-solid bg-gray-50 font-bold text-red-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan</label>
                        <textarea name="notes" rows="2" placeholder="Catatan transaksi..." class="form-input-solid"></textarea>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3.5 font-bold rounded-xl shadow-lg transition-transform active:scale-[0.98]">Simpan Transaksi</button>
                </div>
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
                purchaseDate: new Date().toISOString().split('T')[0],
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

                    // Get buyUnit symbol
                    const selectEl = document.querySelectorAll('select')[index + 2]; // adjusting for offset of supplier_id
                    const selectedOpt = selectEl.options[selectEl.selectedIndex];
                    item.buyUnit = selectedOpt.getAttribute('data-buy-unit') || '';

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

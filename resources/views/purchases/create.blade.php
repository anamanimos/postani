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
        <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- Tengkulak & Tanggal --}}
            <div class="glass-card p-4 space-y-3">
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
                                    class="form-input-glass">
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
                           class="datepicker form-input-glass">
                </div>
                <div class="space-y-3 pt-2 border-t border-gray-100/50">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Nomor Nota Tengkulak (Opsional)</label>
                        <input type="text" name="supplier_invoice_number" placeholder="Masukkan nomor nota dari tengkulak..."
                               class="form-input-glass">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Foto Nota Fisik (Opsional)</label>
                        <input type="hidden" name="gallery_filepath" x-model="galleryFilepath">
                        
                        <div @dragover.prevent="dragging = true"
                             @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; if ($event.dataTransfer.files.length) { $refs.fileInput.files = $event.dataTransfer.files; handleFile($event.dataTransfer.files[0]); }"
                             class="relative border-2 border-dashed rounded-xl p-4 flex flex-col items-center justify-center transition-all duration-200"
                             :class="dragging ? 'border-primary-500 bg-primary-50/20' : 'border-gray-200 hover:border-primary-400 bg-white/40'">
                             
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
                                     <img :src="imagePreview" class="max-h-36 rounded-lg object-contain border border-gray-100 shadow-sm bg-white">
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

            {{-- Detail Barang --}}
            <div class="glass-card p-4 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <h3 class="text-sm font-semibold text-dark">Item Pembelian</h3>
                    <button type="button" @click="addItem()" class="text-xs font-bold text-primary-600 hover:text-primary-700">
                        ➕ Tambah Item
                    </button>
                </div>

                {{-- Item List --}}
                <div class="space-y-4 divide-y divide-gray-100 pt-1">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="pt-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-400" x-text="'Item #' + (index + 1)"></span>
                                <button type="button" @click="removeItem(index)" class="text-xs text-red-500 hover:text-red-700">
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
                                            class="form-input-glass py-1.5 px-3">
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
                                               class="form-input-glass py-1.5 px-3">
                                    </div>
                                    {{-- Unit Price --}}
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Harga Beli Satuan (Rp)</label>
                                        <input type="number" :name="'items['+index+'][unit_price]'" x-model.number="item.unitPrice" @input="calculateSubtotal(index)" required
                                               class="form-input-glass py-1.5 px-3">
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
            </div>

            {{-- Summary & Payment --}}
            <div class="glass-card p-4 space-y-4">
                <div class="flex items-center justify-between text-sm font-semibold border-b border-gray-100 pb-2">
                    <span>Total Pembelian</span>
                    <span class="text-base font-bold text-primary-600" x-text="formatRupiah(totalAmount)"></span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Status Pembayaran</label>
                        <select name="payment_status" x-model="paymentStatus" required class="form-input-glass">
                            <option value="unpaid">Hutang (Kredit)</option>
                            <option value="partial">Bayar Sebagian</option>
                            <option value="paid">Lunas (Paid)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Metode Pembayaran</label>
                        <select name="payment_method" x-model="paymentMethod" required class="form-input-glass">
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                </div>

                <div x-show="paymentStatus !== 'paid'" class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Dibayar (Rp)</label>
                        <input type="number" name="paid_amount" x-model.number="paidAmount" @input="updateDueAmount"
                               class="form-input-glass">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Sisa Hutang (Rp)</label>
                        <input type="text" readonly :value="formatRupiah(dueAmount)"
                               class="form-input-glass bg-gray-50 font-semibold text-red-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan transaksi..." class="form-input-glass"></textarea>
                </div>

                <button type="submit" class="btn-primary w-full py-3 font-bold">Simpan Transaksi</button>
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
                    this.totalAmount = this.items.reduce((sum, item) => sum + item.subtotal, 0);
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

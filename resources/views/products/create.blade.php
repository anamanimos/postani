<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Tambah Produk</h2>
        </div>
    </x-slot>

    <div class="py-5 pb-24 max-w-lg mx-auto">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" 
              @paste.window="
                  const items = ($event.clipboardData || window.clipboardData).items;
                  if (items) {
                      for (let i = 0; i < items.length; i++) {
                          if (items[i].type.indexOf('image') !== -1) {
                              const file = items[i].getAsFile();
                              if (file) {
                                  $event.preventDefault();
                                  handleRawFile(file);
                                  break;
                              }
                          }
                      }
                  }
              "
              x-data="productForm()">
            @csrf
            <input type="hidden" name="gallery_filepath" x-model="galleryFilepath">
            <div class="space-y-4">
                {{-- Image Upload --}}
                <div class="card-solid p-4">
                    <label class="block text-sm font-medium text-dark mb-2">Foto Produk</label>
                    <div class="relative">
                        <div class="w-full aspect-video rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 hover:border-primary-400 transition-colors"
                            @click="$refs.imageInput.click()">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!imagePreview">
                                <div class="text-center">
                                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-xs text-gray-400">Tap untuk upload foto</p>
                                </div>
                            </template>
                        </div>
                        <input type="file" name="image" x-ref="imageInput" @change="handleImageUpload($event)" accept="image/*" class="hidden">
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
                    @error('image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Basic Info --}}
                <div class="card-solid p-4 space-y-4">
                    <h3 class="text-sm font-semibold text-dark border-b border-gray-150 pb-2">Informasi Dasar</h3>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Produk *</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">
                                <!-- Duotone Icon: Package -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                                    <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Pupuk NPK Mutiara 16-16-16"
                                   class="form-input-solid">
                        </div>
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Kategori</label>
                        <div class="flex gap-2 items-center">
                            <div class="input-group-solid flex-1">
                                <span class="input-prefix">
                                    <!-- Duotone Icon: Folder -->
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M19 20H5C3.89543 20 3 19.1046 3 18V6C3 4.89543 3.89543 4 5 4H9.58579C9.85101 4 10.1054 4.10536 10.2929 4.29289L12.7071 6.70711C12.8946 6.89464 13.149 7 13.4142 7H19C20.1046 7 21 7.89543 21 9V18C21 19.1046 20.1046 20 19 20Z" fill="currentColor"/>
                                        <path d="M3 8H21M19 20H5C3.89543 20 3 19.1046 3 18V6C3 4.89543 3.89543 4 5 4H9.58579C9.85101 4 10.1054 4.10536 10.2929 4.29289L12.7071 6.70711C12.8946 6.89464 13.149 7 13.4142 7H19C20.1046 7 21 7.89543 21 9V18C21 19.1046 20.1046 20 19 20Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <select name="category_id" id="category-select"
                                            x-init="
                                                $($el).select2({ width: '100%' }).on('change', (e) => {
                                                    categoryId = e.target.value;
                                                });
                                            "
                                            class="form-input-solid">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories ?? [] as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="button" @click="quickCategoryOpen = true" 
                                    class="w-10 h-10 shrink-0 bg-primary-50 hover:bg-primary-100 border border-primary-200 text-primary-600 rounded-lg flex items-center justify-center transition-colors active:scale-95 shadow-sm text-lg font-bold"
                                    title="Tambah Kategori Baru">
                                +
                            </button>
                        </div>
                        @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">SKU (Kode Produk)</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">
                                <!-- Duotone Icon: Hashtag -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M4 9H20M4 15H20" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                    <path d="M9 3L7 21M17 3L15 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Contoh: NPK-001"
                                   class="form-input-solid">
                        </div>
                        @error('sku') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Unit & Conversion --}}
                <div class="card-solid p-4 space-y-4">
                    <h3 class="text-sm font-semibold text-dark border-b border-gray-150 pb-2">Satuan & Konversi</h3>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Satuan Beli *</label>
                            <div class="input-group-solid">
                                <span class="input-prefix">
                                    <!-- Duotone Icon: Archive -->
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M21 8V20C21 21.1046 20.1046 22 19 22H5C3.89543 22 3 21.1046 3 20V8H21Z" fill="currentColor"/>
                                        <rect x="2" y="3" width="20" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <path d="M10 12H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M3 8V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V8" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </span>
                                <select name="buy_unit_id" required class="form-input-solid">
                                    <option value="">Pilih</option>
                                    @foreach($units ?? [] as $unit)
                                        <option value="{{ $unit->id }}" {{ old('buy_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('buy_unit_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Satuan Jual *</label>
                            <div class="input-group-solid">
                                <span class="input-prefix">
                                    <!-- Duotone Icon: Archive -->
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M21 8V20C21 21.1046 20.1046 22 19 22H5C3.89543 22 3 21.1046 3 20V8H21Z" fill="currentColor"/>
                                        <rect x="2" y="3" width="20" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <path d="M10 12H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M3 8V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V8" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </span>
                                <select name="sell_unit_id" required class="form-input-solid">
                                    <option value="">Pilih</option>
                                    @foreach($units ?? [] as $unit)
                                        <option value="{{ $unit->id }}" {{ old('sell_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('sell_unit_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Faktor Konversi *</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">
                                <!-- Duotone Icon: Swap -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M17 17H7V14L3 18L7 22V19H17V17Z" fill="currentColor"/>
                                    <path opacity="0.3" d="M7 7H17V10L21 6L17 2V5H7V7Z" fill="currentColor"/>
                                    <path d="M17 17H7V14L3 18L7 22V19H17V17ZM7 7H17V10L21 6L17 2V5H7V7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input type="number" name="conversion_factor" value="{{ old('conversion_factor', 1) }}" min="0.01" step="0.01" required placeholder="1 sat. beli = ? sat. jual"
                                   class="form-input-solid">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">1 satuan beli = berapa satuan jual (Contoh: 1 karung = 25 kg, isi 25)</p>
                        @error('conversion_factor') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Pricing & Stock --}}
                <div class="card-solid p-4 space-y-4">
                    <h3 class="text-sm font-semibold text-dark border-b border-gray-150 pb-2">Harga & Stok</h3>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Harga Jual (per satuan jual) *</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">Rp</span>
                            <input type="number" name="selling_price" value="{{ old('selling_price') }}" min="0" required placeholder="0"
                                   class="form-input-solid">
                        </div>
                        @error('selling_price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Stok Minimum</label>
                        <div class="input-group-solid">
                            <span class="input-prefix">
                                <!-- Duotone Icon: Alert Shield -->
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 22C12 22 20 18 20 12V5L12 2L4 5V12C4 18 12 22 12 22Z" fill="currentColor"/>
                                    <path d="M12 8V13M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 22C12 22 20 18 20 12V5L12 2L4 5V12C4 18 12 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" min="0" placeholder="0"
                                   class="form-input-solid">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Peringatan muncul saat stok mencapai angka ini</p>
                        @error('min_stock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Status Toggle --}}
                <div class="card-solid p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-dark">Status Aktif</p>
                            <p class="text-[10px] text-gray-400">Produk nonaktif tidak muncul di kasir</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" x-model="isActive" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-primary-600 peer-focus:ring-2 peer-focus:ring-primary-300 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary w-full py-3.5 font-bold rounded-lg transition-transform active:scale-[0.98] text-sm shadow-lg">
                    Simpan Produk
                </button>
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

            <!-- Modal Quick Add Category -->
            <div x-show="quickCategoryOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                 
                 <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                     <div class="fixed inset-0 transition-opacity bg-gray-500/75 backdrop-blur-sm" @click="closeQuickCategoryModal()"></div>
                     
                     <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                     
                     <div class="inline-block align-bottom bg-white/95 backdrop-blur-xl border border-white/50 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-5 w-full">
                         <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                             <h3 class="text-sm font-bold text-dark">Tambah Kategori Baru</h3>
                             <button type="button" @click="closeQuickCategoryModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
                         </div>
                         
                         <div class="mt-4 space-y-4">
                             <div>
                                 <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Kategori *</label>
                                 <input type="text" x-model="newCategoryName" placeholder="Contoh: Pupuk Organik"
                                        class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
                             </div>
                             <div>
                                 <label class="block text-xs font-semibold text-gray-500 mb-1">Deskripsi</label>
                                 <textarea x-model="newCategoryDescription" placeholder="Deskripsi kategori..." rows="3"
                                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white"></textarea>
                             </div>
                         </div>
                         
                         <div class="mt-6 flex justify-end gap-2">
                             <button type="button" @click="closeQuickCategoryModal()" 
                                     class="px-4 py-2 text-xs font-semibold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors border border-gray-200">
                                 Batal
                             </button>
                             <button type="button" @click="saveQuickCategory()" :disabled="isSavingCategory || !newCategoryName.trim()"
                                     class="px-4 py-2 text-xs font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-colors shadow flex items-center justify-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                 <span x-show="isSavingCategory">Menyimpan...</span>
                                 <span x-show="!isSavingCategory">Simpan</span>
                             </button>
                         </div>
                     </div>
                 </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productForm', () => ({
        imagePreview: null,
        galleryFilepath: '',
        galleryModalOpen: false,
        galleryImages: [],
        searchQuery: '',
        isLoading: false,
        isActive: true,

        // Quick Category State
        quickCategoryOpen: false,
        newCategoryName: '',
        newCategoryDescription: '',
        isSavingCategory: false,
        categoryId: '',

        handleRawFile(file) {
            if (!file) return;
            window.cropImage(file, (croppedBlob) => {
                const croppedFile = new File([croppedBlob], file.name, { type: file.type });
                const dt = new DataTransfer();
                dt.items.add(croppedFile);
                this.$refs.imageInput.files = dt.files;
                this.galleryFilepath = '';
                this.imagePreview = URL.createObjectURL(croppedBlob);
            }, (originalFile) => {
                const dt = new DataTransfer();
                dt.items.add(originalFile);
                this.$refs.imageInput.files = dt.files;
                this.galleryFilepath = '';
                this.imagePreview = URL.createObjectURL(originalFile);
            }, () => {
                this.$refs.imageInput.value = '';
            });
        },
        handleImageUpload(e) {
            const file = e.target.files[0];
            if (!file) return;
            window.cropImage(file, (croppedBlob) => {
                const croppedFile = new File([croppedBlob], file.name, { type: file.type });
                const dt = new DataTransfer();
                dt.items.add(croppedFile);
                this.$refs.imageInput.files = dt.files;
                this.galleryFilepath = '';
                this.imagePreview = URL.createObjectURL(croppedBlob);
            }, (originalFile) => {
                this.galleryFilepath = '';
                this.imagePreview = URL.createObjectURL(originalFile);
            }, () => {
                this.$refs.imageInput.value = '';
            });
        },
        closeQuickCategoryModal() {
            this.quickCategoryOpen = false;
            this.newCategoryName = '';
            this.newCategoryDescription = '';
        },
        saveQuickCategory() {
            if (!this.newCategoryName.trim()) return;
            this.isSavingCategory = true;

            fetch('{{ route("categories.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: this.newCategoryName,
                    description: this.newCategoryDescription
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isSavingCategory = false;
                if (data.success && data.category) {
                    const newOption = new Option(data.category.name, data.category.id, true, true);
                    $('#category-select').append(newOption).trigger('change');
                    this.categoryId = data.category.id;
                    this.closeQuickCategoryModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Kategori baru berhasil ditambahkan.',
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        customClass: { popup: 'rounded-2xl font-sans' }
                    });
                } else {
                    throw new Error(data.message || 'Gagal menambahkan kategori.');
                }
            })
            .catch(err => {
                this.isSavingCategory = false;
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'Terjadi kesalahan saat menambahkan kategori.',
                    customClass: { popup: 'rounded-2xl font-sans border-0' }
                });
            });
        },
        clearImage() {
            this.imagePreview = null;
            this.galleryFilepath = '';
            this.$refs.imageInput.value = '';
        },
        openGalleryModal() {
            this.galleryModalOpen = true;
            this.loadGalleryImages();
        },
        loadGalleryImages() {
            this.isLoading = true;
            fetch('{{ route("api.galleries") }}?search=' + encodeURIComponent(this.searchQuery))
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
            this.$refs.imageInput.value = '';
            this.galleryModalOpen = false;
        }
    }));
});
</script>


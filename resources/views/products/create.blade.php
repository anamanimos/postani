<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Tambah Produk</h2>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" x-data="{
            imagePreview: null,
            galleryFilepath: '',
            galleryModalOpen: false,
            galleryImages: [],
            searchQuery: '',
            isLoading: false,
            isActive: true,

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
                this.$refs.imageInput.value = '';
                this.galleryModalOpen = false;
            }
        }">
            @csrf
            <input type="hidden" name="gallery_filepath" x-model="galleryFilepath">
            <div class="space-y-4">
                {{-- Image Upload --}}
                <div class="rounded-glass border border-white/40 shadow-glass p-4" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
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
                                class="flex-1 py-2 text-xs font-semibold text-primary-600 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors border border-primary-200">
                            🖼️ Pilih dari Galeri
                        </button>
                        <button type="button" x-show="imagePreview || galleryFilepath" @click="clearImage()" 
                                class="px-3 py-2 text-xs font-semibold text-red-500 bg-red-50/50 rounded-xl hover:bg-red-100/50 transition-colors border border-red-200">
                            Hapus
                        </button>
                    </div>
                    @error('image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Basic Info --}}
                <div class="rounded-glass border border-white/40 shadow-glass p-4 space-y-4" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
                    <h3 class="text-sm font-semibold text-dark">Informasi Dasar</h3>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Produk *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-white/80"
                            placeholder="Contoh: Pupuk NPK Mutiara 16-16-16">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                        <select name="category_id" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-white/80">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">SKU (Kode Produk)</label>
                        <input type="text" name="sku" value="{{ old('sku') }}"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-white/80"
                            placeholder="Contoh: NPK-001">
                        @error('sku') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Unit & Conversion --}}
                <div class="rounded-glass border border-white/40 shadow-glass p-4 space-y-4" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
                    <h3 class="text-sm font-semibold text-dark">Satuan & Konversi</h3>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Satuan Beli *</label>
                            <select name="buy_unit_id" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-white/80">
                                <option value="">Pilih</option>
                                @foreach($units ?? [] as $unit)
                                    <option value="{{ $unit->id }}" {{ old('buy_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('buy_unit_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Satuan Jual *</label>
                            <select name="sell_unit_id" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-white/80">
                                <option value="">Pilih</option>
                                @foreach($units ?? [] as $unit)
                                    <option value="{{ $unit->id }}" {{ old('sell_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('sell_unit_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Faktor Konversi *</label>
                        <input type="number" name="conversion_factor" value="{{ old('conversion_factor', 1) }}" min="0.01" step="0.01" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-white/80"
                            placeholder="1 sat. beli = ? sat. jual">
                        <p class="text-xs text-gray-400 mt-1">1 satuan beli = berapa satuan jual</p>
                        @error('conversion_factor') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Pricing & Stock --}}
                <div class="rounded-glass border border-white/40 shadow-glass p-4 space-y-4" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
                    <h3 class="text-sm font-semibold text-dark">Harga & Stok</h3>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Harga Jual (per satuan jual) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                            <input type="number" name="selling_price" value="{{ old('selling_price') }}" min="0" required
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-white/80"
                                placeholder="0">
                        </div>
                        @error('selling_price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Stok Minimum</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" min="0"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-white/80"
                            placeholder="0">
                        <p class="text-xs text-gray-400 mt-1">Peringatan muncul saat stok mencapai angka ini</p>
                        @error('min_stock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Status Toggle --}}
                <div class="rounded-glass border border-white/40 shadow-glass p-4" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-dark">Status Aktif</p>
                            <p class="text-xs text-gray-400">Produk nonaktif tidak muncul di kasir</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" x-model="isActive" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-primary-600 peer-focus:ring-2 peer-focus:ring-primary-300 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full py-3.5 bg-primary-600 text-white font-semibold rounded-glass shadow-lg active:scale-[0.98] transition-transform text-sm">
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
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('products.show', $product) }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform hover:bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-dark">Edit Produk</h2>
                    <p class="text-xs text-gray-400 hidden sm:block truncate max-w-md">{{ $product->name }}</p>
                </div>
            </div>

            {{-- Desktop Header Actions --}}
            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('products.show', $product) }}" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 rounded-xl border border-gray-200 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" form="product-edit-form" class="btn-primary py-2 px-4 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="pb-24 pt-1 max-w-lg mx-auto md:max-w-none">
        <form id="product-edit-form" action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" 
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
              x-data="productEditForm()">
            @csrf
            @method('PUT')
            <input type="hidden" name="gallery_filepath" x-model="galleryFilepath">

            {{-- 12-Column Responsive Grid on Desktop --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-start">
                
                {{-- LEFT COLUMN: Photo Upload & Product Status (4 cols on desktop) --}}
                <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-20">
                    
                    {{-- Photo Upload Card --}}
                    <div class="card-solid p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-dark uppercase tracking-wider">Foto Produk</label>
                            <span class="text-[10px] text-gray-400">Rasio 1:1 disarankan</span>
                        </div>

                        <div class="relative">
                            <div class="w-full aspect-square rounded-2xl bg-gray-50 overflow-hidden flex items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 hover:border-primary-400 hover:bg-primary-50/20 transition-all group"
                                 style="max-height: 280px;"
                                 @click="$refs.imageInput.click()">
                                <template x-if="imagePreview">
                                    <div class="w-full h-full relative group">
                                        <img :src="imagePreview" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span>Ganti Foto</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!imagePreview">
                                    <div class="text-center p-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-2 group-hover:text-primary-600 group-hover:bg-primary-50 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-dark">Klik untuk upload foto</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">atau drag & drop / paste (Ctrl+V)</p>
                                    </div>
                                </template>
                            </div>
                            <input type="file" name="image" x-ref="imageInput" @change="handleImageUpload($event)" accept="image/*" class="hidden">
                        </div>
                        
                        <div class="flex gap-2 pt-1">
                            <button type="button" @click="openGalleryModal()" 
                                    class="flex-1 py-2 text-xs font-semibold text-primary-600 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors border border-primary-200 flex items-center justify-center gap-1.5 shadow-sm">
                                <span>🖼️</span>
                                <span>Pilih dari Galeri</span>
                            </button>
                            <button type="button" x-show="imagePreview || galleryFilepath" @click="clearImage()" 
                                    class="px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors border border-red-200 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                        @error('image') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Product Status & Stock Summary Card --}}
                    <div class="card-solid p-4 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-150 pb-3">
                            <div>
                                <p class="text-xs font-bold text-dark">Status Produk</p>
                                <p class="text-[10px] text-gray-400">Tampilkan di sistem kasir</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" x-model="isActive" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary-600 peer-focus:ring-2 peer-focus:ring-primary-300 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>

                        {{-- Stock Info Read-Only --}}
                        <div class="bg-gray-50/80 rounded-xl p-3 border border-gray-150 space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 font-medium">Stok Saat Ini:</span>
                                <span class="font-bold text-dark text-sm">{{ $product->stock }} {{ $product->sellUnit->symbol ?? '' }}</span>
                            </div>
                            @if($product->last_purchase_price > 0)
                            <div class="flex items-center justify-between text-xs pt-1 border-t border-gray-200/60">
                                <span class="text-gray-500 font-medium">Beli Terakhir:</span>
                                <span class="font-bold text-gray-700">Rp {{ number_format($product->last_purchase_price, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <p class="text-[10px] text-gray-400 pt-1">
                                Stok berubah otomatis melalui transaksi kasir atau mutasi inventaris.
                            </p>
                        </div>
                    </div>

                    {{-- Desktop Quick Actions --}}
                    <div class="hidden lg:block space-y-2">
                        <button type="submit" class="btn-primary w-full py-3 font-bold rounded-xl shadow-md transition-all active:scale-[0.98] text-xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Perbarui Produk</span>
                        </button>
                        <a href="{{ route('products.show', $product) }}" class="w-full py-2.5 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 rounded-xl border border-gray-200 transition-colors flex items-center justify-center">
                            Batal
                        </a>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Product Form Details (8 cols on desktop) --}}
                <div class="lg:col-span-8 space-y-4">
                    
                    {{-- 1. Basic Information Card --}}
                    <div class="card-solid p-5 space-y-4">
                        <div class="flex items-center gap-2 border-b border-gray-150 pb-3">
                            <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                                    <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-dark">Informasi Dasar</h3>
                                <p class="text-[11px] text-gray-400">Identitas utama produk dan pengelompokan</p>
                            </div>
                        </div>

                        <div class="space-y-3.5">
                            {{-- Product Name --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">
                                    Nama Produk <span class="text-red-500">*</span>
                                </label>
                                <div class="input-group-solid">
                                    <span class="input-prefix">
                                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                                            <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required placeholder="Contoh: Pupuk NPK Mutiara 16-16-16"
                                           class="form-input-solid">
                                </div>
                                @error('name') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- 2 Cols: Category & SKU --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- Category --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label>
                                    <div class="input-group-solid">
                                        <span class="input-prefix">
                                            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M19 20H5C3.89543 20 3 19.1046 3 18V6C3 4.89543 3.89543 4 5 4H9.58579C9.85101 4 10.1054 4.10536 10.2929 4.29289L12.7071 6.70711C12.8946 6.89464 13.149 7 13.4142 7H19C20.1046 7 21 7.89543 21 9V18C21 19.1046 20.1046 20 19 20Z" fill="currentColor"/>
                                                <path d="M3 8H21M19 20H5C3.89543 20 3 19.1046 3 18V6C3 4.89543 3.89543 4 5 4H9.58579C9.85101 4 10.1054 4.10536 10.2929 4.29289L12.7071 6.70711C12.8946 6.89464 13.149 7 13.4142 7H19C20.1046 7 21 7.89543 21 9V18C21 19.1046 20.1046 20 19 20Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <select name="category_id" class="form-input-solid"
                                                    x-init="
                                                        $($el).select2({ width: '100%' }).on('change', (e) => {
                                                            categoryId = e.target.value;
                                                        });
                                                        if (categoryId) {
                                                            $($el).val(categoryId).trigger('change');
                                                        }
                                                    ">
                                                <option value="">Pilih Kategori</option>
                                                @foreach($categories ?? [] as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @error('category_id') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                </div>

                                {{-- SKU --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">SKU / Kode Produk</label>
                                    <div class="input-group-solid">
                                        <span class="input-prefix">
                                            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M4 9H20M4 15H20" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                                <path d="M9 3L7 21M17 3L15 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="Contoh: NPK-001"
                                               class="form-input-solid">
                                    </div>
                                    @error('sku') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Unit & Conversion Card --}}
                    <div class="card-solid p-5 space-y-4">
                        <div class="flex items-center gap-2 border-b border-gray-150 pb-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M21 8V20C21 21.1046 20.1046 22 19 22H5C3.89543 22 3 21.1046 3 20V8H21Z" fill="currentColor"/>
                                    <rect x="2" y="3" width="20" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
                                    <path d="M10 12H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M3 8V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V8" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-dark">Satuan & Rasio Konversi</h3>
                                <p class="text-[11px] text-gray-400">Aturan satuan saat pembelian vs saat penjualan</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            {{-- Buy Unit --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">
                                    Satuan Beli (Kulak) <span class="text-red-500">*</span>
                                </label>
                                <div class="input-group-solid">
                                    <span class="input-prefix">
                                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M21 8V20C21 21.1046 20.1046 22 19 22H5C3.89543 22 3 21.1046 3 20V8H21Z" fill="currentColor"/>
                                            <rect x="2" y="3" width="20" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
                                            <path d="M10 12H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                    <select name="buy_unit_id" required class="form-input-solid">
                                        <option value="">Pilih</option>
                                        @foreach($units ?? [] as $unit)
                                            <option value="{{ $unit->id }}" {{ old('buy_unit_id', $product->buy_unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('buy_unit_id') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Sell Unit --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">
                                    Satuan Jual (Kasir) <span class="text-red-500">*</span>
                                </label>
                                <div class="input-group-solid">
                                    <span class="input-prefix">
                                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M21 8V20C21 21.1046 20.1046 22 19 22H5C3.89543 22 3 21.1046 3 20V8H21Z" fill="currentColor"/>
                                            <rect x="2" y="3" width="20" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
                                            <path d="M10 12H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                    <select name="sell_unit_id" required class="form-input-solid">
                                        <option value="">Pilih</option>
                                        @foreach($units ?? [] as $unit)
                                            <option value="{{ $unit->id }}" {{ old('sell_unit_id', $product->sell_unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('sell_unit_id') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Conversion Factor --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">
                                    Faktor Konversi <span class="text-red-500">*</span>
                                </label>
                                <div class="input-group-solid">
                                    <span class="input-prefix">
                                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M17 17H7V14L3 18L7 22V19H17V17Z" fill="currentColor"/>
                                            <path opacity="0.3" d="M7 7H17V10L21 6L17 2V5H7V7Z" fill="currentColor"/>
                                            <path d="M17 17H7V14L3 18L7 22V19H17V17ZM7 7H17V10L21 6L17 2V5H7V7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <input type="number" name="conversion_factor" value="{{ old('conversion_factor', $product->conversion_factor) }}" min="0.01" step="0.01" required placeholder="Contoh: 25"
                                           class="form-input-solid">
                                </div>
                                @error('conversion_factor') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="bg-blue-50/60 rounded-xl p-3 border border-blue-100 flex items-start gap-2.5 text-xs text-blue-800">
                            <span class="text-sm">💡</span>
                            <p class="leading-relaxed">
                                <span class="font-bold">Contoh:</span> Jika kulak 1 Karung = 25 Kg, pilih satuan beli <b>Karung</b>, satuan jual <b>Kg</b>, dan isi faktor konversi <b>25</b>.
                            </p>
                        </div>
                    </div>

                    {{-- 3. Pricing & Minimum Stock Card --}}
                    <div class="card-solid p-5 space-y-4">
                        <div class="flex items-center gap-2 border-b border-gray-150 pb-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 22C16.9706 22 21 17.9706 21 13C21 8.02944 16.9706 4 12 4C7.02944 4 3 8.02944 3 13C3 17.9706 7.02944 22 12 22Z" fill="currentColor"/>
                                    <path d="M12 7V17M14.5 9.5C14.5 8.11929 13.3807 7 12 7H10C8.89543 7 8 7.89543 8 9C8 10.1046 8.89543 11 10 11H14C15.1046 11 16 11.8954 16 13C16 14.1046 15.1046 15 14 15H12M12 17H9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-dark">Harga Jual & Batas Stok</h3>
                                <p class="text-[11px] text-gray-400">Harga satuan kasir dan batas peringatan stok habis</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            {{-- Selling Price --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">
                                    Harga Jual (per satuan jual) <span class="text-red-500">*</span>
                                </label>
                                <div class="input-group-solid">
                                    <span class="input-prefix font-bold text-gray-500">Rp</span>
                                    <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" min="0" required placeholder="0"
                                           class="form-input-solid font-semibold">
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">Harga yang dibebankan kepada pelanggan kasir</p>
                                @error('selling_price') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Min Stock --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">
                                    Batas Minimum Stok
                                </label>
                                <div class="input-group-solid">
                                    <span class="input-prefix">
                                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M12 22C12 22 20 18 20 12V5L12 2L4 5V12C4 18 12 22 12 22Z" fill="currentColor"/>
                                            <path d="M12 8V13M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 22C12 22 20 18 20 12V5L12 2L4 5V12C4 18 12 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" placeholder="0"
                                           class="form-input-solid">
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">Muncul notifikasi bila stok berada di bawah angka ini</p>
                                @error('min_stock') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Submit Button (Visible only on mobile/tablet) --}}
                    <div class="lg:hidden pt-2">
                        <button type="submit" class="btn-primary w-full py-3.5 font-bold rounded-xl shadow-lg transition-transform active:scale-[0.98] text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Perbarui Produk</span>
                        </button>
                    </div>
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
        </form>
    </div>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productEditForm', () => ({
        imagePreview: '{{ $product->image ? asset("storage/" . $product->image) : "" }}',
        galleryFilepath: '{{ $product->image ?? "" }}',
        galleryModalOpen: false,
        galleryImages: [],
        searchQuery: '',
        isLoading: false,
        isActive: {{ $product->is_active ? 'true' : 'false' }},
        categoryId: '{{ $product->category_id }}',

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


<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Galeri Media</h2>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-5" x-data="{ showUsageModal: false, activeUsages: [], activeFilename: '', previewOpen: false, previewUrl: '', previewFilename: '' }">


        {{-- Upload Section --}}
        <div class="glass-card p-4">
            <form action="{{ route('galleries.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                <div x-data="{ 
                        dragging: false, 
                        handleFile(file) {
                            if (!file) return;
                            window.cropImage(file, (croppedBlob) => {
                                const croppedFile = new File([croppedBlob], file.name, { type: file.type });
                                const dt = new DataTransfer();
                                dt.items.add(croppedFile);
                                this.$refs.fileInput.files = dt.files;
                                const form = document.getElementById('upload-form');
                                if (form) {
                                    (form.requestSubmit ? form.requestSubmit() : form.submit());
                                }
                            }, (originalFile) => {
                                const form = document.getElementById('upload-form');
                                if (form) {
                                    (form.requestSubmit ? form.requestSubmit() : form.submit());
                                }
                            }, () => {
                                this.$refs.fileInput.value = '';
                            });
                        }
                     }"
                     @dragover.prevent="dragging = true"
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
                     class="relative border-2 border-dashed rounded-xl p-5 flex flex-col items-center justify-center transition-all duration-200"
                     :class="dragging ? 'border-primary-500 bg-primary-50/20' : 'border-gray-200 hover:border-primary-400 bg-white/40'">
                     
                     <input type="file" name="image" x-ref="fileInput" accept="image/*" class="hidden"
                            @change="handleFile($event.target.files[0])">
                            
                     <div class="text-center cursor-pointer py-3 w-full" @click="$refs.fileInput.click()">
                         <svg class="mx-auto h-10 w-10 text-primary-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                         </svg>
                         <span class="block text-xs font-semibold text-gray-700">Unggah Gambar Baru</span>
                         <span class="block text-[10px] text-gray-400 mt-0.5">Tarik & Lepas gambar di sini, atau klik untuk memilih file</span>
                     </div>
                </div>
            </form>
        </div>

        {{-- Filter & Search --}}
        <div class="glass-card p-4 space-y-3">
            <form action="{{ route('galleries.index') }}" method="GET" class="space-y-3">
                {{-- Search Bar --}}
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari nama file gambar..." 
                           class="form-input-glass pl-9 text-xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Status Tabs --}}
                <div class="flex bg-gray-100/80 p-0.5 rounded-lg text-[11px] font-semibold">
                    <a href="{{ route('galleries.index', ['filter' => '', 'search' => request('search')]) }}" 
                       class="flex-1 text-center py-1.5 rounded-md transition-all {{ request('filter') == '' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Semua
                    </a>
                    <a href="{{ route('galleries.index', ['filter' => 'used', 'search' => request('search')]) }}" 
                       class="flex-1 text-center py-1.5 rounded-md transition-all {{ request('filter') == 'used' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Digunakan
                    </a>
                    <a href="{{ route('galleries.index', ['filter' => 'unused', 'search' => request('search')]) }}" 
                       class="flex-1 text-center py-1.5 rounded-md transition-all {{ request('filter') == 'unused' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Belum Digunakan
                    </a>
                </div>
                
                @if(request('search') || request('filter'))
                    <div class="flex justify-end">
                        <a href="{{ route('galleries.index') }}" class="text-[10px] text-gray-400 hover:text-gray-600 underline">Reset Filter</a>
                    </div>
                @endif
            </form>
        </div>

        {{-- Gallery Grid --}}
        @if($galleries->isEmpty())
            <div class="glass-card p-12 text-center text-gray-400 text-xs">
                Tidak ada berkas gambar yang ditemukan.
            </div>
        @else
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                @foreach($galleries as $gallery)
                    <div class="aspect-square bg-gray-50 relative group rounded-xl overflow-hidden shadow-sm border border-gray-150">
                        {{-- Image Element --}}
                        <img src="{{ asset('storage/' . $gallery->filepath) }}" 
                             class="w-full h-full object-cover" 
                             alt="{{ $gallery->filename }}"
                             loading="lazy">

                        {{-- Center Eye Preview on Hover --}}
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-200 flex items-center justify-center cursor-pointer"
                             @click="previewUrl = '{{ asset('storage/' . $gallery->filepath) }}'; previewFilename = '{{ $gallery->filename }}'; previewOpen = true">
                            <div class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-200">
                                <div class="w-10 h-10 rounded-full bg-white/90 shadow-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Top-Right Checkmark (If Used) --}}
                        @if($gallery->is_used)
                            <div class="absolute top-1.5 right-1.5 z-10">
                                <button @click.stop="activeUsages = {{ json_encode($gallery->usages) }}; activeFilename = '{{ $gallery->filename }}'; showUsageModal = true" type="button" 
                                        title="Gambar ini sedang digunakan. Klik untuk detail."
                                        class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center shadow-md active:scale-90 transition-transform">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        {{-- Bottom Overlay --}}
                        <div @click.stop class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent p-1.5 pt-6 flex items-center justify-between">
                            <span class="text-[9px] font-medium text-white truncate max-w-[70%]" title="{{ $gallery->filename }}">
                                {{ $gallery->filename }}
                            </span>
                            
                            <div class="flex items-center gap-1">
                                {{-- Preview eye button (mobile friendly) --}}
                                <button type="button" 
                                        @click="previewUrl = '{{ asset('storage/' . $gallery->filepath) }}'; previewFilename = '{{ $gallery->filename }}'; previewOpen = true"
                                        class="text-white hover:text-blue-300 transition-colors active:scale-90 transform p-0.5" title="Preview">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                {{-- Delete action --}}
                                @if(!$gallery->is_used)
                                    <form action="{{ route('galleries.destroy', $gallery) }}" method="POST" 
                                          class="confirm-delete" data-confirm="Yakin ingin menghapus gambar ini dari galeri?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-white hover:text-red-400 transition-colors active:scale-90 transform p-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-gray-400 cursor-not-allowed p-0.5" title="Gambar ini terkunci karena sedang digunakan">🔒</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $galleries->links() }}
            </div>
        @endif

        <!-- Modal Detail Penggunaan Gambar -->
        <div x-show="showUsageModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
             <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                 <div class="fixed inset-0 transition-opacity bg-gray-500/75 backdrop-blur-sm" @click="showUsageModal = false"></div>
                 
                 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                 
                 <div class="inline-block align-bottom bg-white/95 backdrop-blur-xl border border-white/50 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-4 w-full">
                     <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                         <h3 class="text-sm font-bold text-dark">Rincian Penggunaan Berkas</h3>
                         <button type="button" @click="showUsageModal = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
                     </div>
                     
                     <div class="mt-3 space-y-3">
                         <p class="text-xs text-gray-500">
                             Berkas <span class="font-bold text-dark" x-text="activeFilename"></span> sedang digunakan pada:
                         </p>
                         
                         <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden bg-gray-50/50">
                             <template x-for="(usage, index) in activeUsages" :key="index">
                                 <div class="flex items-center justify-between p-3 text-xs">
                                     <span class="font-semibold text-gray-500" x-text="usage.type"></span>
                                     <a :href="usage.edit_url || usage.show_url" 
                                        class="text-primary-600 hover:text-primary-700 font-bold underline truncate max-w-[200px]"
                                        x-text="usage.name"
                                        title="Buka Detail">
                                     </a>
                                 </div>
                             </template>
                         </div>
                     </div>
                     
                     <div class="mt-5 flex justify-end">
                         <button type="button" @click="showUsageModal = false" 
                                 class="px-4 py-2 text-xs font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-colors shadow">
                             Tutup
                         </button>
                     </div>
                 </div>
             </div>
        </div>

        <!-- Lightbox Preview Modal -->
        <div x-show="previewOpen" 
             class="fixed inset-0 z-[60]"
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="previewOpen = false">
             
             <!-- Backdrop (fully opaque) -->
             <div class="absolute inset-0 bg-black" @click="previewOpen = false"></div>
             
             <!-- Content -->
             <div class="absolute inset-0 flex flex-col items-center justify-center p-4 pb-24 sm:p-10 sm:pb-10 pointer-events-none">
                 
                 <!-- Image wrapper -->
                 <div class="pointer-events-auto"
                      x-show="previewOpen"
                      x-transition:enter="transition ease-out duration-300 delay-100"
                      x-transition:enter-start="opacity-0 scale-95"
                      x-transition:enter-end="opacity-100 scale-100"
                      x-transition:leave="transition ease-in duration-150"
                      x-transition:leave-start="opacity-100 scale-100"
                      x-transition:leave-end="opacity-0 scale-95"
                      @click.stop>
                     <img :src="previewUrl" :alt="previewFilename" 
                          style="max-width: 90vw; max-height: 65vh; object-fit: contain;"
                          class="rounded-xl shadow-2xl block">
                 </div>

                 <!-- Bottom Bar -->
                 <div class="pointer-events-auto mt-3 flex items-center justify-between max-w-md w-full bg-white/15 backdrop-blur-xl rounded-full px-4 py-2.5 border border-white/10"
                      @click.stop>
                     <span class="text-white text-xs font-medium truncate max-w-[65%]" x-text="previewFilename"></span>
                     <button type="button" @click="previewOpen = false" 
                             class="ml-3 shrink-0 px-4 py-1.5 text-xs font-semibold text-white bg-white/20 hover:bg-white/30 rounded-full transition-colors">
                         ✕ Tutup
                     </button>
                 </div>
             </div>
        </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Galeri Media</h2>
        </div>
    </x-slot>

    <div class="py-5 pb-24 space-y-5" x-data="galleryManager()">

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
                <input type="hidden" name="filter" value="{{ request('filter') }}">

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

                {{-- Label Filter dropdown --}}
                <div class="relative">
                    <select name="label" class="form-input-glass text-xs" onchange="this.form.submit()">
                        <option value="">-- Semua Label --</option>
                        @foreach($allLabels as $lbl)
                            <option value="{{ $lbl->name }}" {{ request('label') == $lbl->name ? 'selected' : '' }}>
                                Label: {{ $lbl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Tabs --}}
                <div class="flex bg-gray-100/80 p-0.5 rounded-lg text-[11px] font-semibold">
                    <a href="{{ route('galleries.index', ['filter' => '', 'search' => request('search'), 'label' => request('label')]) }}" 
                       class="flex-1 text-center py-1.5 rounded-md transition-all {{ request('filter') == '' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Semua
                    </a>
                    <a href="{{ route('galleries.index', ['filter' => 'used', 'search' => request('search'), 'label' => request('label')]) }}" 
                       class="flex-1 text-center py-1.5 rounded-md transition-all {{ request('filter') == 'used' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Digunakan
                    </a>
                    <a href="{{ route('galleries.index', ['filter' => 'unused', 'search' => request('search'), 'label' => request('label')]) }}" 
                       class="flex-1 text-center py-1.5 rounded-md transition-all {{ request('filter') == 'unused' ? 'bg-white text-primary-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Belum Digunakan
                    </a>
                </div>
                
                @if(request('search') || request('filter') || request('label'))
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

                        {{-- Labels at Top-Left --}}
                        @if($gallery->labels->isNotEmpty())
                            <div class="absolute top-1.5 left-1.5 z-10 flex flex-wrap gap-1 max-w-[70%]">
                                @foreach($gallery->labels as $label)
                                    <span class="bg-black/60 text-white rounded px-1.5 py-0.5 text-[8px] font-semibold truncate max-w-[60px] block" title="{{ $label->name }}">
                                        {{ $label->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Center Eye Preview on Hover --}}
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-200 flex items-center justify-center cursor-pointer"
                             onclick="openGalleryPreview({{ $loop->index }})">
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
                                <button @click.stop="openUsageModal({{ json_encode($gallery->usages) }}, '{{ addslashes($gallery->filename) }}')" type="button" 
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
                            <span class="text-[9px] font-medium text-white truncate max-w-[55%]" title="{{ $gallery->filename }}">
                                {{ $gallery->filename }}
                            </span>
                            
                            <div class="flex items-center gap-1">
                                {{-- Edit label button --}}
                                <button type="button" 
                                        @click.stop="openLabelModal({{ $gallery->id }}, {{ json_encode($gallery->labels->pluck('name')) }}, '{{ addslashes($gallery->filename) }}')"
                                        class="text-white hover:text-yellow-300 transition-colors active:scale-90 transform p-0.5" title="Kelola Label">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.5 20.5L18 12l-6-6-8.5 8.5a2.12 2.12 0 000 3l3 3a2.12 2.12 0 003 0zM7 7h.01"/>
                                    </svg>
                                </button>

                                {{-- Preview eye button (mobile friendly) --}}
                                <button type="button" 
                                        onclick="event.stopPropagation(); openGalleryPreview({{ $loop->index }})"
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

        <!-- Modal Kelola Label -->
        <div x-show="showLabelModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
             <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                  <div class="fixed inset-0 transition-opacity bg-gray-500/75 backdrop-blur-sm" @click="showLabelModal = false"></div>
                  
                  <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                  
                  <div class="inline-block align-bottom bg-white/95 backdrop-blur-xl border border-white/50 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-4 w-full">
                      <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                          <h3 class="text-sm font-bold text-dark">Kelola Label Berkas</h3>
                          <button type="button" @click="showLabelModal = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
                      </div>
                      
                      <div class="mt-3 space-y-4">
                          <div>
                              <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold block mb-0.5">Nama Berkas</span>
                              <span class="text-xs font-semibold text-gray-700 truncate block" x-text="activeFilename"></span>
                          </div>

                          {{-- Input Form --}}
                          <div class="space-y-1.5">
                              <label class="block text-[11px] font-semibold text-gray-500">Tambah Label Baru</label>
                              <div class="flex gap-2">
                                  <input type="text" x-model="newLabelInput" @keydown.enter.prevent="addNewLabel()"
                                         placeholder="Tulis nama label..." 
                                         class="form-input-glass text-xs py-2 flex-1">
                                  <button type="button" @click="addNewLabel()"
                                          class="px-3 py-2 text-xs font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-colors shadow shrink-0">
                                      Tambah
                                  </button>
                              </div>
                              
                              {{-- Suggestions / Popular tags --}}
                              @if($allLabels->isNotEmpty())
                                  <div class="mt-2 flex flex-wrap gap-1.5 items-center">
                                      <span class="text-[10px] text-gray-400 font-medium shrink-0">Saran:</span>
                                      @foreach($allLabels->take(6) as $popLabel)
                                          <button type="button" 
                                                  @click="addPopularLabel('{{ addslashes($popLabel->name) }}')"
                                                  class="bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 text-[9px] font-semibold transition-colors">
                                              + {{ $popLabel->name }}
                                          </button>
                                      @endforeach
                                  </div>
                              @endif
                          </div>

                          {{-- Attached Labels --}}
                          <div class="space-y-1.5">
                              <label class="block text-[11px] font-semibold text-gray-500">Label Terpasang</label>
                              <div class="flex flex-wrap gap-1.5 min-h-[50px] p-2 bg-gray-50 border border-gray-100 rounded-xl">
                                  <template x-if="activeGalleryLabels.length === 0">
                                      <span class="text-[10px] text-gray-400 italic m-auto">Belum ada label terpasang.</span>
                                  </template>
                                  <template x-for="(label, idx) in activeGalleryLabels" :key="idx">
                                      <span class="inline-flex items-center gap-1.5 bg-primary-50 text-primary-700 border border-primary-200 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                          <span x-text="label"></span>
                                          <button type="button" @click="removeLabel(idx)" class="text-primary-400 hover:text-primary-600 font-bold focus:outline-none">&times;</button>
                                      </span>
                                  </template>
                              </div>
                          </div>
                      </div>
                      
                      <div class="mt-6 flex justify-end gap-2">
                          <button type="button" @click="showLabelModal = false" 
                                  class="px-4 py-2 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-xl hover:bg-gray-200 transition-colors">
                              Batal
                          </button>
                          <button type="button" @click="saveLabels()" :disabled="isSavingLabels"
                                  class="px-4 py-2 text-xs font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-colors shadow disabled:opacity-50">
                              <span x-show="!isSavingLabels">Simpan Perubahan</span>
                              <span x-show="isSavingLabels">Menyimpan...</span>
                          </button>
                      </div>
                  </div>
             </div>
        </div>

    </div>
</x-app-layout>

<script>
// Build array of galleries for lightbox navigation
const galleryItems = [
    @foreach($galleries as $gallery)
        {
            id: {{ $gallery->id }},
            url: '{{ asset('storage/' . $gallery->filepath) }}',
            filename: '{{ addslashes($gallery->filename) }}',
            labels: {!! json_encode($gallery->labels->pluck('name')) !!}
        },
    @endforeach
];

// Alpine gallery manager component
document.addEventListener('alpine:init', () => {
    Alpine.data('galleryManager', () => ({
        showUsageModal: false,
        activeUsages: [],
        activeFilename: '',

        // Label management state
        showLabelModal: false,
        activeGalleryId: null,
        activeGalleryLabels: [],
        newLabelInput: '',
        isSavingLabels: false,

        openUsageModal(usages, filename) {
            this.activeUsages = usages;
            this.activeFilename = filename;
            this.showUsageModal = true;
        },

        openLabelModal(id, labels, filename) {
            this.activeGalleryId = id;
            this.activeGalleryLabels = [...labels]; // Clone array
            this.activeFilename = filename;
            this.newLabelInput = '';
            this.showLabelModal = true;
        },

        addNewLabel() {
            const val = this.newLabelInput.trim();
            if (val && !this.activeGalleryLabels.includes(val)) {
                this.activeGalleryLabels.push(val);
            }
            this.newLabelInput = '';
        },

        addPopularLabel(name) {
            if (!this.activeGalleryLabels.includes(name)) {
                this.activeGalleryLabels.push(name);
            }
        },

        removeLabel(index) {
            this.activeGalleryLabels.splice(index, 1);
        },

        saveLabels() {
            this.isSavingLabels = true;
            fetch(`/galleries/${this.activeGalleryId}/labels`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ labels: this.activeGalleryLabels })
            })
            .then(res => res.json())
            .then(data => {
                this.isSavingLabels = false;
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal menyimpan label.');
                }
            })
            .catch(err => {
                this.isSavingLabels = false;
                console.error(err);
                alert('Terjadi kesalahan saat menyimpan label.');
            });
        }
    }));
});

let currentPreviewIndex = -1;

function openGalleryPreview(index) {
    currentPreviewIndex = index;
    const item = galleryItems[index];
    if (!item) return;

    // Remove existing lightbox if any
    const existing = document.getElementById('gallery-lightbox');
    if (existing) existing.remove();

    // Create lightbox
    const lightbox = document.createElement('div');
    lightbox.id = 'gallery-lightbox';
    lightbox.style.cssText = 'position:fixed;inset:0;z-index:99999;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:16px;padding-bottom:80px;animation:lbFadeIn .25s ease;';
    
    // Determine label pills html
    const labelPills = item.labels.map(l => `<span style="background:rgba(22,163,74,0.3);color:#fff;font-size:9px;padding:2px 8px;border-radius:999px;font-weight:600;margin-right:4px;">${l}</span>`).join('');

    lightbox.innerHTML = `
        <!-- Backdrop -->
        <div style="position:absolute;inset:0;background:#000;" onclick="closeGalleryPreview()"></div>
        
        <!-- Close button -->
        <button onclick="closeGalleryPreview()" style="position:absolute;top:16px;right:16px;z-index:10;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,0.25);border:2px solid rgba(255,255,255,0.4);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:22px;font-weight:bold;line-height:1;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.25)'">
            ✕
        </button>
        
        <!-- Prev button -->
        <button onclick="navigateGalleryPreview(-1)" style="position:absolute;left:16px;z-index:10;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,0.25);border:2px solid rgba(255,255,255,0.4);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;font-weight:bold;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.25)'">
            ‹
        </button>

        <!-- Next button -->
        <button onclick="navigateGalleryPreview(1)" style="position:absolute;right:16px;z-index:10;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,0.25);border:2px solid rgba(255,255,255,0.4);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;font-weight:bold;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.25)'">
            ›
        </button>

        <img src="${item.url}" alt="${item.filename}" style="max-width:92vw;max-height:70vh;object-fit:contain;border-radius:12px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);position:relative;z-index:1;animation:lbScaleIn .3s ease .1s both;" onclick="event.stopPropagation()">
        
        <div style="position:relative;z-index:1;margin-top:12px;display:flex;flex-direction:column;align-items:center;gap:6px;background:rgba(255,255,255,0.15);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-radius:16px;padding:10px 20px;border:1px solid rgba(255,255,255,0.1);max-width:80%;animation:lbScaleIn .3s ease .15s both;">
            <span style="color:#fff;font-size:12px;font-weight:600;word-break:break-all;text-align:center;">${item.filename}</span>
            ${labelPills ? `<div style="display:flex;flex-wrap:wrap;justify-content:center;gap:4px;margin-top:2px;">${labelPills}</div>` : ''}
        </div>
    `;
    document.body.appendChild(lightbox);

    // Remove existing keydown listener to avoid duplication
    if (window._galleryEscHandler) {
        document.removeEventListener('keydown', window._galleryEscHandler);
    }

    window._galleryEscHandler = function(e) {
        if (e.key === 'Escape') {
            closeGalleryPreview();
        } else if (e.key === 'ArrowRight') {
            navigateGalleryPreview(1);
        } else if (e.key === 'ArrowLeft') {
            navigateGalleryPreview(-1);
        }
    };
    document.addEventListener('keydown', window._galleryEscHandler);
}

function navigateGalleryPreview(direction) {
    if (galleryItems.length <= 1) return;
    let newIndex = currentPreviewIndex + direction;
    if (newIndex >= galleryItems.length) newIndex = 0;
    if (newIndex < 0) newIndex = galleryItems.length - 1;
    openGalleryPreview(newIndex);
}

function closeGalleryPreview() {
    const lightbox = document.getElementById('gallery-lightbox');
    if (lightbox) {
        if (window._galleryEscHandler) {
            document.removeEventListener('keydown', window._galleryEscHandler);
            window._galleryEscHandler = null;
        }
        lightbox.style.animation = 'lbFadeOut .2s ease forwards';
        setTimeout(() => lightbox.remove(), 200);
    }
}

// Add keyframe animations
if (!document.getElementById('gallery-lightbox-styles')) {
    const style = document.createElement('style');
    style.id = 'gallery-lightbox-styles';
    style.textContent = `
        @keyframes lbFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes lbFadeOut { from { opacity: 1; } to { opacity: 0; } }
        @keyframes lbScaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    `;
    document.head.appendChild(style);
}
</script>


<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-dark">Galeri Media</h2>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-5">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="glass-card-solid p-3 border-l-4 border-primary-500 text-xs text-dark">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="glass-card-solid p-3 border-l-4 border-red-500 text-xs text-dark">
                {{ session('error') }}
            </div>
        @endif

        {{-- Upload Section --}}
        <div class="glass-card p-4">
            <form action="{{ route('galleries.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                <div x-data="{ 
                        dragging: false, 
                        handleFile(file) {
                            if (!file) return;
                            document.getElementById('upload-form').submit();
                        }
                     }"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; if ($event.dataTransfer.files.length) { $refs.fileInput.files = $event.dataTransfer.files; handleFile($event.dataTransfer.files[0]); }"
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
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($galleries as $gallery)
                    <div class="glass-card overflow-hidden flex flex-col justify-between" x-data="{ showUsages: false }">
                        <div>
                            {{-- Image Preview --}}
                            <div class="aspect-square bg-gray-100 relative group overflow-hidden">
                                <img src="{{ asset('storage/' . $gallery->filepath) }}" 
                                     class="w-full h-full object-cover" 
                                     alt="{{ $gallery->filename }}">
                                
                                {{-- Hover zoom/detail link --}}
                                <a href="{{ asset('storage/' . $gallery->filepath) }}" target="_blank" 
                                   class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-200">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </div>

                            {{-- Image Meta --}}
                            <div class="p-2 space-y-1.5">
                                <div class="truncate text-[10px] font-bold text-dark" title="{{ $gallery->filename }}">
                                    {{ $gallery->filename }}
                                </div>
                                <div class="text-[9px] text-gray-400">
                                    {{ round($gallery->file_size / 1024, 1) }} KB
                                </div>

                                {{-- Usage Badge --}}
                                <div>
                                    @if($gallery->is_used)
                                        <button type="button" @click="showUsages = !showUsages" 
                                                class="w-full flex items-center justify-between text-[9px] px-1.5 py-0.5 rounded bg-green-50 hover:bg-green-100 text-green-700 font-bold border border-green-200 transition-colors">
                                            <span>✔️ Digunakan ({{ count($gallery->usages) }})</span>
                                            <svg class="h-3 w-3 transition-transform" :class="showUsages ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    @else
                                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 font-bold border border-gray-200">
                                            ⚠️ Belum Digunakan
                                        </span>
                                    @endif
                                </div>

                                {{-- Usage List --}}
                                <div x-show="showUsages" x-collapse class="pt-1.5 space-y-1 border-t border-dashed border-gray-100 text-[9px] text-gray-500">
                                    @foreach($gallery->usages as $usage)
                                        <div class="flex items-center justify-between">
                                            <span>{{ $usage['type'] }}:</span>
                                            @if($usage['edit_url'])
                                                <a href="{{ $usage['edit_url'] }}" class="text-primary-600 hover:text-primary-700 font-semibold underline truncate max-w-[80px]" title="Klik untuk edit">
                                                    {{ $usage['name'] }}
                                                </a>
                                            @else
                                                <a href="{{ $usage['show_url'] }}" class="text-primary-600 hover:text-primary-700 font-semibold underline truncate max-w-[80px]" title="Klik untuk detail">
                                                    {{ $usage['name'] }}
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="p-2 pt-0 border-t border-gray-50 flex justify-end">
                            @if($gallery->is_used)
                                <button type="button" disabled 
                                        title="Gambar ini sedang digunakan dan tidak dapat dihapus."
                                        class="text-[9px] text-gray-300 font-medium cursor-not-allowed">
                                    Hapus
                                </button>
                            @else
                                <form action="{{ route('galleries.destroy', $gallery) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus gambar ini dari galeri?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[9px] text-red-500 hover:text-red-700 font-semibold">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $galleries->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

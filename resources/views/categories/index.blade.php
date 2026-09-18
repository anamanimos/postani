<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-dark">Kategori Produk</h2>
                <p class="text-xs text-gray-400 hidden sm:block">Kelompokkan produk untuk memudahkan pencarian di kasir</p>
            </div>
            <span class="text-xs font-semibold text-primary-700 bg-primary-50 border border-primary-200 px-3 py-1 rounded-full">
                {{ $categories->count() }} Kategori
            </span>
        </div>
    </x-slot>

    <div class="py-4 pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- LEFT COLUMN: Form Tambah Kategori (Sticky on Desktop) --}}
            <div class="lg:col-span-5 lg:sticky lg:top-20">
                <div class="card-solid p-5">
                    <h3 class="text-sm font-bold text-dark mb-3 pb-2 border-b border-gray-150">Tambah Kategori Baru</h3>
                    <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-xs font-semibold text-gray-500 mb-1">Nama Kategori</label>
                            <div class="input-group-solid">
                                <span class="input-prefix">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M12 2l9 9-9 9-9-9 9-9z" fill="currentColor"/>
                                        <path d="M12 2l9 9-9 9-9-9 9-9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="12" cy="7" r="1" fill="currentColor"/>
                                    </svg>
                                </span>
                                <input type="text" name="name" id="name" required placeholder="Contoh: Pupuk, Pestisida, Benih..."
                                       class="form-input-solid">
                            </div>
                        </div>
                        <div>
                            <label for="description" class="block text-xs font-semibold text-gray-500 mb-1">Deskripsi (Opsional)</label>
                            <div class="input-group-solid">
                                <span class="input-prefix">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" fill="currentColor"/>
                                        <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <textarea name="description" id="description" rows="2" placeholder="Deskripsi singkat kategori..."
                                          class="form-input-solid"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full py-2.5 font-bold rounded-xl shadow-xs active:scale-[0.98] transition-transform">
                            + Simpan Kategori
                        </button>
                    </form>
                </div>
            </div>

            {{-- RIGHT COLUMN: Daftar Kategori --}}
            <div class="lg:col-span-7">
                <div class="card-solid overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-150 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-dark">Daftar Kategori Terdaftar</h3>
                        <span class="text-xs text-gray-400 font-medium">Total: {{ $categories->count() }}</span>
                    </div>
                    <div class="divide-y divide-gray-150">
                        @forelse($categories as $category)
                        <div class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                            <div class="space-y-0.5">
                                <p class="text-sm font-bold text-dark">{{ $category->name }}</p>
                                @if($category->description)
                                    <p class="text-xs text-gray-400 line-clamp-1">{{ $category->description }}</p>
                                @else
                                    <p class="text-[11px] text-gray-300 italic">Tidak ada deskripsi</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <a href="{{ route('categories.edit', $category) }}" 
                                   class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 text-gray-600 transition-colors"
                                   title="Edit Kategori">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="confirm-delete" data-confirm="Yakin ingin menghapus kategori '{{ $category->name }}'? Produk yang terkait mungkin terpengaruh.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors"
                                            title="Hapus Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center text-gray-400 text-sm">
                            Belum ada kategori yang ditambahkan.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

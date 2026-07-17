<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Kategori Produk</h2>
    </x-slot>

    <div class="py-5 pb-24 space-y-6 max-w-lg mx-auto">
        {{-- Form Tambah Kategori --}}
        <div class="card-solid p-4">
            <h3 class="text-sm font-semibold text-dark mb-3">Tambah Kategori Baru</h3>
            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 mb-1">Nama Kategori</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Tag -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M12 2l9 9-9 9-9-9 9-9z" fill="currentColor"/>
                                <path d="M12 2l9 9-9 9-9-9 9-9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="7" r="1" fill="currentColor"/>
                            </svg>
                        </span>
                        <input type="text" name="name" id="name" required placeholder="Contoh: Pupuk, Pestisida..."
                               class="form-input-solid">
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-xs font-semibold text-gray-500 mb-1">Deskripsi</label>
                    <div class="input-group-solid">
                        <span class="input-prefix">
                            <!-- Duotone Icon: Message/Chat -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" fill="currentColor"/>
                                <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <textarea name="description" id="description" rows="2" placeholder="Deskripsi singkat..."
                                  class="form-input-solid"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full py-3 font-bold rounded-lg transition-transform active:scale-[0.98]">Simpan Kategori</button>
            </form>
        </div>

        {{-- Daftar Kategori --}}
        <div class="card-solid overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-150">
                <h3 class="text-sm font-semibold text-dark">Daftar Kategori</h3>
            </div>
            <div class="divide-y divide-gray-150">
                @forelse($categories as $category)
                <div class="px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-dark">{{ $category->name }}</p>
                        @if($category->description)
                            <p class="text-xs text-gray-400 line-clamp-1">{{ $category->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('categories.edit', $category) }}" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
                            ✏️
                        </a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="confirm-delete" data-confirm="Hapus kategori ini?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors">
                                🗑️
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-400 text-sm">
                    Belum ada kategori.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>


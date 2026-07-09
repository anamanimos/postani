<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Kategori Produk</h2>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-6">
        {{-- Form Tambah Kategori --}}
        <div class="glass-card p-4">
            <h3 class="text-sm font-semibold text-dark mb-3">Tambah Kategori Baru</h3>
            <form action="{{ route('categories.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 mb-1">Nama Kategori</label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: Pupuk, Pestisida..."
                           class="form-input-glass">
                </div>
                <div>
                    <label for="description" class="block text-xs font-semibold text-gray-500 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="2" placeholder="Deskripsi singkat..."
                              class="form-input-glass"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full">Simpan Kategori</button>
            </form>
        </div>

        {{-- Daftar Kategori --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Daftar Kategori</h3>
            </div>
            <div class="divide-y divide-gray-100">
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
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
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

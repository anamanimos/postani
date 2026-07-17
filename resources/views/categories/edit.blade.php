<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('categories.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Edit Kategori</h2>
        </div>
    </x-slot>

    <div class="py-5 pb-24 max-w-lg mx-auto">
        <div class="card-solid p-4">
            <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
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
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required placeholder="Contoh: Pupuk, Pestisida..."
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
                        <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat..."
                                  class="form-input-solid">{{ old('description', $category->description) }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full py-3 font-bold rounded-lg transition-transform active:scale-[0.98]">Perbarui Kategori</button>
            </form>
        </div>
    </div>
</x-app-layout>


<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('units.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Edit Satuan</h2>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24">
        <div class="glass-card p-4">
            <form action="{{ route('units.update', $unit) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 mb-1">Nama Satuan</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $unit->name) }}" required
                           class="form-input-glass">
                </div>
                <div>
                    <label for="symbol" class="block text-xs font-semibold text-gray-500 mb-1">Simbol / Singkatan</label>
                    <input type="text" name="symbol" id="symbol" value="{{ old('symbol', $unit->symbol) }}" required
                           class="form-input-glass">
                </div>
                <button type="submit" class="btn-primary w-full">Perbarui Satuan</button>
            </form>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-bold text-dark">Pengaturan Aplikasi</h2>
    </x-slot>

    <div class="py-5 pb-24 space-y-4">
        <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Profil Toko --}}
            <div class="glass-card p-4 space-y-3">
                <h3 class="text-sm font-bold text-dark border-b border-gray-100 pb-2">🏢 Profil Toko</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Toko</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $settings->get('store_name', 'Toko Tani')) }}" required class="form-input-glass">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Alamat Toko</label>
                    <textarea name="store_address" rows="2" class="form-input-glass" placeholder="Alamat lengkap toko...">{{ old('store_address', $settings->get('store_address', '')) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">No. Telepon Toko</label>
                    <input type="text" name="store_phone" value="{{ old('store_phone', $settings->get('store_phone', '')) }}" class="form-input-glass">
                </div>
            </div>

            {{-- Aturan Transaksi --}}
            <div class="glass-card p-4 space-y-3">
                <h3 class="text-sm font-bold text-dark border-b border-gray-100 pb-2">⚙️ Aturan Bisnis</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Margin Minimum Produk (Rupiah)</label>
                    <input type="number" name="min_margin" value="{{ old('min_margin', $settings->get('min_margin', '1000')) }}" required class="form-input-glass">
                    <p class="text-[10px] text-gray-400 mt-1">Jika margin harga jual produk setelah pembelian stok baru berada di bawah nilai ini, sistem akan menaikkan harga jual secara otomatis agar margin tetap terjaga.</p>
                </div>
            </div>

            {{-- Template Struk --}}
            <div class="glass-card p-4 space-y-3">
                <h3 class="text-sm font-bold text-dark border-b border-gray-100 pb-2">📄 Struk Belanja</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan Kaki Struk (Receipt Footer)</label>
                    <textarea name="receipt_footer" rows="2" class="form-input-glass" placeholder="Terima kasih atas kunjungan Anda...">{{ old('receipt_footer', $settings->get('receipt_footer', '')) }}</textarea>
                </div>
            </div>

            {{-- Backup Telegram --}}
            <div class="glass-card p-4 space-y-3">
                <h3 class="text-sm font-bold text-dark border-b border-gray-100 pb-2">🤖 Telegram Backup (Database)</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Telegram Bot Token</label>
                    <input type="text" name="telegram_bot_token" value="{{ old('telegram_bot_token', $settings->get('telegram_bot_token', '')) }}" class="form-input-glass" placeholder="123456789:ABCdefGhIJKlmNoPQRsT...">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Telegram Chat ID</label>
                    <input type="text" name="telegram_chat_id" value="{{ old('telegram_chat_id', $settings->get('telegram_chat_id', '')) }}" class="form-input-glass" placeholder="-100123456789">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full py-3.5 text-base font-bold shadow-float">Simpan Pengaturan</button>
        </form>
    </div>
</x-app-layout>


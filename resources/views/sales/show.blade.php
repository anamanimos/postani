<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Detail Penjualan</h2>
        </div>
    </x-slot>

    <div class="py-5 pb-24 space-y-4">
        {{-- Invoice header --}}
        <div class="glass-card p-4 space-y-3" x-data="{ editingDate: false }">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 font-semibold uppercase">{{ $sale->invoice_number }}</span>
                @if($sale->payment_status === 'paid')
                    <span class="badge-paid">Lunas</span>
                @elseif($sale->payment_status === 'partial')
                    <span class="badge-partial">Sebagian</span>
                @else
                    <span class="badge-unpaid">Hutang</span>
                @endif
            </div>
            <h1 class="text-lg font-bold text-dark">Penjualan ke {{ $sale->customer->name ?? 'Walk-in (Umum)' }}</h1>
            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-gray-400">Tanggal Transaksi</p>
                        <button type="button" @click="editingDate = !editingDate" class="text-primary-600 font-bold hover:underline text-[11px] flex items-center gap-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span x-text="editingDate ? 'Batal' : 'Edit'"></span>
                        </button>
                    </div>
                    <p class="font-medium text-dark mt-0.5" x-show="!editingDate">
                        {{ $sale->sale_date->locale('id')->isoFormat('D MMMM Y, HH:mm') }}
                    </p>

                    <form action="{{ route('sales.update-date', $sale) }}" method="POST" x-show="editingDate" class="mt-2 space-y-2" style="display: none;">
                        @csrf
                        @method('PATCH')
                        <input type="datetime-local" name="sale_date" value="{{ $sale->sale_date->format('Y-m-d\TH:i') }}" required class="form-input-glass !text-xs !py-1.5 !px-2 w-full">
                        <div class="flex gap-1">
                            <button type="submit" class="btn-primary py-1 px-3 text-[11px] font-bold">Simpan</button>
                            <button type="button" @click="editingDate = false" class="btn-secondary py-1 px-3 text-[11px]">Batal</button>
                        </div>
                    </form>
                </div>
                <div>
                    <p class="text-gray-400">Metode Bayar</p>
                    <p class="font-medium text-dark uppercase mt-0.5">{{ $sale->payment_method }}</p>
                </div>
            </div>
            <div class="pt-2 flex gap-2">
                <a href="{{ route('sales.receipt', $sale) }}" target="_blank"
                   class="btn-primary flex-1 py-2 text-xs font-semibold text-center">📄 Download / Cetak PDF Struk</a>
                <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi {{ $sale->invoice_number }}? Stok produk akan dikembalikan secara otomatis.');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-xs px-3 py-2 rounded-xl font-bold transition-all shadow-sm active:scale-95 flex items-center gap-1" title="Hapus Penjualan">
                        🗑️ Hapus
                    </button>
                </form>
            </div>
        </div>

        {{-- Sale items --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Item Belanja</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($sale->saleItems as $item)
                <div class="px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-dark">{{ $item->product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $item->quantity }} {{ $item->product->sellUnit->symbol }} @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                    </div>
                    <span class="text-sm font-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="bg-gray-50 px-4 py-3 space-y-2 border-t border-gray-100">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Total Belanja:</span>
                    <span class="font-bold text-dark">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Jumlah Dibayar:</span>
                    <span class="font-bold text-green-600">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
                </div>
                @if($sale->due_amount > 0)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-red-500 font-medium">Sisa Piutang:</span>
                    <span class="font-bold text-red-600">Rp {{ number_format($sale->due_amount, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Payment installments history --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Riwayat Cicilan Pelanggan</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($sale->customerPayments as $payment)
                <div class="px-4 py-3 flex items-center justify-between text-xs">
                    <div>
                        <p class="font-semibold text-dark">Pembayaran - {{ ucfirst($payment->payment_method) }}</p>
                        <p class="text-gray-400">{{ $payment->payment_date->locale('id')->isoFormat('D MMM Y') }} · {{ $payment->creator->name }}</p>
                        @if($payment->notes)
                            <p class="text-gray-400 italic mt-0.5">Note: {{ $payment->notes }}</p>
                        @endif
                    </div>
                    <span class="font-bold text-green-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="p-4 text-center text-gray-400 text-xs">
                    Belum ada pembayaran cicilan.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Add payment if unpaid --}}
        @if($sale->due_amount > 0)
        <div class="glass-card p-4">
            <h3 class="text-sm font-semibold text-dark mb-3">Terima Pembayaran / Cicilan</h3>
            <form action="{{ route('payments.customers.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Bayar (Rp)</label>
                    <input type="number" name="amount" max="{{ $sale->due_amount }}" required
                           value="{{ $sale->due_amount }}" class="form-input-glass">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Metode</label>
                        <select name="payment_method" required class="form-input-glass">
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal</label>
                        <input type="text" name="payment_date" required value="{{ date('Y-m-d') }}"
                               class="datepicker form-input-glass">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan</label>
                    <input type="text" name="notes" placeholder="Catatan pembayaran..." class="form-input-glass">
                </div>
                <button type="submit" class="btn-accent w-full font-bold">Simpan Cicilan</button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>


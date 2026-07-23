<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="text-lg font-bold text-dark">Detail Pembelian</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('purchases.edit', $purchase) }}" 
                   class="px-3 py-1.5 text-xs font-bold text-primary-600 hover:text-primary-700 bg-white/80 hover:bg-white border border-gray-200 rounded-xl transition-all shadow-sm active:scale-95">
                    ✏️ Edit Nota
                </a>
                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pembelian {{ $purchase->invoice_number }}? Stok barang akan disesuaikan secara otomatis.');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-xs px-3 py-1.5 rounded-xl font-bold shadow-sm transition-all flex items-center gap-1 active:scale-95">
                        🗑️ Hapus
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-5 pb-24 space-y-4">
        {{-- Invoice header --}}
        <div class="glass-card p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 font-semibold uppercase">{{ $purchase->invoice_number }}</span>
                @if($purchase->payment_status === 'paid')
                    <span class="badge-paid">Lunas</span>
                @elseif($purchase->payment_status === 'partial')
                    <span class="badge-partial">Sebagian</span>
                @else
                    <span class="badge-unpaid">Belum Lunas</span>
                @endif
            </div>
            <h1 class="text-lg font-bold text-dark">Pembelian dari {{ $purchase->supplier->name }}</h1>
            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                <div>
                    <p class="text-gray-400">Tanggal Transaksi</p>
                    <p class="font-medium text-dark">{{ $purchase->purchase_date->locale('id')->isoFormat('D MMMM Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Diinput Oleh</p>
                    <p class="font-medium text-dark">{{ $purchase->creator->name }}</p>
                </div>
                @if($purchase->supplier_invoice_number)
                <div class="col-span-2 pt-1 border-t border-gray-100">
                    <p class="text-gray-400">Nomor Nota Tengkulak</p>
                    <p class="font-medium text-dark">{{ $purchase->supplier_invoice_number }}</p>
                </div>
                @endif
                @if($purchase->invoice_image)
                <div class="col-span-2 pt-2 border-t border-gray-100" x-data="{ imgPreviewOpen: false }">
                    <p class="text-gray-400 mb-1.5 font-semibold">Foto Nota Fisik</p>
                    
                    {{-- Clickable full-width thumbnail --}}
                    <div @click="imgPreviewOpen = true" 
                         class="relative w-full h-48 rounded-xl overflow-hidden border border-gray-200 shadow-sm active:scale-[0.99] transition-all cursor-pointer group bg-gray-50 flex items-center justify-center">
                        <img src="{{ asset('storage/' . $purchase->invoice_image) }}" alt="Foto Nota Fisik" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300">
                        {{-- Hover Overlay --}}
                        <div class="absolute inset-0 bg-black/35 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1">
                            🔍 Klik untuk Memperbesar
                        </div>
                    </div>

                    {{-- Alpine Lightbox Modal --}}
                    <div x-show="imgPreviewOpen" 
                         x-transition.opacity 
                         class="fixed inset-0 z-50 bg-black/90 backdrop-blur-sm flex items-center justify-center p-4"
                         @click="imgPreviewOpen = false"
                         @keydown.escape.window="imgPreviewOpen = false"
                         style="display: none;">
                        
                        {{-- Close button --}}
                        <button type="button" @click="imgPreviewOpen = false" 
                                class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-lg z-50">
                            ✕
                        </button>
                        
                        {{-- Full size Image --}}
                        <img src="{{ asset('storage/' . $purchase->invoice_image) }}" 
                             alt="Detail Nota Fisik" 
                             class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
                             @click.stop>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Purchase items --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Item Pembelian</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($purchase->purchaseItems as $item)
                <div class="px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-dark">{{ $item->product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $item->quantity }} {{ $item->product->buyUnit->symbol }} @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                    </div>
                    <span class="text-sm font-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="bg-gray-50 px-4 py-3 space-y-2 border-t border-gray-100">
                @if($purchase->additional_cost > 0)
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Subtotal Barang:</span>
                    <span class="font-semibold text-dark">Rp {{ number_format($purchase->purchaseItems->sum('subtotal'), 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Biaya Tambahan:</span>
                    <div class="text-right flex flex-col items-end">
                        <span class="font-semibold text-dark">Rp {{ number_format($purchase->additional_cost, 0, ',', '.') }}</span>
                        @if($purchase->additional_cost_notes)
                            <span class="text-[10px] text-gray-400 italic">({{ $purchase->additional_cost_notes }})</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 pt-1 border-t border-dashed border-gray-250">
                    <span>Total Pembelian:</span>
                    <span class="font-bold text-primary-600">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                </div>
                @else
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Total Belanja:</span>
                    <span class="font-bold text-dark">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Jumlah Dibayar:</span>
                    <span class="font-bold text-green-600">Rp {{ number_format($purchase->paid_amount, 0, ',', '.') }}</span>
                </div>
                @if($purchase->due_amount > 0)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-red-500 font-medium">Sisa Hutang:</span>
                    <span class="font-bold text-red-600">Rp {{ number_format($purchase->due_amount, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Payment installments history --}}
        <div class="glass-card overflow-hidden">
            <div class="px-4 py-3 border-b border-white/30">
                <h3 class="text-sm font-semibold text-dark">Riwayat Cicilan / Pembayaran</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($purchase->supplierPayments as $payment)
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
        @if($purchase->due_amount > 0)
        <div class="glass-card p-4">
            <h3 class="text-sm font-semibold text-dark mb-3">Cicil / Lunasi Hutang</h3>
            <form action="{{ route('payments.suppliers.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Bayar (Rp)</label>
                    <input type="number" name="amount" max="{{ $purchase->due_amount }}" required
                           value="{{ $purchase->due_amount }}" class="form-input-glass">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Metode</label>
                        <select name="payment_method" required class="form-input-glass">
                            <option value="cash">Tunai</option>
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


<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform hover:bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-dark">Detail Pembelian</h2>
                    <p class="text-xs text-gray-400 hidden sm:block">Informasi nota tengkulak dan item belanja</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('purchases.edit', $purchase) }}" 
                   class="px-3.5 py-2 text-xs font-bold text-primary-600 hover:text-primary-700 bg-white/90 hover:bg-white border border-gray-200 rounded-xl transition-all shadow-xs active:scale-95 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Nota</span>
                </a>
                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="inline confirm-delete" data-confirm="Apakah Anda yakin ingin menghapus data pembelian {{ $purchase->invoice_number }}? Stok barang akan disesuaikan secara otomatis.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-xs px-3.5 py-2 rounded-xl font-bold shadow-xs transition-all flex items-center gap-1.5 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-4 pb-24" x-data="{ imgPreviewOpen: false }">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-start">
            
            {{-- LEFT COLUMN: Nota Info, Foto Nota, Ringkasan, & Form Cicilan --}}
            <div class="lg:col-span-5 space-y-4">
                {{-- Invoice Header Card --}}
                <div class="card-solid p-5 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-150">
                        <span class="text-xs text-gray-500 font-mono font-bold tracking-wider uppercase">{{ $purchase->invoice_number }}</span>
                        @if($purchase->payment_status === 'paid')
                            <span class="badge-paid">Lunas</span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="badge-partial">Sebagian</span>
                        @else
                            <span class="badge-unpaid">Belum Lunas</span>
                        @endif
                    </div>

                    <div>
                        <span class="text-[10px] text-gray-400 uppercase font-semibold">Tengkulak / Supplier</span>
                        <h1 class="text-lg font-black text-dark">{{ $purchase->supplier->name ?? 'Tengkulak' }}</h1>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs text-gray-500 pt-2 border-t border-gray-150">
                        <div>
                            <p class="text-[11px] text-gray-400">Tanggal Transaksi</p>
                            <p class="font-bold text-dark mt-0.5">{{ $purchase->purchase_date ? $purchase->purchase_date->locale('id')->isoFormat('D MMMM Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400">Diinput Oleh</p>
                            <p class="font-bold text-dark mt-0.5">{{ $purchase->creator->name ?? 'Admin' }}</p>
                        </div>
                        @if($purchase->supplier_invoice_number)
                        <div class="col-span-2 pt-1 border-t border-gray-100">
                            <p class="text-[11px] text-gray-400">Nomor Nota Fisik Tengkulak</p>
                            <p class="font-bold text-dark mt-0.5 font-mono">{{ $purchase->supplier_invoice_number }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Foto Nota Fisik --}}
                @if($purchase->invoice_image)
                <div class="card-solid p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-dark">Foto Nota Fisik</p>
                        <span class="text-[10px] text-gray-400">Klik untuk perbesar</span>
                    </div>
                    
                    {{-- Clickable thumbnail --}}
                    <div @click="imgPreviewOpen = true" 
                         class="relative w-full rounded-xl overflow-hidden border border-gray-200 shadow-2xs active:scale-[0.99] transition-all cursor-pointer group bg-gray-50 flex items-center justify-center"
                         style="height: 240px;">
                        <img src="{{ asset('storage/' . $purchase->invoice_image) }}" 
                             alt="Foto Nota Fisik" 
                             class="w-full h-full object-contain p-1 group-hover:scale-105 transition-transform duration-300">
                        {{-- Hover Overlay --}}
                        <div class="absolute inset-0 bg-black/35 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5 pointer-events-none">
                            <span class="bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                <span>Perbesar Nota</span>
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Ringkasan Pembayaran Card --}}
                <div class="card-solid p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 pb-2 border-b border-gray-150">Ringkasan Pembayaran</h3>
                    
                    <div class="space-y-2 text-xs">
                        @if($purchase->additional_cost > 0)
                        <div class="flex items-center justify-between text-gray-500">
                            <span>Subtotal Barang:</span>
                            <span class="font-bold text-dark">Rp {{ number_format($purchase->purchaseItems->sum('subtotal'), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-500">
                            <span>Biaya Tambahan:</span>
                            <div class="text-right flex flex-col items-end">
                                <span class="font-bold text-dark">Rp {{ number_format($purchase->additional_cost, 0, ',', '.') }}</span>
                                @if($purchase->additional_cost_notes)
                                    <span class="text-[10px] text-gray-400 italic">({{ $purchase->additional_cost_notes }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-dashed border-gray-200">
                            <span class="font-semibold text-dark">Total Pembelian:</span>
                            <span class="text-lg font-black text-primary-600">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @else
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-dark">Total Pembelian:</span>
                            <span class="text-lg font-black text-primary-600">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between text-gray-500 pt-1">
                            <span>Jumlah Dibayar:</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($purchase->paid_amount, 0, ',', '.') }}</span>
                        </div>

                        @if($purchase->due_amount > 0)
                        <div class="flex items-center justify-between pt-2 border-t border-gray-150 bg-red-50/60 p-2.5 rounded-xl text-red-700">
                            <span class="font-bold">Sisa Hutang:</span>
                            <span class="text-base font-black text-red-600">Rp {{ number_format($purchase->due_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Add payment if unpaid --}}
                @if($purchase->due_amount > 0)
                <div class="card-solid p-5">
                    <h3 class="text-sm font-bold text-dark mb-3">Cicil / Lunasi Hutang</h3>
                    <form action="{{ route('payments.suppliers.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Bayar (Rp)</label>
                            <input type="number" name="amount" max="{{ $purchase->due_amount }}" required
                                   value="{{ $purchase->due_amount }}" class="form-input-solid w-full">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Metode</label>
                                <select name="payment_method" required class="form-input-solid w-full">
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal</label>
                                <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}"
                                       class="form-input-solid w-full">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan</label>
                            <input type="text" name="notes" placeholder="Catatan pembayaran..." class="form-input-solid w-full">
                        </div>
                        <button type="submit" class="btn-accent w-full py-2.5 font-bold shadow-sm active:scale-95 transition-transform">Simpan Pembayaran</button>
                    </form>
                </div>
                @endif
            </div>

            {{-- RIGHT COLUMN: Item Pembelian & Riwayat Cicilan --}}
            <div class="lg:col-span-7 space-y-5">
                {{-- Purchase items --}}
                <div class="card-solid overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-150 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-dark">Item Pembelian</h3>
                        <span class="text-xs text-gray-500 font-medium">{{ $purchase->purchaseItems->count() }} item barang</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($purchase->purchaseItems as $item)
                        <div class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                            <div>
                                <p class="text-sm font-bold text-dark">{{ $item->product->name ?? 'Produk' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->quantity }} {{ $item->product->buyUnit->symbol ?? '' }} @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            </div>
                            <span class="text-sm font-black text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Payment installments history --}}
                <div class="card-solid overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-150 bg-gray-50/50">
                        <h3 class="text-sm font-bold text-dark">Riwayat Cicilan / Pembayaran</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($purchase->supplierPayments as $payment)
                        <div class="px-5 py-3.5 flex items-center justify-between text-xs hover:bg-gray-50/50 transition-colors">
                            <div>
                                <p class="font-bold text-dark">Pembayaran - {{ ucfirst($payment->payment_method) }}</p>
                                <p class="text-gray-400 text-[11px] mt-0.5">{{ $payment->payment_date->locale('id')->isoFormat('D MMM Y') }} · {{ $payment->creator->name ?? 'Admin' }}</p>
                                @if($payment->notes)
                                    <p class="text-gray-500 italic mt-0.5">Note: {{ $payment->notes }}</p>
                                @endif
                            </div>
                            <span class="font-black text-emerald-600 text-sm">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                        @empty
                        <div class="p-6 text-center text-gray-400 text-xs">
                            Belum ada riwayat pembayaran cicilan.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Lightbox Modal --}}
        @if($purchase->invoice_image)
        <div x-show="imgPreviewOpen" 
             x-transition.opacity 
             class="fixed inset-0 z-50 bg-black/90 backdrop-blur-sm flex items-center justify-center p-4"
             @click="imgPreviewOpen = false"
             @keydown.escape.window="imgPreviewOpen = false"
             style="display: none;">
            
            <button type="button" @click="imgPreviewOpen = false" 
                    class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-lg z-50">
                ✕
            </button>
            
            <img src="{{ asset('storage/' . $purchase->invoice_image) }}" 
                 alt="Detail Nota Fisik" 
                 class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl"
                 @click.stop>
        </div>
        @endif
    </div>
</x-app-layout>

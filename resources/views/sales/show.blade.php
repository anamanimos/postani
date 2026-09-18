<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform hover:bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-dark">Detail Penjualan</h2>
                    <p class="text-xs text-gray-400 hidden sm:block">Informasi transaksi kasir dan item belanja</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('sales.receipt', $sale) }}" target="_blank"
                   class="btn-primary py-2 px-3.5 sm:px-4 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-xs active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Struk</span>
                </a>
                <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline confirm-delete" data-confirm="Apakah Anda yakin ingin menghapus transaksi {{ $sale->invoice_number }}? Stok produk akan dikembalikan secara otomatis.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-xs px-3.5 py-2 rounded-xl font-bold transition-colors shadow-xs active:scale-95 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-4 pb-24" x-data="{ editingDate: false }">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-start">
            
            {{-- LEFT COLUMN: Header Info, Payment Summary, & Cicilan Form --}}
            <div class="lg:col-span-5 space-y-4">
                {{-- Transaction Info Card --}}
                <div class="card-solid p-5 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-150">
                        <span class="text-xs text-gray-500 font-mono font-bold tracking-wider uppercase">{{ $sale->invoice_number }}</span>
                        @if($sale->payment_status === 'paid')
                            <span class="badge-paid">Lunas</span>
                        @elseif($sale->payment_status === 'partial')
                            <span class="badge-partial">Sebagian</span>
                        @else
                            <span class="badge-unpaid">Hutang</span>
                        @endif
                    </div>

                    <div>
                        <span class="text-[10px] text-gray-400 uppercase font-semibold">Pelanggan</span>
                        <h1 class="text-lg font-black text-dark">{{ $sale->customer->name ?? 'Walk-in (Umum)' }}</h1>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs text-gray-500 pt-2 border-t border-gray-150">
                        <div>
                            <div class="flex items-center justify-between">
                                <p class="text-[11px] text-gray-400">Tanggal Transaksi</p>
                                <button type="button" @click="editingDate = !editingDate" class="text-primary-600 font-bold hover:underline text-[10px]">
                                    <span x-text="editingDate ? 'Batal' : 'Edit'"></span>
                                </button>
                            </div>
                            <p class="font-bold text-dark mt-0.5" x-show="!editingDate">
                                {{ $sale->sale_date->locale('id')->isoFormat('D MMMM Y, HH:mm') }}
                            </p>

                            <form action="{{ route('sales.update-date', $sale) }}" method="POST" x-show="editingDate" class="mt-2 space-y-2" style="display: none;">
                                @csrf
                                @method('PATCH')
                                <input type="datetime-local" name="sale_date" value="{{ $sale->sale_date->format('Y-m-d\TH:i') }}" required class="form-input-solid !text-xs !py-1 !px-2 w-full">
                                <div class="flex gap-1">
                                    <button type="submit" class="btn-primary py-1 px-2.5 text-[10px] font-bold">Simpan</button>
                                    <button type="button" @click="editingDate = false" class="btn-secondary py-1 px-2.5 text-[10px]">Batal</button>
                                </div>
                            </form>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400">Metode Bayar</p>
                            <p class="font-bold text-dark uppercase mt-0.5">{{ $sale->payment_method }}</p>
                        </div>
                    </div>
                </div>

                {{-- Payment Summary Card --}}
                <div class="card-solid p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 pb-2 border-b border-gray-150">Ringkasan Pembayaran</h3>
                    
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-dark">Total Belanja:</span>
                            <span class="text-lg font-black text-primary-600">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-500 pt-1">
                            <span>Jumlah Dibayar:</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
                        </div>

                        @if($sale->due_amount > 0)
                        <div class="flex items-center justify-between pt-2 border-t border-gray-150 bg-red-50/60 p-2.5 rounded-xl text-red-700">
                            <span class="font-bold">Sisa Piutang:</span>
                            <span class="text-base font-black text-red-600">Rp {{ number_format($sale->due_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Add payment if unpaid --}}
                @if($sale->due_amount > 0)
                <div class="card-solid p-5">
                    <h3 class="text-sm font-bold text-dark mb-3">Terima Pembayaran / Cicilan</h3>
                    <form action="{{ route('payments.customers.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Bayar (Rp)</label>
                            <input type="number" name="amount" max="{{ $sale->due_amount }}" required
                                   value="{{ $sale->due_amount }}" class="form-input-solid w-full">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Metode</label>
                                <select name="payment_method" required class="form-input-solid w-full">
                                    <option value="cash">Tunai</option>
                                    <option value="qris">QRIS</option>
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
                            <input type="text" name="notes" placeholder="Catatan cicilan..." class="form-input-solid w-full">
                        </div>
                        <button type="submit" class="btn-accent w-full py-2.5 font-bold shadow-sm active:scale-95 transition-transform">Simpan Pembayaran</button>
                    </form>
                </div>
                @endif
            </div>

            {{-- RIGHT COLUMN: Items Belanja & Customer Payments --}}
            <div class="lg:col-span-7 space-y-5">
                {{-- Sale items --}}
                <div class="card-solid overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-150 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-dark">Item Belanja</h3>
                        <span class="text-xs text-gray-500 font-medium">{{ $sale->saleItems->count() }} jenis barang</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($sale->saleItems as $item)
                        <div class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                            <div>
                                <p class="text-sm font-bold text-dark">{{ $item->product->name ?? 'Produk' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->quantity }} {{ $item->product->sellUnit->symbol ?? '' }} @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            </div>
                            <span class="text-sm font-black text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Payment installments history --}}
                <div class="card-solid overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-150 bg-gray-50/50">
                        <h3 class="text-sm font-bold text-dark">Riwayat Cicilan Pelanggan</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($sale->customerPayments as $payment)
                        <div class="px-5 py-3.5 flex items-center justify-between text-xs hover:bg-gray-50/50 transition-colors">
                            <div>
                                <p class="font-bold text-dark">Pembayaran - {{ ucfirst($payment->payment_method) }}</p>
                                <p class="text-gray-400 text-[11px] mt-0.5">{{ $payment->payment_date->locale('id')->isoFormat('D MMM Y') }} · {{ $payment->creator->name ?? 'Kasir' }}</p>
                                @if($payment->notes)
                                    <p class="text-gray-500 italic mt-0.5">Note: {{ $payment->notes }}</p>
                                @endif
                            </div>
                            <span class="font-black text-emerald-600 text-sm">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                        @empty
                        <div class="p-6 text-center text-gray-400 text-xs">
                            Belum ada riwayat pembayaran cicilan untuk transaksi ini.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

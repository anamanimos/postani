<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('purchases.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-bold text-dark">Detail Pembelian</h2>
        </div>
    </x-slot>

    <div class="px-4 py-5 pb-24 space-y-4">
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
                <div class="col-span-2 pt-1 border-t border-gray-100">
                    <p class="text-gray-400 mb-1">Foto Nota Fisik</p>
                    <a href="{{ asset('storage/' . $purchase->invoice_image) }}" target="_blank" class="block w-24 h-24 rounded-lg overflow-hidden border border-gray-200 shadow-sm active:scale-95 transition-transform">
                        <img src="{{ asset('storage/' . $purchase->invoice_image) }}" alt="Foto Nota" class="w-full h-full object-cover">
                    </a>
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
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Total Belanja:</span>
                    <span class="font-bold text-dark">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                </div>
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

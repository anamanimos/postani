<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('customers.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform hover:bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-dark">Detail Pelanggan</h2>
                    <p class="text-xs text-gray-400 hidden sm:block">Profil petani dan riwayat transaksi</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="btn-primary py-2 px-3.5 sm:px-4 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-xs active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Profil</span>
                </a>
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="confirm-delete hidden sm:inline-block" data-confirm="Yakin ingin menghapus pelanggan {{ $customer->name }}?">
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

    <div class="py-4 pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-start">
            
            {{-- LEFT COLUMN: Profile & Piutang Summary --}}
            <div class="lg:col-span-4 space-y-4">
                {{-- Profile Card --}}
                <div class="card-solid p-5 space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-150">
                        <div class="w-12 h-12 rounded-2xl bg-primary-100 text-primary-700 flex items-center justify-center font-black text-lg">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-bold uppercase">Pelanggan (Petani)</span>
                            <h1 class="text-base font-black text-dark leading-tight mt-0.5">{{ $customer->name }}</h1>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs">
                        @if($customer->phone)
                        <div>
                            <p class="text-[11px] text-gray-400 font-medium">Nomor Telepon / WhatsApp</p>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="font-bold text-primary-600 hover:underline flex items-center gap-1.5 mt-0.5">
                                <span>📞 {{ $customer->phone }}</span>
                                <span class="text-[10px] bg-primary-50 px-1.5 py-0.2 rounded border border-primary-200">Chat WA ➔</span>
                            </a>
                        </div>
                        @endif

                        @if($customer->address)
                        <div>
                            <p class="text-[11px] text-gray-400 font-medium">Alamat</p>
                            <p class="font-medium text-dark mt-0.5">📍 {{ $customer->address }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Piutang Summary Card --}}
                @php
                    $totalDue = $customer->sales->sum('due_amount');
                @endphp
                <div class="card-solid p-5 space-y-3 border-l-4 {{ $totalDue > 0 ? 'border-amber-500 bg-amber-50/20' : 'border-emerald-500 bg-emerald-50/20' }}">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-500">Total Piutang Belum Lunas</p>
                        @if($totalDue > 0)
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full">Ada Piutang</span>
                        @else
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full">Lunas</span>
                        @endif
                    </div>

                    <p class="text-2xl font-black {{ $totalDue > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                        Rp {{ number_format($totalDue, 0, ',', '.') }}
                    </p>

                    @if($totalDue > 0)
                        <a href="{{ route('payments.customers') }}" class="btn-accent w-full py-2.5 text-xs font-bold rounded-xl shadow-xs flex items-center justify-center gap-1.5">
                            <span>Terima Cicilan Piutang</span>
                            <span>➔</span>
                        </a>
                    @endif
                </div>

                {{-- Mobile Delete Button --}}
                <div class="sm:hidden pt-2">
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="confirm-delete" data-confirm="Yakin ingin menghapus pelanggan {{ $customer->name }}?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 border border-red-200 bg-red-50 text-red-600 font-bold rounded-xl text-xs hover:bg-red-100 active:scale-[0.98] transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Pelanggan Ini</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- RIGHT COLUMN: Sales History --}}
            <div class="lg:col-span-8 space-y-4">
                <div class="card-solid overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-150 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-dark">Riwayat Transaksi Belanja</h3>
                        <span class="text-xs text-gray-500 font-medium">{{ $sales->count() }} transaksi</span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse($sales as $sale)
                        <div class="p-4 hover:bg-gray-50/50 transition-colors space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <a href="{{ route('sales.show', $sale) }}" class="text-sm font-bold text-dark hover:text-primary-600 transition-colors">
                                        {{ $sale->invoice_number }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $sale->sale_date->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-dark">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                                    @if($sale->payment_status === 'paid')
                                        <span class="badge-paid">Lunas</span>
                                    @elseif($sale->payment_status === 'partial')
                                        <span class="badge-partial">Sebagian</span>
                                    @else
                                        <span class="badge-unpaid">Hutang</span>
                                    @endif
                                </div>
                            </div>
                            @if($sale->due_amount > 0)
                                <div class="flex items-center justify-between text-xs pt-2 border-t border-dashed border-gray-200 text-gray-500">
                                    <span>Sisa Hutang:</span>
                                    <span class="font-bold text-red-600">Rp {{ number_format($sale->due_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                        @empty
                        <div class="p-8 text-center text-gray-400 text-sm">
                            Belum ada riwayat transaksi dengan pelanggan ini.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

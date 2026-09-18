<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="w-10 h-10 rounded-full bg-white/60 border border-white/40 flex items-center justify-center active:scale-95 transition-transform hover:bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-dark">Detail Pengguna</h2>
                    <p class="text-xs text-gray-500">{{ $user->name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('users.edit', $user) }}" class="btn-primary py-2 px-4 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Ubah Pengguna</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-24 pt-1 max-w-lg mx-auto md:max-w-none">
        {{-- Responsive 12-Column Grid on Desktop --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-start">
            
            {{-- LEFT COLUMN: Profile & Account Information (4 cols on desktop) --}}
            <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-20">
                {{-- Profile Card --}}
                <div class="card-solid p-5 space-y-4 relative overflow-hidden bg-white">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 font-black text-xl shadow-inner {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h1 class="text-base font-extrabold text-dark">{{ $user->name }}</h1>
                                @if(auth()->id() === $user->id)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-primary-100 text-primary-700 font-extrabold">Akun Anda</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5 pt-0.5 flex-wrap">
                                @if($user->role === 'admin')
                                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                        👑 Administrator
                                    </span>
                                @else
                                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        🛒 Kasir
                                    </span>
                                @endif

                                @if($user->is_active)
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200">
                                        🟢 Aktif
                                    </span>
                                @else
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                        ⚪ Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5 pt-3 border-t border-gray-150 text-xs text-gray-600">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-gray-400">Email Login:</span>
                            <span class="font-semibold text-dark truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-gray-400">Telepon/WA:</span>
                            <span class="font-semibold text-dark">{{ $user->phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-gray-400">Terdaftar Sejak:</span>
                            <span class="font-medium text-dark">{{ $user->created_at ? $user->created_at->locale('id')->isoFormat('D MMMM Y') : '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action / Delete Note Card --}}
                <div class="card-solid p-4">
                    @if(auth()->id() === $user->id)
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-center text-xs text-amber-700 font-medium">
                            ℹ️ Anda sedang login dengan akun ini. Anda tidak dapat menghapus akun Anda sendiri.
                        </div>
                    @elseif($user->sales_count > 0 || $user->purchases_count > 0)
                        <div class="p-3.5 bg-blue-50/70 border border-blue-200 rounded-xl text-xs text-blue-800 space-y-1.5">
                            <p class="font-bold flex items-center gap-1.5 text-blue-900">
                                <span>💡</span>
                                <span>Keutuhan Data Transaksi</span>
                            </p>
                            <p class="leading-relaxed text-[11px] text-blue-700">
                                Pengguna ini mencatat {{ $user->sales_count + $user->purchases_count }} transaksi kasir. Akun tidak dapat dihapus permanen agar pembukuan tidak rusak. Ubah status menjadi <strong>Nonaktif</strong> jika kasir sudah tidak bekerja.
                            </p>
                        </div>
                    @else
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="confirm-delete" data-confirm="Apakah Anda yakin ingin menghapus akun '{{ $user->name }}'?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2.5 border border-red-200 text-red-600 hover:bg-red-50 font-bold rounded-xl text-xs transition-colors active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M6 7H18L17 21H7L6 7Z" fill="currentColor"/>
                                    <path d="M6 7H18L17 21H7L6 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 7H20M10 3H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span>Hapus Akun Pengguna</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- RIGHT COLUMN: Statistics & History Tabs (8 cols on desktop) --}}
            <div class="lg:col-span-8 space-y-4">
                {{-- Statistics Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Total Penjualan --}}
                    <div class="card-solid p-5 space-y-1.5 bg-white">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wide">Penjualan Kasir</span>
                            <span class="text-base p-1.5 bg-emerald-50 rounded-lg">🛒</span>
                        </div>
                        <p class="text-2xl font-black text-dark">Rp {{ number_format($totalSalesAmount ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400">{{ $user->sales_count }} Nota Penjualan Tercatat</p>
                    </div>

                    {{-- Total Pembelian --}}
                    <div class="card-solid p-5 space-y-1.5 bg-white">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-700 uppercase tracking-wide">Pembelian Kasir</span>
                            <span class="text-base p-1.5 bg-blue-50 rounded-lg">📦</span>
                        </div>
                        <p class="text-2xl font-black text-dark">Rp {{ number_format($totalPurchasesAmount ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400">{{ $user->purchases_count }} Nota Pembelian Kulak</p>
                    </div>
                </div>

                {{-- Recent Sales & Purchases Tab --}}
                <div class="card-solid overflow-hidden bg-white" x-data="{ tab: 'sales' }">
                    <div class="px-5 py-3.5 border-b border-gray-150 flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="tab = 'sales'" 
                                    :class="tab === 'sales' ? 'bg-primary-600 text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-gray-100 font-medium'" 
                                    class="text-xs px-3.5 py-2 rounded-xl transition-all">
                                Penjualan Terakhir ({{ $recentSales->count() }})
                            </button>
                            <button type="button" 
                                    @click="tab = 'purchases'" 
                                    :class="tab === 'purchases' ? 'bg-primary-600 text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-gray-100 font-medium'" 
                                    class="text-xs px-3.5 py-2 rounded-xl transition-all">
                                Pembelian Terakhir ({{ $recentPurchases->count() }})
                            </button>
                        </div>
                    </div>

                    {{-- Sales List --}}
                    <div x-show="tab === 'sales'" class="divide-y divide-gray-100">
                        @forelse($recentSales as $sale)
                        <a href="{{ route('sales.show', $sale) }}" class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50 transition-colors block">
                            <div>
                                <p class="text-xs font-bold text-dark">{{ $sale->invoice_number }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $sale->sale_date ? $sale->sale_date->locale('id')->isoFormat('D MMM Y, HH:mm') : $sale->created_at->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                                    @if($sale->customer)
                                        · Pelanggan: <span class="text-gray-600 font-medium">{{ $sale->customer->name }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-primary-600">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $sale->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $sale->payment_status === 'paid' ? 'Lunas' : 'Hutang' }}
                                </span>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center text-gray-400 text-xs">
                            Belum ada riwayat penjualan yang dicatat oleh pengguna ini.
                        </div>
                        @endforelse
                    </div>

                    {{-- Purchases List --}}
                    <div x-show="tab === 'purchases'" class="divide-y divide-gray-100" style="display: none;">
                        @forelse($recentPurchases as $purchase)
                        <a href="{{ route('purchases.show', $purchase) }}" class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50 transition-colors block">
                            <div>
                                <p class="text-xs font-bold text-dark">{{ $purchase->invoice_number }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $purchase->purchase_date ? $purchase->purchase_date->locale('id')->isoFormat('D MMM Y') : $purchase->created_at->locale('id')->isoFormat('D MMM Y') }}
                                    @if($purchase->supplier)
                                        · Tengkulak: <span class="text-gray-600 font-medium">{{ $purchase->supplier->name }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-dark">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $purchase->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $purchase->payment_status === 'paid' ? 'Lunas' : 'Sebagian/Hutang' }}
                                </span>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center text-gray-400 text-xs">
                            Belum ada riwayat pembelian yang dicatat oleh pengguna ini.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

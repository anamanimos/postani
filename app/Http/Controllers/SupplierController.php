<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::withSum(
                ['purchases as total_debt' => fn ($q) => $q->where('due_amount', '>', 0)],
                'due_amount'
            )
            ->orderBy('name')
            ->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if (empty($validated['notes']) && !empty($validated['description'])) {
            $validated['notes'] = $validated['description'];
        }

        $supplier = Supplier::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => 'Supplier berhasil ditambahkan.'
            ]);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load([
            'purchases' => fn ($q) => $q->latest('purchase_date')->latest('id')->take(20),
            'purchases.items.product',
        ]);

        $payments = \App\Models\SupplierPayment::whereHas(
                'purchase',
                fn ($q) => $q->where('supplier_id', $supplier->id)
            )
            ->with('purchase')
            ->latest('payment_date')
            ->latest('id')
            ->take(20)
            ->get();

        $totalDebt = $supplier->purchases()->where('due_amount', '>', 0)->sum('due_amount');

        return view('suppliers.show', compact('supplier', 'payments', 'totalDebt'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->exists()) {
            return redirect()
                ->route('suppliers.index')
                ->with('error', 'Supplier tidak dapat dihapus karena sudah memiliki transaksi pembelian.');
        }

        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}

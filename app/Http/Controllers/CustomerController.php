<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::withSum(
                ['sales as total_receivable' => fn ($q) => $q->where('due_amount', '>', 0)],
                'due_amount'
            )
            ->orderBy('name')
            ->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'sales' => fn ($q) => $q->latest('sale_date')->latest('id')->take(20),
            'sales.items.product',
        ]);

        $payments = \App\Models\CustomerPayment::whereHas(
                'sale',
                fn ($q) => $q->where('customer_id', $customer->id)
            )
            ->with('sale')
            ->latest('payment_date')
            ->latest('id')
            ->take(20)
            ->get();

        $totalReceivable = $customer->sales()->where('due_amount', '>', 0)->sum('due_amount');

        return view('customers.show', compact('customer', 'payments', 'totalReceivable'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->sales()->exists()) {
            return redirect()
                ->route('customers.index')
                ->with('error', 'Pelanggan tidak dapat dihapus karena sudah memiliki transaksi penjualan.');
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}

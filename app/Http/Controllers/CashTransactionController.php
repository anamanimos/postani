<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = CashTransaction::latest('transaction_date')->latest('id');

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(20)->withQueryString();

        // Running balance
        $totalIn = CashTransaction::where('type', 'in')->sum('amount');
        $totalOut = CashTransaction::where('type', 'out')->sum('amount');
        $balance = $totalIn - $totalOut;

        return view('cash-transactions.index', compact('transactions', 'totalIn', 'totalOut', 'balance'));
    }

    public function create(): View
    {
        return view('cash-transactions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:in,out'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['created_by'] = auth()->id();
        CashTransaction::create($validated);

        return redirect()
            ->route('cash-transactions.index')
            ->with('success', 'Transaksi kas berhasil disimpan.');
    }

    public function show(CashTransaction $cashTransaction): View
    {
        return view('cash-transactions.show', compact('cashTransaction'));
    }

    public function edit(CashTransaction $cashTransaction): View
    {
        return view('cash-transactions.edit', compact('cashTransaction'));
    }

    public function update(Request $request, CashTransaction $cashTransaction): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:in,out'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $cashTransaction->update($validated);

        return redirect()
            ->route('cash-transactions.index')
            ->with('success', 'Transaksi kas berhasil diperbarui.');
    }

    public function destroy(CashTransaction $cashTransaction): RedirectResponse
    {
        $cashTransaction->delete();

        return redirect()
            ->route('cash-transactions.index')
            ->with('success', 'Transaksi kas berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CustomerPayment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SupplierPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * List all purchases with outstanding due amounts, grouped by supplier.
     */
    public function supplierPayments(): View
    {
        $purchases = Purchase::with('supplier')
            ->where('due_amount', '>', 0)
            ->orderBy('supplier_id')
            ->latest('purchase_date')
            ->get()
            ->groupBy('supplier_id');

        return view('payments.suppliers', compact('purchases'));
    }

    /**
     * Process a payment to a supplier.
     */
    public function storeSupplierPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_id' => ['required', 'exists:purchases,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:cash,transfer,qris'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $purchase = Purchase::lockForUpdate()->findOrFail($validated['purchase_id']);

                // Cap payment amount to remaining due
                $amount = min((float) $validated['amount'], (float) $purchase->due_amount);

                SupplierPayment::create([
                    'purchase_id' => $purchase->id,
                    'amount' => $amount,
                    'payment_method' => $validated['payment_method'],
                    'payment_date' => $validated['payment_date'],
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                $purchase->paid_amount += $amount;
                $purchase->due_amount -= $amount;

                if ($purchase->due_amount <= 0) {
                    $purchase->due_amount = 0;
                    $purchase->payment_status = 'paid';
                } else {
                    $purchase->payment_status = 'partial';
                }

                $purchase->save();
            });

            return redirect()
                ->route('payments.suppliers')
                ->with('success', 'Pembayaran ke supplier berhasil disimpan.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * List all sales with outstanding due amounts, grouped by customer.
     */
    public function customerPayments(): View
    {
        $sales = Sale::with('customer')
            ->where('due_amount', '>', 0)
            ->whereNotNull('customer_id')
            ->orderBy('customer_id')
            ->latest('sale_date')
            ->get()
            ->groupBy('customer_id');

        return view('payments.customers', compact('sales'));
    }

    /**
     * Process a payment from a customer.
     */
    public function storeCustomerPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sale_id' => ['required', 'exists:sales,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:cash,transfer,qris'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $sale = Sale::lockForUpdate()->findOrFail($validated['sale_id']);

                // Cap payment amount to remaining due
                $amount = min((float) $validated['amount'], (float) $sale->due_amount);

                CustomerPayment::create([
                    'sale_id' => $sale->id,
                    'amount' => $amount,
                    'payment_method' => $validated['payment_method'],
                    'payment_date' => $validated['payment_date'],
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                $sale->paid_amount += $amount;
                $sale->due_amount -= $amount;

                if ($sale->due_amount <= 0) {
                    $sale->due_amount = 0;
                    $sale->payment_status = 'paid';
                } else {
                    $sale->payment_status = 'partial';
                }

                $sale->save();
            });

            return redirect()
                ->route('payments.customers')
                ->with('success', 'Pembayaran dari pelanggan berhasil disimpan.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }
}

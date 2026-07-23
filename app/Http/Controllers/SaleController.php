<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sale::with('customer')->latest('sale_date')->latest('id');

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $sales = $query->paginate(15)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $products = \App\Models\Product::where('is_active', true)
            ->with(['sellUnit', 'category'])
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'sale_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'string', 'in:cash,qris,transfer,credit'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        // Credit sales require a customer
        if ($validated['payment_method'] === 'credit' && empty($validated['customer_id'])) {
            $errorMsg = 'Pelanggan wajib dipilih untuk penjualan kredit.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMsg], 422);
            }

            return redirect()->back()->withInput()->with('error', $errorMsg);
        }

        try {
            $sale = DB::transaction(function () use ($validated) {
                $totalAmount = collect($validated['items'])->sum(
                    fn (array $item): float => $item['quantity'] * $item['unit_price']
                );

                $isCredit = $validated['payment_method'] === 'credit';

                if ($isCredit) {
                    $paidAmount = (float) ($validated['paid_amount'] ?? 0);
                    $dueAmount = $totalAmount - $paidAmount;
                    $paymentStatus = $paidAmount >= $totalAmount ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');
                } else {
                    $paidAmount = $totalAmount;
                    $dueAmount = 0;
                    $paymentStatus = 'paid';
                }

                $sale = Sale::create([
                    'invoice_number' => Sale::generateInvoiceNumber(),
                    'customer_id' => $validated['customer_id'] ?? null,
                    'sale_date' => $validated['sale_date'] ?? Carbon::now(),
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $validated['payment_method'],
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                foreach ($validated['items'] as $item) {
                    // Creating SaleItem — observer will handle stock deduction
                    // quantity is in sell_unit, observer converts via conversion_factor
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['quantity'] * $item['unit_price'],
                    ]);
                }

                return $sale;
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Penjualan berhasil disimpan.',
                    'sale_id' => $sale->id,
                    'redirect' => route('sales.receipt', $sale),
                ]);
            }

            return redirect()
                ->route('sales.receipt', $sale)
                ->with('success', 'Penjualan berhasil disimpan.');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Gagal menyimpan penjualan: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan penjualan: ' . $e->getMessage());
        }
    }

    public function show(Sale $sale): View
    {
        $sale->load([
            'customer',
            'saleItems.product.buyUnit',
            'saleItems.product.sellUnit',
            'customerPayments',
        ]);

        return view('sales.show', compact('sale'));
    }

    /**
     * Generate and stream a PDF receipt for the sale.
     */
    public function receipt(Sale $sale): HttpResponse
    {
        $sale->load([
            'customer',
            'saleItems.product.sellUnit',
        ]);

        $settings = Setting::getAllCached();

        $pdf = Pdf::loadView('sales.receipt', compact('sale', 'settings'))
            ->setPaper([0, 0, 226.77, 600], 'portrait'); // ~80mm thermal receipt width

        return $pdf->stream("struk-{$sale->invoice_number}.pdf");
    }

    /**
     * Update the sale date for a transaction.
     */
    public function updateDate(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'sale_date' => ['required', 'date'],
        ]);

        $sale->update([
            'sale_date' => $validated['sale_date'],
        ]);

        return redirect()->back()->with('success', 'Tanggal transaksi berhasil diperbarui.');
    }

    /**
     * Soft delete a sale transaction and restore product stock.
     */
    public function destroy(Sale $sale): RedirectResponse
    {
        try {
            DB::transaction(function () use ($sale) {
                foreach ($sale->saleItems as $item) {
                    $product = $item->product;
                    if ($product) {
                        $stockReduction = $item->quantity * $product->conversion_factor;
                        $product->stock += $stockReduction;
                        $product->save();
                    }
                }

                $sale->delete();
            });

            return redirect()
                ->back()
                ->with('success', 'Data transaksi penjualan berhasil dihapus (soft delete).');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus transaksi penjualan: ' . $e->getMessage());
        }
    }
}

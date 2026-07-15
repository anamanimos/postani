<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePriceHistory;
use App\Models\Supplier;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Purchase::with('supplier')->latest('purchase_date')->latest('id');

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchases = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->with('buyUnit')
            ->orderBy('name')
            ->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'supplier_invoice_number' => ['nullable', 'string', 'max:255'],
            'invoice_image' => ['nullable', 'image', 'max:2048'],
            'gallery_filepath' => ['nullable', 'string', 'max:500'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'in:cash,transfer,qris'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'additional_cost' => ['nullable', 'numeric', 'min:0'],
            'additional_cost_notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $invoiceImagePath = $request->input('gallery_filepath');
            
            if ($request->hasFile('invoice_image')) {
                $file = $request->file('invoice_image');
                $filename = $file->getClientOriginalName();
                $filepath = $file->store('gallery', 'public');

                \App\Models\Gallery::create([
                    'filename' => $filename,
                    'filepath' => $filepath,
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);

                $invoiceImagePath = $filepath;
            }

            $purchase = DB::transaction(function () use ($validated, $invoiceImagePath) {
                $itemsSubtotal = collect($validated['items'])->sum(
                    fn (array $item): float => $item['quantity'] * $item['unit_price']
                );
                $additionalCost = (float) ($validated['additional_cost'] ?? 0);
                $totalAmount = $itemsSubtotal + $additionalCost;

                $paidAmount = (float) ($validated['paid_amount'] ?? 0);
                $dueAmount = $totalAmount - $paidAmount;

                if ($paidAmount >= $totalAmount) {
                    $paymentStatus = 'paid';
                    $paidAmount = $totalAmount;
                    $dueAmount = 0;
                } elseif ($paidAmount > 0) {
                    $paymentStatus = 'partial';
                } else {
                    $paymentStatus = 'unpaid';
                }

                $purchase = Purchase::create([
                    'invoice_number' => Purchase::generateInvoiceNumber(),
                    'supplier_id' => $validated['supplier_id'],
                    'purchase_date' => $validated['purchase_date'] ?? Carbon::today(),
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $validated['payment_method'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'supplier_invoice_number' => $validated['supplier_invoice_number'] ?? null,
                    'invoice_image' => $invoiceImagePath,
                    'additional_cost' => $additionalCost,
                    'additional_cost_notes' => $validated['additional_cost_notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                foreach ($validated['items'] as $item) {
                    // Creating PurchaseItem — observer will handle stock update,
                    // purchase price history, and margin check
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['quantity'] * $item['unit_price'],
                    ]);
                }

                return $purchase;
            });

            return redirect()
                ->route('purchases.show', $purchase)
                ->with('success', 'Pembelian berhasil disimpan.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pembelian: ' . $e->getMessage());
        }
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load([
            'supplier',
            'purchaseItems.product.buyUnit',
            'purchaseItems.product.sellUnit',
            'supplierPayments',
        ]);

        return view('purchases.show', compact('purchase'));
    }

    /**
     * AJAX: Get price history for a supplier + product combination.
     */
    public function getPriceHistory(Request $request): JsonResponse
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $history = PurchasePriceHistory::where('supplier_id', $request->supplier_id)
            ->where('product_id', $request->product_id)
            ->latest('created_at')
            ->take(5)
            ->get(['unit_price', 'quantity', 'created_at']);

        return response()->json($history);
    }
}

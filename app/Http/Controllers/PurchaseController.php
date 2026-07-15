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

    public function edit(Purchase $purchase): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        // Include products that are currently in the purchase items, even if they are inactive
        $purchaseProductIds = $purchase->purchaseItems->pluck('product_id')->toArray();
        $products = Product::where('is_active', true)
            ->orWhereIn('id', $purchaseProductIds)
            ->with('buyUnit')
            ->orderBy('name')
            ->get();

        $purchase->load([
            'supplier',
            'purchaseItems.product.buyUnit',
        ]);

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
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
            $invoiceImagePath = $request->input('gallery_filepath', $purchase->invoice_image);
            
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

            DB::transaction(function () use ($validated, $purchase, $invoiceImagePath) {
                // Collect affected products (old and new)
                $oldProductIds = $purchase->purchaseItems->pluck('product_id')->toArray();
                $newProductIds = collect($validated['items'])->pluck('product_id')->toArray();
                $affectedProductIds = array_unique(array_merge($oldProductIds, $newProductIds));

                // 1. Revert stocks of old purchase items
                foreach ($purchase->purchaseItems as $oldItem) {
                    $product = $oldItem->product;
                    $product->stock -= $oldItem->quantity;
                    $product->save();

                    // Delete old price history
                    PurchasePriceHistory::where('purchase_item_id', $oldItem->id)->delete();
                    
                    // Delete old item
                    $oldItem->delete();
                }

                // 2. Calculate header amounts
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

                // 3. Update purchase header
                $purchase->update([
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
                ]);

                // 4. Create new purchase items (triggers observer for stock, history, margin check)
                foreach ($validated['items'] as $item) {
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['quantity'] * $item['unit_price'],
                    ]);
                }

                // 5. Recalculate price and cost averages for all affected products
                foreach ($affectedProductIds as $productId) {
                    $this->recalculateProductPrices($productId);
                }
            });

            return redirect()
                ->route('purchases.show', $purchase)
                ->with('success', 'Pembelian berhasil diperbarui.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pembelian: ' . $e->getMessage());
        }
    }

    protected function recalculateProductPrices($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        // Find the latest purchase item for this product
        $latestItem = PurchaseItem::where('product_id', $productId)
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->orderBy('purchases.purchase_date', 'desc')
            ->orderBy('purchase_items.id', 'desc')
            ->select('purchase_items.*')
            ->first();

        if ($latestItem) {
            $product->last_purchase_price = $latestItem->unit_price;
        } else {
            $product->last_purchase_price = 0;
        }

        // Recalculate avg_purchase_price
        $aggregates = PurchaseItem::where('product_id', $productId)
            ->selectRaw('SUM(quantity * unit_price) as total_value, SUM(quantity) as total_qty')
            ->first();

        if ($aggregates && $aggregates->total_qty > 0) {
            $product->avg_purchase_price = $aggregates->total_value / $aggregates->total_qty;
        } else {
            $product->avg_purchase_price = 0;
        }

        // Enforce margin rule
        $minMargin = (float) \App\Models\Setting::get('min_margin', 1000);
        $costPerSellUnit = $product->last_purchase_price * $product->conversion_factor;
        $currentMargin = $product->selling_price - $costPerSellUnit;

        if ($currentMargin < $minMargin) {
            $product->selling_price = $costPerSellUnit + $minMargin;
        }

        $product->save();
    }
}

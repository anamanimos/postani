<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'buyUnit', 'sellUnit']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'buy_unit_id' => ['required', 'exists:units,id'],
            'sell_unit_id' => ['required', 'exists:units,id'],
            'conversion_factor' => ['required', 'numeric', 'min:0.0001'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['stock'] = 0;
        $validated['min_stock'] = $validated['min_stock'] ?? 0;

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'buyUnit', 'sellUnit']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'buy_unit_id' => ['required', 'exists:units,id'],
            'sell_unit_id' => ['required', 'exists:units,id'],
            'conversion_factor' => ['required', 'numeric', 'min:0.0001'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_stock'] = $validated['min_stock'] ?? 0;

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Check if product has any purchase or sale items
        $hasPurchases = $product->purchaseItems()->exists();
        $hasSales = $product->saleItems()->exists();

        if ($hasPurchases || $hasSales) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena sudah memiliki transaksi.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * AJAX search for POS and purchase screens.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $products = Product::where('is_active', true)
            ->where('name', 'like', '%' . $query . '%')
            ->with(['buyUnit', 'sellUnit', 'category'])
            ->select([
                'id',
                'name',
                'stock',
                'selling_price',
                'last_purchase_price',
                'buy_unit_id',
                'sell_unit_id',
                'conversion_factor',
                'image',
                'category_id',
            ])
            ->limit(20)
            ->get();

        return response()->json($products);
    }
}

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
    public function index(Request $request): View|JsonResponse
    {
        $query = Product::with(['category', 'buyUnit', 'sellUnit']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Support both 'category' and 'category_id' query params
        $categoryId = $request->input('category', $request->input('category_id'));
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'selling_price' => (float) $p->selling_price,
                    'stock' => (float) $p->stock,
                    'min_stock' => (float) ($p->min_stock ?? 0),
                    'is_active' => (bool) $p->is_active,
                    'image' => $p->image,
                    'category_name' => $p->category->name ?? 'Tanpa Kategori',
                    'sell_unit_symbol' => $p->sellUnit->symbol ?? '',
                    'show_url' => route('products.show', $p),
                ]),
                'next_page_url' => $products->nextPageUrl(),
            ]);
        }

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
            'gallery_filepath' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $imagePath = $request->input('gallery_filepath');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $filepath = $file->store('gallery', 'public');

            \App\Models\Gallery::create([
                'filename' => $filename,
                'filepath' => $filepath,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $imagePath = $filepath;
        }

        $validated['image'] = $imagePath;
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
            'gallery_filepath' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $imagePath = $request->input('gallery_filepath', $product->image);

        if ($request->hasFile('image')) {
            // Delete old image only if it's not a gallery item and not used elsewhere
            if ($product->image && !\App\Models\Gallery::where('filepath', $product->image)->exists()) {
                Storage::disk('public')->delete($product->image);
            }
            
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $filepath = $file->store('gallery', 'public');

            \App\Models\Gallery::create([
                'filename' => $filename,
                'filepath' => $filepath,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $imagePath = $filepath;
        }

        $validated['image'] = $imagePath;
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
            // Delete old image only if it's not a gallery item
            if (!\App\Models\Gallery::where('filepath', $product->image)->exists()) {
                Storage::disk('public')->delete($product->image);
            }
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

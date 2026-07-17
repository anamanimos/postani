<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Display a listing of the gallery items.
     */
    public function index(Request $request): View
    {
        $query = Gallery::with(['products', 'purchases', 'labels']);

        // Search filter
        if ($request->filled('search')) {
            $query->where('filename', 'like', '%' . $request->search . '%');
        }

        // Usage filter
        if ($request->filled('filter')) {
            if ($request->filter === 'used') {
                $query->where(function ($q) {
                    $q->has('products')->orHas('purchases');
                });
            } elseif ($request->filter === 'unused') {
                $query->doesntHave('products')->doesntHave('purchases');
            }
        }

        // Label filter
        if ($request->filled('label')) {
            $query->whereHas('labels', function ($q) use ($request) {
                $q->where('name', $request->label);
            });
        }

        $galleries = $query->latest()->paginate(18)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $galleries->map(fn($g) => [
                    'id' => $g->id,
                    'filepath' => $g->filepath,
                    'url' => asset('storage/' . $g->filepath),
                    'filename' => $g->filename,
                    'labels' => $g->labels->pluck('name'),
                    'is_used' => $g->is_used,
                    'usages' => $g->usages
                ]),
                'next_page_url' => $galleries->nextPageUrl(),
            ]);
        }

        $allLabels = \App\Models\Label::orderBy('name')->get();

        return view('galleries.index', compact('galleries', 'allLabels'));
    }

    /**
     * Store a newly created gallery item in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            
            // Store under 'gallery' directory of public disk
            $filepath = $file->store('gallery', 'public');

            $gallery = Gallery::create([
                'filename' => $filename,
                'filepath' => $filepath,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'gallery' => $gallery,
                ]);
            }

            return redirect()
                ->route('galleries.index')
                ->with('success', 'Gambar berhasil diunggah ke galeri.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas gambar tidak ditemukan.',
            ], 400);
        }

        return redirect()
            ->route('galleries.index')
            ->with('error', 'Gagal mengunggah gambar.');
    }

    /**
     * Remove the specified gallery item from storage.
     */
    public function destroy(Gallery $gallery)
    {
        if ($gallery->is_used) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gambar tidak dapat dihapus karena sedang digunakan.',
                ], 422);
            }

            return redirect()
                ->route('galleries.index')
                ->with('error', 'Gambar tidak dapat dihapus karena sedang digunakan.');
        }

        // Delete from storage disk
        if (Storage::disk('public')->exists($gallery->filepath)) {
            Storage::disk('public')->delete($gallery->filepath);
        }

        $gallery->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('galleries.index')
            ->with('success', 'Gambar berhasil dihapus dari galeri.');
    }

    /**
     * API: Get all gallery items for picker.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $query = Gallery::query();

        if ($request->filled('search')) {
            $query->where('filename', 'like', '%' . $request->search . '%');
        }

        $galleries = $query->latest()->get()->map(function ($gallery) {
            return [
                'id' => $gallery->id,
                'filename' => $gallery->filename,
                'filepath' => $gallery->filepath,
                'url' => asset('storage/' . $gallery->filepath),
                'is_used' => $gallery->is_used,
            ];
        });

        return response()->json($galleries);
    }

    /**
     * API: Update labels for the specified gallery item.
     */
    public function updateLabels(Request $request, Gallery $gallery): JsonResponse
    {
        $request->validate([
            'labels' => ['nullable', 'array'],
            'labels.*' => ['string', 'max:50', 'distinct'],
        ]);

        $labelIds = [];
        if ($request->has('labels')) {
            foreach ($request->input('labels') as $labelName) {
                $labelName = trim($labelName);
                if ($labelName !== '') {
                    $label = \App\Models\Label::firstOrCreate(['name' => $labelName]);
                    $labelIds[] = $label->id;
                }
            }
        }

        $gallery->labels()->sync($labelIds);
        $gallery->load('labels');

        return response()->json([
            'success' => true,
            'labels' => $gallery->labels->pluck('name'),
        ]);
    }
}

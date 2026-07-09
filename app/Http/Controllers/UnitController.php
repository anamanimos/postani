<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::orderBy('name')->paginate(15);

        return view('units.index', compact('units'));
    }

    public function create(): View
    {
        return view('units.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:20'],
        ]);

        Unit::create($validated);

        return redirect()
            ->route('units.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit): View
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:20'],
        ]);

        $unit->update($validated);

        return redirect()
            ->route('units.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $usedAsBuyUnit = \App\Models\Product::where('buy_unit_id', $unit->id)->exists();
        $usedAsSellUnit = \App\Models\Product::where('sell_unit_id', $unit->id)->exists();

        if ($usedAsBuyUnit || $usedAsSellUnit) {
            return redirect()
                ->route('units.index')
                ->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh produk.');
        }

        $unit->delete();

        return redirect()
            ->route('units.index')
            ->with('success', 'Satuan berhasil dihapus.');
    }
}

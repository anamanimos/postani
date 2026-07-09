<?php

namespace App\Observers;

use App\Models\SaleItem;

class SaleItemObserver
{
    /**
     * Handle the SaleItem "created" event.
     *
     * When a sale item is created:
     * 1. Convert quantity from sell_unit to buy_unit using conversion_factor
     * 2. Validate sufficient stock
     * 3. Decrease product stock
     */
    public function created(SaleItem $saleItem): void
    {
        $product = $saleItem->product;

        // 1. Convert quantity from sell_unit to buy_unit
        $stockReduction = $saleItem->quantity * $product->conversion_factor;

        // 2. Validate stock availability
        if ($product->stock < $stockReduction) {
            throw new \RuntimeException(
                "Stok tidak mencukupi untuk produk {$product->name}. " .
                "Stok tersedia: {$product->stock} {$product->buyUnit->symbol}, " .
                "dibutuhkan: {$stockReduction} {$product->buyUnit->symbol}."
            );
        }

        // 3. Decrease product stock
        $product->stock -= $stockReduction;
        $product->save();
    }
}

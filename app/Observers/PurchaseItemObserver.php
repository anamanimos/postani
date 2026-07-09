<?php

namespace App\Observers;

use App\Models\PurchaseItem;
use App\Models\PurchasePriceHistory;
use App\Models\Setting;

class PurchaseItemObserver
{
    /**
     * Handle the PurchaseItem "created" event.
     *
     * When a purchase item is created:
     * 1. Increase product stock by quantity (in buy_unit)
     * 2. Update last_purchase_price
     * 3. Recalculate avg_purchase_price as weighted average
     * 4. Insert purchase_price_history record
     * 5. Check & enforce minimum margin rule on selling_price
     */
    public function created(PurchaseItem $purchaseItem): void
    {
        $product = $purchaseItem->product;
        $purchase = $purchaseItem->purchase;

        // 1. Increase product stock by quantity (already in buy_unit)
        $product->stock += $purchaseItem->quantity;

        // 2. Update last_purchase_price
        $product->last_purchase_price = $purchaseItem->unit_price;

        // 3. Recalculate avg_purchase_price as weighted average from ALL purchase_items
        $aggregates = PurchaseItem::where('product_id', $product->id)
            ->selectRaw('SUM(quantity * unit_price) as total_value, SUM(quantity) as total_qty')
            ->first();

        if ($aggregates->total_qty > 0) {
            $product->avg_purchase_price = $aggregates->total_value / $aggregates->total_qty;
        }

        // 4. Insert row into purchase_price_history
        PurchasePriceHistory::create([
            'supplier_id' => $purchase->supplier_id,
            'product_id' => $product->id,
            'unit_price' => $purchaseItem->unit_price,
            'purchase_date' => $purchase->purchase_date,
            'purchase_item_id' => $purchaseItem->id,
        ]);

        // 5. Check margin rule
        $minMargin = (float) Setting::get('min_margin', 1000);
        $costPerSellUnit = $product->last_purchase_price * $product->conversion_factor;
        $currentMargin = $product->selling_price - $costPerSellUnit;

        if ($currentMargin < $minMargin) {
            $product->selling_price = $costPerSellUnit + $minMargin;
        }

        // 6. Save product
        $product->save();
    }
}

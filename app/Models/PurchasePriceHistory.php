<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePriceHistory extends Model
{
    protected $table = 'purchase_price_history';

    protected $fillable = ['supplier_id', 'product_id', 'unit_price', 'purchase_date', 'purchase_item_id'];

    protected $casts = [
        'unit_price' => 'double',
        'purchase_date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }
}

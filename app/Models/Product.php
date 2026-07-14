<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'sku', 'name', 'buy_unit_id', 'sell_unit_id',
        'conversion_factor', 'last_purchase_price', 'avg_purchase_price',
        'selling_price', 'stock', 'min_stock', 'image', 'notes', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conversion_factor' => 'double',
        'last_purchase_price' => 'double',
        'avg_purchase_price' => 'double',
        'selling_price' => 'double',
        'stock' => 'double',
        'min_stock' => 'double',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function buyUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'buy_unit_id');
    }

    public function sellUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'sell_unit_id');
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(PurchasePriceHistory::class);
    }

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class, 'image', 'filepath');
    }
}

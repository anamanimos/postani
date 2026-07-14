<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    protected $fillable = [
        'filename',
        'filepath',
        'mime_type',
        'file_size',
    ];

    /**
     * Get products using this gallery image.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'image', 'filepath');
    }

    /**
     * Get purchases using this gallery image.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'invoice_image', 'filepath');
    }

    /**
     * Determine if the gallery image is used anywhere.
     */
    public function getIsUsedAttribute(): bool
    {
        return $this->products()->exists() || $this->purchases()->exists();
    }

    /**
     * Get detailed list of usages.
     */
    public function getUsagesAttribute(): array
    {
        $usages = [];

        foreach ($this->products as $product) {
            $usages[] = [
                'type' => 'Produk',
                'name' => $product->name,
                'show_url' => route('products.show', $product),
                'edit_url' => route('products.edit', $product),
            ];
        }

        foreach ($this->purchases as $purchase) {
            $usages[] = [
                'type' => 'Nota Pembelian',
                'name' => $purchase->invoice_number,
                'show_url' => route('purchases.show', $purchase),
                'edit_url' => null,
            ];
        }

        return $usages;
    }
}

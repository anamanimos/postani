<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = ['name', 'symbol'];

    public function buyProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'buy_unit_id');
    }

    public function sellProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'sell_unit_id');
    }
}

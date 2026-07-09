<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Sale extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'sale_date', 'total_amount',
        'payment_method', 'payment_status', 'paid_amount', 'due_amount', 'created_by'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'total_amount' => 'double',
        'paid_amount' => 'double',
        'due_amount' => 'double',
    ];

    public static function generateInvoiceNumber(): string
    {
        $today = Carbon::today()->format('Ymd');
        $prefix = "PJ-" . $today . "-";
        
        $lastSale = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastSale) {
            $lastNum = intval(substr($lastSale->invoice_number, -4));
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customerPayments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

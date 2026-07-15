<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Purchase extends Model
{
    protected $fillable = [
        'invoice_number', 'supplier_id', 'purchase_date', 'total_amount',
        'payment_status', 'paid_amount', 'due_amount', 'notes', 'created_by',
        'supplier_invoice_number', 'invoice_image', 'additional_cost', 'additional_cost_notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'double',
        'paid_amount' => 'double',
        'due_amount' => 'double',
        'additional_cost' => 'double',
    ];

    public static function generateInvoiceNumber(): string
    {
        $today = Carbon::today()->format('Ymd');
        $prefix = "PB-" . $today . "-";
        
        $lastPurchase = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastPurchase) {
            $lastNum = intval(substr($lastPurchase->invoice_number, -4));
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class, 'invoice_image', 'filepath');
    }
}

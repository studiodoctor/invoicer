<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'details',
        'quantity',
        'unit',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'discount_rate',
        'discount_amount',
        'total',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function calculateTotal(): void
    {
        $lineTotal = $this->quantity * $this->unit_price;
        
        if ($this->discount_rate > 0) {
            $this->discount_amount = $lineTotal * ($this->discount_rate / 100);
        }
        $lineTotal -= $this->discount_amount;
        
        if ($this->tax_rate > 0) {
            $this->tax_amount = $lineTotal * ($this->tax_rate / 100);
        }
        
        $this->total = $lineTotal + $this->tax_amount;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateTotal();
        });
    }
}
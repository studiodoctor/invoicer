<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
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

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function calculateTotal(): void
    {
        $lineTotal = $this->quantity * $this->unit_price;
        
        // Apply discount
        if ($this->discount_rate > 0) {
            $this->discount_amount = $lineTotal * ($this->discount_rate / 100);
        }
        $lineTotal -= $this->discount_amount;
        
        // Apply tax
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
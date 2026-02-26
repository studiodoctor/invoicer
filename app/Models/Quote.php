<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'quote_number',
        'reference',
        'status',
        'issue_date',
        'expiry_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_type_value',
        'discount_amount',
        'total',
        'currency',
        'notes',
        'terms',
        'sign_token',
        'signed_at',
        'signed_ip',
        'signature_data',
        'signer_name',
        'sent_at',
        'viewed_at',
        'converted_invoice_id',
    ];

    protected $casts = [
        'status' => QuoteStatus::class,
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'signed_at' => 'datetime',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (empty($quote->sign_token)) {
                $quote->sign_token = Str::random(64);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('sort_order');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function convertedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'converted_invoice_id');
    }

    public function getSignUrlAttribute(): string
    {
        return route('quotes.sign', ['token' => $this->sign_token]);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date->isPast() && !in_array($this->status, [QuoteStatus::SIGNED, QuoteStatus::CONVERTED]);
    }

    public function getCanBeSignedAttribute(): bool
    {
        return in_array($this->status, [QuoteStatus::SENT, QuoteStatus::VIEWED]) && !$this->is_expired;
    }

    public function getCanBeConvertedAttribute(): bool
    {
        return $this->status === QuoteStatus::SIGNED && !$this->converted_invoice_id;
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total');
        
        // Calculate discount
        if ($this->discount_type_value === 'percentage' && $this->discount_type > 0) {
            $this->discount_amount = $this->subtotal * ($this->discount_type / 100);
        } elseif ($this->discount_type_value === 'fixed' && $this->discount_type > 0) {
            $this->discount_amount = $this->discount_type;
        } else {
            $this->discount_amount = 0;
        }

        $afterDiscount = $this->subtotal - $this->discount_amount;
        
        // Calculate tax
        $this->tax_amount = $afterDiscount * ($this->tax_rate / 100);
        
        $this->total = $afterDiscount + $this->tax_amount;
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => QuoteStatus::SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsViewed(): void
    {
        if ($this->status === QuoteStatus::SENT) {
            $this->update([
                'status' => QuoteStatus::VIEWED,
                'viewed_at' => now(),
            ]);
        }
    }

    public function sign(string $signerName, string $signatureData, string $ip): void
    {
        $this->update([
            'status' => QuoteStatus::SIGNED,
            'signed_at' => now(),
            'signer_name' => $signerName,
            'signature_data' => $signatureData,
            'signed_ip' => $ip,
        ]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeStatus($query, QuoteStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('quote_number', 'like', "%{$search}%")
              ->orWhere('reference', 'like', "%{$search}%")
              ->orWhereHas('client', function ($clientQuery) use ($search) {
                  $clientQuery->where('company_name', 'like', "%{$search}%");
              });
        });
    }
}
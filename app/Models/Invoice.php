<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'quote_id',
        'invoice_number',
        'reference',
        'status',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_type_value',
        'discount_amount',
        'total',
        'amount_paid',
        'amount_due',
        'currency',
        'notes',
        'terms',
        'payment_instructions',
        'sent_at',
        'viewed_at',
        'paid_at',
        'payment_method',
        'transaction_id',
        'is_recurring',
        'recurring_frequency',
        'recurring_end_date',
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'paid_at' => 'datetime',
        'is_recurring' => 'boolean',
        'recurring_end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() && 
               !in_array($this->status, [InvoiceStatus::PAID, InvoiceStatus::CANCELLED, InvoiceStatus::REFUNDED]);
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return $this->due_date->diffInDays(now());
    }

    public function getDaysUntilDueAttribute(): int
    {
        if ($this->due_date->isPast()) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
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
        $this->amount_due = $this->total - $this->amount_paid;
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => InvoiceStatus::SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsViewed(): void
    {
        if ($this->status === InvoiceStatus::SENT) {
            $this->update([
                'status' => InvoiceStatus::VIEWED,
                'viewed_at' => now(),
            ]);
        }
    }

    public function recordPayment(float $amount, string $paymentMethod, ?string $transactionId = null, ?string $notes = null): Payment
    {
        $payment = $this->payments()->create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'currency' => $this->currency,
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
            'notes' => $notes,
            'payment_date' => now(),
        ]);

        $this->amount_paid += $amount;
        $this->amount_due = $this->total - $this->amount_paid;

        if ($this->amount_due <= 0) {
            $this->status = InvoiceStatus::PAID;
            $this->paid_at = now();
            $this->payment_method = $paymentMethod;
            $this->transaction_id = $transactionId;
        } else {
            $this->status = InvoiceStatus::PARTIAL;
        }

        $this->save();

        return $payment;
    }

    public function checkOverdue(): void
    {
        if ($this->is_overdue && !in_array($this->status, [InvoiceStatus::OVERDUE, InvoiceStatus::PAID, InvoiceStatus::CANCELLED])) {
            $this->update(['status' => InvoiceStatus::OVERDUE]);
        }
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeStatus($query, InvoiceStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->whereNotIn('status', ['paid', 'cancelled', 'refunded']);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhere('reference', 'like', "%{$search}%")
              ->orWhereHas('client', function ($clientQuery) use ($search) {
                  $clientQuery->where('company_name', 'like', "%{$search}%");
              });
        });
    }
}
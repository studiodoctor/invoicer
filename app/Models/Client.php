<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'website',
        'vat_number',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'currency',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->contact_name ?: $this->company_name;
    }

    public function getTotalRevenueAttribute(): float
    {
        return $this->invoices()
            ->where('status', 'paid')
            ->sum('total');
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return $this->invoices()
            ->whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])
            ->sum('amount_due');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('company_name', 'like', "%{$search}%")
              ->orWhere('contact_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }
}
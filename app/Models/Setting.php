<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_phone',
        'company_website',
        'company_address',
        'company_city',
        'company_state',
        'company_postal_code',
        'company_country',
        'vat_number',
        'tax_id',
        'logo_path',
        'default_currency',
        'date_format',
        'default_tax_rate',
        'invoice_prefix',
        'quote_prefix',
        'invoice_start_number',
        'quote_start_number',
        'quote_validity_days',
        'invoice_due_days',
        'default_invoice_notes',
        'default_invoice_terms',
        'default_quote_notes',
        'default_quote_terms',
        'payment_instructions',
        'primary_color',
        'secondary_color',
        'show_logo_on_documents',
        'show_payment_instructions',
    ];

    protected $casts = [
        'default_tax_rate' => 'decimal:2',
        'show_logo_on_documents' => 'boolean',
        'show_payment_instructions' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }
        return Storage::url($this->logo_path);
    }

    public function getFullCompanyAddressAttribute(): string
    {
        $parts = array_filter([
            $this->company_address,
            $this->company_city,
            $this->company_state,
            $this->company_postal_code,
            $this->company_country,
        ]);

        return implode(', ', $parts);
    }

    public function getNextQuoteNumber(): string
    {
        $lastQuote = Quote::where('user_id', $this->user_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastQuote) {
            $lastNumber = (int) str_replace($this->quote_prefix, '', $lastQuote->quote_number);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = $this->quote_start_number;
        }

        return $this->quote_prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function getNextInvoiceNumber(): string
    {
        $lastInvoice = Invoice::where('user_id', $this->user_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) str_replace($this->invoice_prefix, '', $lastInvoice->invoice_number);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = $this->invoice_start_number;
        }

        return $this->invoice_prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
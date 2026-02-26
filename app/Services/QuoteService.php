<?php

namespace App\Services;

use App\Enums\QuoteStatus;
use App\Models\Activity;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class QuoteService
{
    public function create(array $data, int $userId): Quote
    {
        return DB::transaction(function () use ($data, $userId) {
            $settings = Setting::where('user_id', $userId)->first();

            $quote = Quote::create([
                'user_id' => $userId,
                'client_id' => $data['client_id'],
                'quote_number' => $settings->getNextQuoteNumber(),
                'reference' => $data['reference'] ?? null,
                'status' => QuoteStatus::DRAFT,
                'issue_date' => $data['issue_date'],
                'expiry_date' => $data['expiry_date'],
                'tax_rate' => $data['tax_rate'] ?? $settings->default_tax_rate,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_type_value' => $data['discount_type_value'] ?? 'percentage',
                'currency' => $data['currency'] ?? $settings->default_currency,
                'notes' => $data['notes'] ?? $settings->default_quote_notes,
                'terms' => $data['terms'] ?? $settings->default_quote_terms,
            ]);

            foreach ($data['items'] as $index => $item) {
                $quote->items()->create([
                    'description' => $item['description'],
                    'details' => $item['details'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'unit',
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'discount_rate' => $item['discount_rate'] ?? 0,
                    'sort_order' => $index,
                ]);
            }

            $quote->load('items');
            $quote->calculateTotals();
            $quote->save();

            Activity::log($quote, 'created', 'Quote created');

            return $quote;
        });
    }

    public function update(Quote $quote, array $data): Quote
    {
        return DB::transaction(function () use ($quote, $data) {
            $quote->update([
                'client_id' => $data['client_id'],
                'reference' => $data['reference'] ?? null,
                'issue_date' => $data['issue_date'],
                'expiry_date' => $data['expiry_date'],
                'tax_rate' => $data['tax_rate'] ?? 0,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_type_value' => $data['discount_type_value'] ?? 'percentage',
                'currency' => $data['currency'],
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
            ]);

            // Delete existing items
            $quote->items()->delete();

            // Create new items
            foreach ($data['items'] as $index => $item) {
                $quote->items()->create([
                    'description' => $item['description'],
                    'details' => $item['details'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'unit',
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'discount_rate' => $item['discount_rate'] ?? 0,
                    'sort_order' => $index,
                ]);
            }

            $quote->load('items');
            $quote->calculateTotals();
            $quote->save();

            Activity::log($quote, 'updated', 'Quote updated');

            return $quote;
        });
    }

    public function convertToInvoice(Quote $quote): Invoice
    {
        return DB::transaction(function () use ($quote) {
            $settings = Setting::where('user_id', $quote->user_id)->first();

            $invoice = Invoice::create([
                'user_id' => $quote->user_id,
                'client_id' => $quote->client_id,
                'quote_id' => $quote->id,
                'invoice_number' => $settings->getNextInvoiceNumber(),
                'reference' => $quote->reference,
                'status' => 'draft',
                'issue_date' => now(),
                'due_date' => now()->addDays($settings->invoice_due_days),
                'subtotal' => $quote->subtotal,
                'tax_rate' => $quote->tax_rate,
                'tax_amount' => $quote->tax_amount,
                'discount_type' => $quote->discount_type,
                'discount_type_value' => $quote->discount_type_value,
                'discount_amount' => $quote->discount_amount,
                'total' => $quote->total,
                'amount_due' => $quote->total,
                'currency' => $quote->currency,
                'notes' => $settings->default_invoice_notes,
                'terms' => $settings->default_invoice_terms,
                'payment_instructions' => $settings->payment_instructions,
            ]);

            // Copy items
            foreach ($quote->items as $item) {
                $invoice->items()->create([
                    'description' => $item->description,
                    'details' => $item->details,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'tax_rate' => $item->tax_rate,
                    'tax_amount' => $item->tax_amount,
                    'discount_rate' => $item->discount_rate,
                    'discount_amount' => $item->discount_amount,
                    'total' => $item->total,
                    'sort_order' => $item->sort_order,
                ]);
            }

            // Update quote
            $quote->update([
                'status' => QuoteStatus::CONVERTED,
                'converted_invoice_id' => $invoice->id,
            ]);

            Activity::log($quote, 'converted', 'Quote converted to invoice', ['invoice_id' => $invoice->id]);
            Activity::log($invoice, 'created', 'Invoice created from quote', ['quote_id' => $quote->id]);

            return $invoice;
        });
    }

    public function duplicate(Quote $quote): Quote
    {
        return DB::transaction(function () use ($quote) {
            $settings = Setting::where('user_id', $quote->user_id)->first();

            $newQuote = $quote->replicate();
            $newQuote->quote_number = $settings->getNextQuoteNumber();
            $newQuote->status = QuoteStatus::DRAFT;
            $newQuote->issue_date = now();
            $newQuote->expiry_date = now()->addDays($settings->quote_validity_days);
            $newQuote->sign_token = null;
            $newQuote->signed_at = null;
            $newQuote->signed_ip = null;
            $newQuote->signature_data = null;
            $newQuote->signer_name = null;
            $newQuote->sent_at = null;
            $newQuote->viewed_at = null;
            $newQuote->converted_invoice_id = null;
            $newQuote->save();

            foreach ($quote->items as $item) {
                $newItem = $item->replicate();
                $newItem->quote_id = $newQuote->id;
                $newItem->save();
            }

            Activity::log($newQuote, 'duplicated', 'Quote duplicated', ['original_id' => $quote->id]);

            return $newQuote;
        });
    }
}
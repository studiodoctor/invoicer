<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Activity;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(array $data, int $userId): Invoice
    {
        return DB::transaction(function () use ($data, $userId) {
            $settings = Setting::where('user_id', $userId)->first();

            $invoice = Invoice::create([
                'user_id' => $userId,
                'client_id' => $data['client_id'],
                'invoice_number' => $settings->getNextInvoiceNumber(),
                'reference' => $data['reference'] ?? null,
                'status' => InvoiceStatus::DRAFT,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'tax_rate' => $data['tax_rate'] ?? $settings->default_tax_rate,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_type_value' => $data['discount_type_value'] ?? 'percentage',
                'currency' => $data['currency'] ?? $settings->default_currency,
                'notes' => $data['notes'] ?? $settings->default_invoice_notes,
                'terms' => $data['terms'] ?? $settings->default_invoice_terms,
                'payment_instructions' => $data['payment_instructions'] ?? $settings->payment_instructions,
            ]);

            foreach ($data['items'] as $index => $item) {
                $invoice->items()->create([
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

            $invoice->load('items');
            $invoice->calculateTotals();
            $invoice->save();

            Activity::log($invoice, 'created', 'Invoice created');

            return $invoice;
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice->update([
                'client_id' => $data['client_id'],
                'reference' => $data['reference'] ?? null,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'tax_rate' => $data['tax_rate'] ?? 0,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_type_value' => $data['discount_type_value'] ?? 'percentage',
                'currency' => $data['currency'],
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'payment_instructions' => $data['payment_instructions'] ?? null,
            ]);

            $invoice->items()->delete();

            foreach ($data['items'] as $index => $item) {
                $invoice->items()->create([
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

            $invoice->load('items');
            $invoice->calculateTotals();
            $invoice->save();

            Activity::log($invoice, 'updated', 'Invoice updated');

            return $invoice;
        });
    }

    public function duplicate(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $settings = Setting::where('user_id', $invoice->user_id)->first();

            $newInvoice = $invoice->replicate();
            $newInvoice->invoice_number = $settings->getNextInvoiceNumber();
            $newInvoice->status = InvoiceStatus::DRAFT;
            $newInvoice->issue_date = now();
            $newInvoice->due_date = now()->addDays($settings->invoice_due_days);
            $newInvoice->amount_paid = 0;
            $newInvoice->amount_due = $invoice->total;
            $newInvoice->sent_at = null;
            $newInvoice->viewed_at = null;
            $newInvoice->paid_at = null;
            $newInvoice->payment_method = null;
            $newInvoice->transaction_id = null;
            $newInvoice->quote_id = null;
            $newInvoice->save();

            foreach ($invoice->items as $item) {
                $newItem = $item->replicate();
                $newItem->invoice_id = $newInvoice->id;
                $newItem->save();
            }

            Activity::log($newInvoice, 'duplicated', 'Invoice duplicated', ['original_id' => $invoice->id]);

            return $newInvoice;
        });
    }
}
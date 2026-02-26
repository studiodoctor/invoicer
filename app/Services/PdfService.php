<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateQuotePdf(Quote $quote): \Barryvdh\DomPDF\PDF
    {
        $quote->load(['client', 'items', 'user']);
        $settings = Setting::where('user_id', $quote->user_id)->first();

        return Pdf::loadView('pdf.quote', [
            'quote' => $quote,
            'settings' => $settings,
        ])
        ->setPaper('a4')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);
    }

    public function generateInvoicePdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load(['client', 'items', 'user', 'payments']);
        $settings = Setting::where('user_id', $invoice->user_id)->first();

        return Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'settings' => $settings,
        ])
        ->setPaper('a4')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);
    }

    public function generateReceiptPdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load(['client', 'items', 'user', 'payments']);
        $settings = Setting::where('user_id', $invoice->user_id)->first();

        return Pdf::loadView('pdf.receipt', [
            'invoice' => $invoice,
            'settings' => $settings,
        ])
        ->setPaper('a4')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);
    }

    public function saveQuotePdf(Quote $quote): string
    {
        $pdf = $this->generateQuotePdf($quote);
        $filename = "quotes/{$quote->quote_number}.pdf";
        Storage::put($filename, $pdf->output());
        return $filename;
    }

    public function saveInvoicePdf(Invoice $invoice): string
    {
        $pdf = $this->generateInvoicePdf($invoice);
        $filename = "invoices/{$invoice->invoice_number}.pdf";
        Storage::put($filename, $pdf->output());
        return $filename;
    }
}
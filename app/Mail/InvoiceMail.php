<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function envelope(): Envelope
    {
        $settings = $this->invoice->user->settings;

        return new Envelope(
            subject: "Invoice {$this->invoice->invoice_number} from {$settings->company_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'settings' => $this->invoice->user->settings,
            ],
        );
    }

    public function attachments(): array
    {
        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateInvoicePdf($this->invoice);

        return [
            Attachment::fromData(fn() => $pdf->output(), "{$this->invoice->invoice_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
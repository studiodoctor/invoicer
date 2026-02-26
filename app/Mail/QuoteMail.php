<?php

namespace App\Mail;

use App\Models\Quote;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quote
    ) {}

    public function envelope(): Envelope
    {
        $settings = $this->quote->user->settings;

        return new Envelope(
            subject: "Quote {$this->quote->quote_number} from {$settings->company_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote',
            with: [
                'quote' => $this->quote,
                'settings' => $this->quote->user->settings,
            ],
        );
    }

    public function attachments(): array
    {
        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateQuotePdf($this->quote);

        return [
            Attachment::fromData(fn() => $pdf->output(), "{$this->quote->quote_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .button { display: inline-block; background: #0284c7; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; }
        .details { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #0284c7;">Invoice {{ $invoice->invoice_number }}</h1>
        </div>

        <p>Dear {{ $invoice->client->contact_name ?: $invoice->client->company_name }},</p>

        <p>Please find attached invoice <strong>{{ $invoice->invoice_number }}</strong> for your reference.</p>

        <div class="details">
            <table style="width: 100%;">
                <tr><td style="padding: 8px 0; color: #6b7280;">Invoice Number:</td><td style="text-align: right;"><strong>{{ $invoice->invoice_number }}</strong></td></tr>
                <tr><td style="padding: 8px 0; color: #6b7280;">Issue Date:</td><td style="text-align: right;">{{ $invoice->issue_date->format('M d, Y') }}</td></tr>
                <tr><td style="padding: 8px 0; color: #6b7280;">Due Date:</td><td style="text-align: right;">{{ $invoice->due_date->format('M d, Y') }}</td></tr>
                <tr><td style="padding: 8px 0; color: #6b7280;">Amount Due:</td><td style="text-align: right;"><strong style="font-size: 18px; color: #0284c7;">R{{ number_format($invoice->amount_due, 2) }}</strong></td></tr>
            </table>
        </div>

        @if($invoice->payment_instructions)
        <h3>Payment Instructions</h3>
        <p style="white-space: pre-line; color: #6b7280;">{{ $invoice->payment_instructions }}</p>
        @endif

        <p>If you have any questions, please don't hesitate to contact us.</p>

        <p>Thank you for your business!</p>

        <p>Best regards,<br><strong>{{ $settings->company_name }}</strong></p>

        <div class="footer">
            <p>{{ $settings->company_name }}<br>{{ $settings->company_email }}<br>{{ $settings->company_phone }}</p>
        </div>
    </div>
</body>
</html>

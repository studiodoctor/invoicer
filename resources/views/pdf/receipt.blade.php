<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; line-height: 1.5; color: #374151; }
        .container { padding: 40px; }
        .header { text-align: center; margin-bottom: 40px; }
        .receipt-title { font-size: 28px; font-weight: bold; color: #22c55e; }
        .checkmark { width: 60px; height: 60px; margin: 0 auto 20px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .info-box { background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .info-row:last-child { border-bottom: none; }
        .total-row { font-size: 18px; font-weight: bold; background: #22c55e; color: white; padding: 15px 20px; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="width: 60px; height: 60px; margin: 0 auto 20px; background: #22c55e; border-radius: 50%; text-align: center; line-height: 60px; color: white; font-size: 30px;">✓</div>
            <div class="receipt-title">PAYMENT RECEIPT</div>
            <p style="color: #6b7280; margin-top: 10px;">Thank you for your payment!</p>
        </div>

        <div class="info-box">
            <table style="width: 100%;">
                <tr><td style="padding: 8px 0; color: #6b7280;">Receipt For:</td><td style="text-align: right; font-weight: 500;">{{ $invoice->client->company_name }}</td></tr>
                <tr><td style="padding: 8px 0; color: #6b7280;">Invoice Number:</td><td style="text-align: right; font-weight: 500;">{{ $invoice->invoice_number }}</td></tr>
                <tr><td style="padding: 8px 0; color: #6b7280;">Payment Date:</td><td style="text-align: right; font-weight: 500;">{{ $invoice->paid_at->format('M d, Y') }}</td></tr>
                <tr><td style="padding: 8px 0; color: #6b7280;">Payment Method:</td><td style="text-align: right; font-weight: 500;">{{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</td></tr>
            </table>
        </div>

        <div class="total-row">
            <table style="width: 100%;"><tr><td>Amount Paid</td><td style="text-align: right;">R{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td></tr></table>
        </div>

        <div style="margin-top: 40px; text-align: center; color: #6b7280; font-size: 11px;">
            <p><strong>{{ $settings->company_name }}</strong></p>
            <p>{{ $settings->company_email }} | {{ $settings->company_phone }}</p>
        </div>
    </div>
</body>
</html>

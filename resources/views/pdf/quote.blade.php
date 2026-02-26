<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; line-height: 1.5; color: #374151; }
        .container { padding: 40px; }
        .header table { width: 100%; }
        .logo { max-height: 60px; }
        .quote-title { font-size: 28px; font-weight: bold; color: #0284c7; text-align: right; }
        .quote-number { font-size: 14px; color: #6b7280; text-align: right; margin-top: 5px; }
        .info-table { width: 100%; margin: 30px 0; }
        .info-table td { vertical-align: top; }
        .label { font-size: 10px; text-transform: uppercase; color: #9ca3af; margin-bottom: 8px; }
        .company-name { font-weight: bold; font-size: 14px; color: #111827; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background-color: #f9fafb; padding: 12px; text-align: left; font-weight: 600; font-size: 10px; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        .items-table th:last-child { text-align: right; }
        .items-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        .items-table td:last-child { text-align: right; }
        .totals { width: 250px; margin-left: auto; }
        .totals-row { padding: 8px 0; }
        .totals-row.total { border-top: 2px solid #e5e7eb; font-weight: bold; font-size: 14px; margin-top: 8px; padding-top: 12px; }
        .signature { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .signature img { max-height: 60px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td style="width: 50%;">
                        @if($settings->logo_path && $settings->show_logo_on_documents)
                        <img src="{{ storage_path('app/public/' . $settings->logo_path) }}" alt="{{ $settings->company_name }}" class="logo">
                        @else
                        <div class="company-name" style="font-size: 20px; color: #0284c7;">{{ $settings->company_name }}</div>
                        @endif
                    </td>
                    <td style="width: 50%;">
                        <div class="quote-title">QUOTE</div>
                        <div class="quote-number">{{ $quote->quote_number }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <div class="label">From</div>
                    <div class="company-name">{{ $settings->company_name }}</div>
                    @if($settings->company_email)<div>{{ $settings->company_email }}</div>@endif
                </td>
                <td style="width: 50%;">
                    <div class="label">Prepared For</div>
                    <div class="company-name">{{ $quote->client->company_name }}</div>
                    <div>{{ $quote->client->email }}</div>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td><table>
                    <tr><td style="padding: 5px 20px 5px 0; font-size: 11px; color: #6b7280;">Issue Date:</td><td style="font-weight: 500;">{{ $quote->issue_date->format('M d, Y') }}</td></tr>
                    <tr><td style="padding: 5px 20px 5px 0; font-size: 11px; color: #6b7280;">Valid Until:</td><td style="font-weight: 500;">{{ $quote->expiry_date->format('M d, Y') }}</td></tr>
                </table></td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $item)
                <tr>
                    <td><div style="font-weight: 500;">{{ $item->description }}</div></td>
                    <td style="text-align: center;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align: right;">${{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right; font-weight: 500;">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table style="width: 100%;">
                <tr class="totals-row"><td>Subtotal</td><td style="text-align: right;">${{ number_format($quote->subtotal, 2) }}</td></tr>
                @if($quote->tax_amount > 0)<tr class="totals-row"><td>Tax ({{ $quote->tax_rate }}%)</td><td style="text-align: right;">${{ number_format($quote->tax_amount, 2) }}</td></tr>@endif
                <tr class="totals-row total"><td>Total</td><td style="text-align: right;">${{ number_format($quote->total, 2) }} {{ $quote->currency }}</td></tr>
            </table>
        </div>

        @if($quote->signature_data)
        <div class="signature">
            <div class="label">Accepted By</div>
            <img src="{{ $quote->signature_data }}" alt="Signature">
            <div style="margin-top: 8px;"><strong>{{ $quote->signer_name }}</strong></div>
            <div style="font-size: 11px; color: #6b7280;">Signed on {{ $quote->signed_at->format('M d, Y \a\t H:i') }}</div>
        </div>
        @endif
    </div>
</body>
</html>

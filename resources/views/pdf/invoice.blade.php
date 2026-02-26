<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; line-height: 1.5; color: #374151; }
        .container { padding: 40px; }
        .header { margin-bottom: 40px; }
        .header table { width: 100%; }
        .logo { max-height: 60px; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #0284c7; text-align: right; }
        .invoice-number { font-size: 14px; color: #6b7280; text-align: right; margin-top: 5px; }
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { vertical-align: top; padding: 0; }
        .label { font-size: 10px; text-transform: uppercase; color: #9ca3af; margin-bottom: 8px; letter-spacing: 0.5px; }
        .company-name { font-weight: bold; font-size: 14px; color: #111827; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background-color: #f9fafb; padding: 12px; text-align: left; font-weight: 600; font-size: 10px; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        .items-table th:last-child { text-align: right; }
        .items-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        .items-table td:last-child { text-align: right; }
        .totals { width: 250px; margin-left: auto; }
        .totals-row { padding: 8px 0; }
        .totals-row.total { border-top: 2px solid #e5e7eb; font-weight: bold; font-size: 14px; margin-top: 8px; padding-top: 12px; }
        .totals-row.due { background-color: #0284c7; color: white; padding: 12px; margin-top: 8px; font-size: 16px; }
        .notes { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .notes-title { font-size: 10px; text-transform: uppercase; color: #9ca3af; margin-bottom: 8px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-overdue { background-color: #fee2e2; color: #991b1b; }
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
                        <div class="invoice-title">INVOICE</div>
                        <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <div class="label">From</div>
                    <div class="company-name">{{ $settings->company_name }}</div>
                    @if($settings->company_address)<div>{{ $settings->company_address }}</div>@endif
                    @if($settings->company_city)<div>{{ $settings->company_city }}, {{ $settings->company_state }} {{ $settings->company_postal_code }}</div>@endif
                    @if($settings->company_email)<div>{{ $settings->company_email }}</div>@endif
                    @if($settings->vat_number)<div>VAT: {{ $settings->vat_number }}</div>@endif
                </td>
                <td style="width: 50%;">
                    <div class="label">Bill To</div>
                    <div class="company-name">{{ $invoice->client->company_name }}</div>
                    @if($invoice->client->contact_name)<div>{{ $invoice->client->contact_name }}</div>@endif
                    <div>{{ $invoice->client->email }}</div>
                    @if($invoice->client->address_line_1)<div>{{ $invoice->client->address_line_1 }}</div>@endif
                    @if($invoice->client->city)<div>{{ $invoice->client->city }}, {{ $invoice->client->state }} {{ $invoice->client->postal_code }}</div>@endif
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td>
                    <table>
                        <tr><td style="padding: 5px 20px 5px 0; font-size: 11px; color: #6b7280;">Issue Date:</td><td style="font-weight: 500;">{{ $invoice->issue_date->format('M d, Y') }}</td></tr>
                        <tr><td style="padding: 5px 20px 5px 0; font-size: 11px; color: #6b7280;">Due Date:</td><td style="font-weight: 500;">{{ $invoice->due_date->format('M d, Y') }}</td></tr>
                        @if($invoice->reference)<tr><td style="padding: 5px 20px 5px 0; font-size: 11px; color: #6b7280;">Reference:</td><td style="font-weight: 500;">{{ $invoice->reference }}</td></tr>@endif
                    </table>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    @if($invoice->status->value === 'paid')<span class="status-badge status-paid">PAID</span>@elseif($invoice->is_overdue)<span class="status-badge status-overdue">OVERDUE</span>@endif
                </td>
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
                @foreach($invoice->items as $item)
                <tr>
                    <td><div style="font-weight: 500; color: #111827;">{{ $item->description }}</div>@if($item->details)<div style="font-size: 11px; color: #6b7280; margin-top: 4px;">{{ $item->details }}</div>@endif</td>
                    <td style="text-align: center;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align: right;">${{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right; font-weight: 500;">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table style="width: 100%;">
                <tr class="totals-row"><td>Subtotal</td><td style="text-align: right;">${{ number_format($invoice->subtotal, 2) }}</td></tr>
                @if($invoice->discount_amount > 0)<tr class="totals-row"><td>Discount</td><td style="text-align: right;">-${{ number_format($invoice->discount_amount, 2) }}</td></tr>@endif
                @if($invoice->tax_amount > 0)<tr class="totals-row"><td>Tax ({{ $invoice->tax_rate }}%)</td><td style="text-align: right;">${{ number_format($invoice->tax_amount, 2) }}</td></tr>@endif
                <tr class="totals-row total"><td>Total</td><td style="text-align: right;">${{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td></tr>
                @if($invoice->amount_paid > 0)<tr class="totals-row"><td>Paid</td><td style="text-align: right;">-${{ number_format($invoice->amount_paid, 2) }}</td></tr>@endif
                @if($invoice->amount_due > 0)<tr class="totals-row due"><td>Amount Due</td><td style="text-align: right;">${{ number_format($invoice->amount_due, 2) }} {{ $invoice->currency }}</td></tr>@endif
            </table>
        </div>

        @if($invoice->payment_instructions && $settings->show_payment_instructions && $invoice->amount_due > 0)
        <div class="notes" style="background-color: #f9fafb; padding: 15px; border-radius: 4px; margin-top: 20px;">
            <div class="notes-title">Payment Instructions</div>
            <div style="color: #6b7280; font-size: 11px;">{!! nl2br(e($invoice->payment_instructions)) !!}</div>
        </div>
        @endif

        @if($invoice->notes || $invoice->terms)
        <div class="notes">
            @if($invoice->notes)<div style="margin-bottom: 15px;"><div class="notes-title">Notes</div><div style="color: #6b7280; font-size: 11px;">{!! nl2br(e($invoice->notes)) !!}</div></div>@endif
            @if($invoice->terms)<div><div class="notes-title">Terms & Conditions</div><div style="color: #6b7280; font-size: 11px;">{!! nl2br(e($invoice->terms)) !!}</div></div>@endif
        </div>
        @endif
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #374151;
        }
        .container {
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .logo {
            max-height: 60px;
            max-width: 200px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: {{ $settings->primary_color }};
            text-align: right;
        }
        .invoice-number {
            font-size: 14px;
            color: #6b7280;
            text-align: right;
            margin-top: 5px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-block {
            width: 48%;
        }
        .info-block h4 {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .info-block p {
            margin-bottom: 3px;
        }
        .company-name {
            font-weight: bold;
            font-size: 14px;
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f9fafb;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
        }
        th:last-child {
            text-align: right;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        td:last-child {
            text-align: right;
        }
        .item-description {
            font-weight: 500;
            color: #111827;
        }
        .item-details {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .totals-row.total {
            border-top: 2px solid #e5e7eb;
            font-weight: bold;
            font-size: 14px;
            color: #111827;
            margin-top: 8px;
            padding-top: 12px;
        }
        .totals-row.due {
            background-color: {{ $settings->primary_color }};
            color: white;
            padding: 12px;
            margin: 8px -12px -8px;
            font-size: 16px;
        }
        .notes-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .notes-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 8px;
        }
        .notes-content {
            color: #6b7280;
            font-size: 11px;
        }
        .payment-info {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .footer {
            position: fixed;
            bottom: 40px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <table style="margin-bottom: 40px; border: none;">
            <tr>
                <td style="border: none; padding: 0; width: 50%;">
                    @if($settings->logo_path && $settings->show_logo_on_documents)
                    <img src="{{ storage_path('app/public/' . $settings->logo_path) }}" alt="{{ $settings->company_name }}" class="logo">
                    @else
                    <div class="company-name" style="font-size: 20px; color: {{ $settings->primary_color }};">
                        {{ $settings->company_name }}
                    </div>
                    @endif
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                </td>
            </tr>
        </table>

        <table style="margin-bottom: 30px; border: none;">
            <tr>
                <td style="border: none; padding: 0; width: 50%; vertical-align: top;">
                    <h4 style="font-size: 10px; text-transform: uppercase; color: #9ca3af; margin-bottom: 8px;">From</h4>
                    <p class="company-name">{{ $settings->company_name }}</p>
                    @if($settings->company_address)
                    <p>{{ $settings->company_address }}</p>
                    @endif
                    @if($settings->company_city || $settings->company_state || $settings->company_postal_code)
                    <p>{{ $settings->company_city }}{{ $settings->company_state ? ', ' . $settings->company_state : '' }} {{ $settings->company_postal_code }}</p>
                    @endif
                    @if($settings->company_email)
                    <p>{{ $settings->company_email }}</p>
                    @endif
                    @if($settings->vat_number)
                    <p>VAT: {{ $settings->vat_number }}</p>
                    @endif
                </td>
                <td style="border: none; padding: 0; width: 50%; vertical-align: top;">
                    <h4 style="font-size: 10px; text-transform: uppercase; color: #9ca3af; margin-bottom: 8px;">Bill To</h4>
                    <p class="company-name">{{ $invoice->client->company_name }}</p>
                    @if($invoice->client->contact_name)
                    <p>{{ $invoice->client->contact_name }}</p>
                    @endif
                    <p>{{ $invoice->client->email }}</p>
                    @if($invoice->client->address_line_1)
                    <p>{{ $invoice->client->address_line_1 }}</p>
                    @endif
                    @if($invoice->client->city || $invoice->client->state || $invoice->client->postal_code)
                    <p>{{ $invoice->client->city }}{{ $invoice->client->state ? ', ' . $invoice->client->state : '' }} {{ $invoice->client->postal_code }}</p>
                    @endif
                    @if($invoice->client->vat_number)
                    <p>VAT: {{ $invoice->client->vat_number }}</p>
                    @endif
                </td>
            </tr>
        </table>

        <table style="margin-bottom: 30px; border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    <table style="width: auto; border: none;">
                        <tr>
                            <td style="border: none; padding: 5px 20px 5px 0; font-size: 11px; color: #6b7280;">Issue Date:</td>
                            <td style="border: none; padding: 5px 0; font-weight: 500;">{{ $invoice->issue_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 5px 20px 5px 0; font-size: 11px; color: #6b7280;">Due Date:</td>
                            <td style="border: none; padding: 5px 0; font-weight: 500;">{{ $invoice->due_date->format('M d, Y') }}</td>
                        </tr>
                        @if($invoice->reference)
                        <tr>
                            <td style="border: none; padding: 5px 20px 5px 0; font-size: 11px; color: #6b7280;">Reference:</td>
                            <td style="border: none; padding: 5px 0; font-weight: 500;">{{ $invoice->reference }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
                <td style="border: none; padding: 0; text-align: right; vertical-align: top;">
                    @if($invoice->status->value === 'paid')
                    <span class="status-badge status-paid">PAID</span>
                    @elseif($invoice->is_overdue)
                    <span class="status-badge status-overdue">OVERDUE</span>
                    @else
                    <span class="status-badge status-pending">{{ strtoupper($invoice->status->label()) }}</span>
                    @endif
                </td>
            </tr>
        </table>

        <table>
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
                    <td>
                        <div class="item-description">{{ $item->description }}</div>
                        @if($item->details)
                        <div class="item-details">{{ $item->details }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align: right;">${{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right; font-weight: 500;">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>${{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if($invoice->discount_amount > 0)
            <div class="totals-row">
                <span>Discount</span>
                <span>-${{ number_format($invoice->discount_amount, 2) }}</span>
            </div>
            @endif
            @if($invoice->tax_amount > 0)
            <div class="totals-row">
                <span>Tax ({{ $invoice->tax_rate }}%)</span>
                <span>${{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
            @endif
            <div class="totals-row total">
                <span>Total</span>
                <span>${{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</span>
            </div>
            @if($invoice->amount_paid > 0)
            <div class="totals-row">
                <span>Amount Paid</span>
                <span>-${{ number_format($invoice->amount_paid, 2) }}</span>
            </div>
            @endif
            @if($invoice->amount_due > 0)
            <div class="totals-row due">
                <span>Amount Due</span>
                <span>${{ number_format($invoice->amount_due, 2) }} {{ $invoice->currency }}</span>
            </div>
            @endif
        </div>

        @if($invoice->payment_instructions && $settings->show_payment_instructions && $invoice->amount_due > 0)
        <div class="payment-info">
            <div class="notes-title">Payment Instructions</div>
            <div class="notes-content">{!! nl2br(e($invoice->payment_instructions)) !!}</div>
        </div>
        @endif

        @if($invoice->notes || $invoice->terms)
        <div class="notes-section">
            @if($invoice->notes)
            <div style="margin-bottom: 15px;">
                <div class="notes-title">Notes</div>
                <div class="notes-content">{!! nl2br(e($invoice->notes)) !!}</div>
            </div>
            @endif
            @if($invoice->terms)
            <div>
                <div class="notes-title">Terms & Conditions</div>
                <div class="notes-content">{!! nl2br(e($invoice->terms)) !!}</div>
            </div>
            @endif
        </div>
        @endif

        <div class="footer">
            {{ $settings->company_name }} | {{ $settings->company_email }} | {{ $settings->company_phone }}
        </div>
    </div>
</body>
</html>
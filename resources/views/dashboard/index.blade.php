@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Dashboard
            </h2>
        </div>
        <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
            <a href="{{ route('quotes.create') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                New Quote
            </a>
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                New Invoice
            </a>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Revenue</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                ${{ number_format($stats['total_revenue'], 2) }}
            </dd>
        </div>

        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Pending Invoices</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                {{ $stats['pending_invoices']['count'] }}
            </dd>
            <dd class="mt-1 text-sm text-gray-500">
                ${{ number_format($stats['pending_invoices']['total'], 2) }} outstanding
            </dd>
        </div>

        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Overdue Invoices</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600">
                {{ $stats['overdue_invoices']['count'] }}
            </dd>
            <dd class="mt-1 text-sm text-gray-500">
                ${{ number_format($stats['overdue_invoices']['total'], 2) }} overdue
            </dd>
        </div>

        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Quote Conversion</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                {{ $stats['conversion_rate'] }}%
            </dd>
            <dd class="mt-1 text-sm text-gray-500">
                {{ $stats['pending_quotes']['count'] }} quotes pending
            </dd>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="overflow-hidden rounded-lg bg-white p-6 shadow">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Revenue Overview</h3>
            <div class="mt-4" style="height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div class="overflow-hidden rounded-lg bg-white p-6 shadow">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Invoice Status</h3>
                <div class="mt-4" style="height: 200px;">
                    <canvas id="invoiceStatusChart"></canvas>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white p-6 shadow">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Quote Status</h3>
                <div class="mt-4" style="height: 200px;">
                    <canvas id="quoteStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Items -->
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Recent Invoices</h3>
                    <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">View all</a>
                </div>
                <div class="mt-6 flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @forelse($recentInvoices as $invoice)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-gray-900">
                                        <a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a>
                                    </p>
                                    <p class="truncate text-sm text-gray-500">{{ $invoice->client->company_name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">${{ number_format($invoice->total, 2) }}</p>
                                    @include('components.status-badge', ['status' => $invoice->status])
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-4 text-center text-sm text-gray-500">No invoices yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Recent Quotes</h3>
                    <a href="{{ route('quotes.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">View all</a>
                </div>
                <div class="mt-6 flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @forelse($recentQuotes as $quote)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-gray-900">
                                        <a href="{{ route('quotes.show', $quote) }}">{{ $quote->quote_number }}</a>
                                    </p>
                                    <p class="truncate text-sm text-gray-500">{{ $quote->client->company_name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">${{ number_format($quote->total, 2) }}</p>
                                    @include('components.status-badge', ['status' => $quote->status, 'type' => 'quote'])
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-4 text-center text-sm text-gray-500">No quotes yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($revenueChart['labels']),
            datasets: [{
                label: 'Revenue',
                data: @json($revenueChart['data']),
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14, 165, 233, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(value) { return '$' + value.toLocaleString(); } }
                }
            }
        }
    });

    const invoiceStatusCtx = document.getElementById('invoiceStatusChart').getContext('2d');
    new Chart(invoiceStatusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($invoiceStatusChart['labels']),
            datasets: [{
                data: @json($invoiceStatusChart['data']),
                backgroundColor: @json($invoiceStatusChart['colors'])
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8 } } }
        }
    });

    const quoteStatusCtx = document.getElementById('quoteStatusChart').getContext('2d');
    new Chart(quoteStatusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($quoteStatusChart['labels']),
            datasets: [{
                data: @json($quoteStatusChart['data']),
                backgroundColor: @json($quoteStatusChart['colors'])
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8 } } }
        }
    });
});
</script>
@endpush

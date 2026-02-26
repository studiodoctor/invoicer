<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\QuoteStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(
        protected int $userId
    ) {}

    public function getStats(): array
    {
        return [
            'total_clients' => $this->getTotalClients(),
            'total_quotes' => $this->getTotalQuotes(),
            'total_invoices' => $this->getTotalInvoices(),
            'total_revenue' => $this->getTotalRevenue(),
            'pending_invoices' => $this->getPendingInvoices(),
            'overdue_invoices' => $this->getOverdueInvoices(),
            'pending_quotes' => $this->getPendingQuotes(),
            'conversion_rate' => $this->getQuoteConversionRate(),
        ];
    }

    public function getTotalClients(): int
    {
        return Client::where('user_id', $this->userId)->count();
    }

    public function getTotalQuotes(): int
    {
        return Quote::where('user_id', $this->userId)->count();
    }

    public function getTotalInvoices(): int
    {
        return Invoice::where('user_id', $this->userId)->count();
    }

    public function getTotalRevenue(): float
    {
        return Invoice::where('user_id', $this->userId)
            ->where('status', InvoiceStatus::PAID)
            ->sum('total');
    }

    public function getPendingInvoices(): array
    {
        $invoices = Invoice::where('user_id', $this->userId)
            ->whereIn('status', [InvoiceStatus::SENT, InvoiceStatus::VIEWED, InvoiceStatus::PARTIAL])
            ->get();

        return [
            'count' => $invoices->count(),
            'total' => $invoices->sum('amount_due'),
        ];
    }

    public function getOverdueInvoices(): array
    {
        $invoices = Invoice::where('user_id', $this->userId)
            ->where('status', InvoiceStatus::OVERDUE)
            ->get();

        return [
            'count' => $invoices->count(),
            'total' => $invoices->sum('amount_due'),
        ];
    }

    public function getPendingQuotes(): array
    {
        $quotes = Quote::where('user_id', $this->userId)
            ->whereIn('status', [QuoteStatus::SENT, QuoteStatus::VIEWED])
            ->get();

        return [
            'count' => $quotes->count(),
            'total' => $quotes->sum('total'),
        ];
    }

    public function getQuoteConversionRate(): float
    {
        $totalQuotes = Quote::where('user_id', $this->userId)->count();
        $convertedQuotes = Quote::where('user_id', $this->userId)
            ->whereIn('status', [QuoteStatus::SIGNED, QuoteStatus::CONVERTED])
            ->count();

        if ($totalQuotes === 0) {
            return 0;
        }

        return round(($convertedQuotes / $totalQuotes) * 100, 1);
    }

    public function getRevenueChart(int $months = 12): array
    {
        $data = Invoice::where('user_id', $this->userId)
            ->where('status', InvoiceStatus::PAID)
            ->where('paid_at', '>=', now()->subMonths($months))
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as month, SUM(total) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->format('M Y');
            $monthData = $data->firstWhere('month', $month);
            $values[] = $monthData ? (float) $monthData->revenue : 0;
        }

        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }

    public function getInvoiceStatusChart(): array
    {
        $statuses = Invoice::where('user_id', $this->userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return [
            'labels' => array_map(fn($s) => InvoiceStatus::from($s)->label(), array_keys($statuses)),
            'data' => array_values($statuses),
            'colors' => array_map(fn($s) => $this->getStatusColor(InvoiceStatus::from($s)->color()), array_keys($statuses)),
        ];
    }

    public function getQuoteStatusChart(): array
    {
        $statuses = Quote::where('user_id', $this->userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return [
            'labels' => array_map(fn($s) => QuoteStatus::from($s)->label(), array_keys($statuses)),
            'data' => array_values($statuses),
            'colors' => array_map(fn($s) => $this->getStatusColor(QuoteStatus::from($s)->color()), array_keys($statuses)),
        ];
    }

    public function getRecentInvoices(int $limit = 5)
    {
        return Invoice::where('user_id', $this->userId)
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentQuotes(int $limit = 5)
    {
        return Quote::where('user_id', $this->userId)
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopClients(int $limit = 5)
    {
        return Client::where('user_id', $this->userId)
            ->withSum(['invoices as total_revenue' => function ($query) {
                $query->where('status', InvoiceStatus::PAID);
            }], 'total')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function getStatusColor(string $color): string
    {
        return match($color) {
            'gray' => '#6b7280',
            'blue' => '#3b82f6',
            'purple' => '#8b5cf6',
            'green' => '#22c55e',
            'red' => '#ef4444',
            'orange' => '#f97316',
            'yellow' => '#eab308',
            'teal' => '#14b8a6',
            default => '#6b7280',
        };
    }
}
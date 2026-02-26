<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dashboardService = new DashboardService(auth()->id());

        $stats = $dashboardService->getStats();
        $revenueChart = $dashboardService->getRevenueChart();
        $invoiceStatusChart = $dashboardService->getInvoiceStatusChart();
        $quoteStatusChart = $dashboardService->getQuoteStatusChart();
        $recentInvoices = $dashboardService->getRecentInvoices();
        $recentQuotes = $dashboardService->getRecentQuotes();
        $topClients = $dashboardService->getTopClients();

        return view('dashboard.index', compact(
            'stats',
            'revenueChart',
            'invoiceStatusChart',
            'quoteStatusChart',
            'recentInvoices',
            'recentQuotes',
            'topClients'
        ));
    }
}
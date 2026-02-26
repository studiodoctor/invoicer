<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Http\Requests\InvoiceRequest;
use App\Mail\InvoiceMail;
use App\Models\Activity;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected PdfService $pdfService
    ) {}

    public function index(Request $request)
    {
        $query = Invoice::where('user_id', auth()->id())
            ->with('client');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = Client::where('user_id', auth()->id())->orderBy('company_name')->get();

        return view('invoices.index', compact('invoices', 'clients'));
    }

    public function create()
    {
        $clients = Client::where('user_id', auth()->id())
            ->active()
            ->orderBy('company_name')
            ->get();

        $settings = auth()->user()->settings;

        return view('invoices.create', compact('clients', 'settings'));
    }

    public function store(InvoiceRequest $request)
    {
        $invoice = $this->invoiceService->create($request->validated(), auth()->id());

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['client', 'items', 'payments', 'quote']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if (!in_array($invoice->status, [InvoiceStatus::DRAFT])) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'This invoice cannot be edited.');
        }

        $clients = Client::where('user_id', auth()->id())
            ->active()
            ->orderBy('company_name')
            ->get();

        $invoice->load('items');
        $settings = auth()->user()->settings;

        return view('invoices.edit', compact('invoice', 'clients', 'settings'));
    }

    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $invoice = $this->invoiceService->update($invoice, $request->validated());

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);

        $invoice->delete();

        Activity::log($invoice, 'deleted', 'Invoice deleted');

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function send(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));

        $invoice->markAsSent();

        Activity::log($invoice, 'sent', 'Invoice sent to client', ['email' => $invoice->client->email]);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice sent successfully.');
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $pdf = $this->pdfService->generateInvoicePdf($invoice);

        return $pdf->download("{$invoice->invoice_number}.pdf");
    }

    public function preview(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $pdf = $this->pdfService->generateInvoicePdf($invoice);

        return $pdf->stream("{$invoice->invoice_number}.pdf");
    }

    public function receipt(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        if ($invoice->status !== InvoiceStatus::PAID) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'Receipt is only available for paid invoices.');
        }

        $pdf = $this->pdfService->generateReceiptPdf($invoice);

        return $pdf->download("{$invoice->invoice_number}-receipt.pdf");
    }

    public function duplicate(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $newInvoice = $this->invoiceService->duplicate($invoice);

        return redirect()
            ->route('invoices.edit', $newInvoice)
            ->with('success', 'Invoice duplicated successfully.');
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->amount_due,
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $payment = $invoice->recordPayment(
            $request->amount,
            $request->payment_method,
            $request->transaction_id,
            $request->notes
        );

        Activity::log($invoice, 'payment_recorded', 'Payment recorded', [
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully.');
    }

    public function markAsPaid(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $invoice->recordPayment(
            $invoice->amount_due,
            'manual',
            null,
            'Marked as paid manually'
        );

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice marked as paid.');
    }
}
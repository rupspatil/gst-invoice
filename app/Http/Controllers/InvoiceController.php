<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Helpers\InvoiceHelper;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Support\Facades\DB;
use PDF;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    // -----------------------------
    // List invoices
    // -----------------------------
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::with('customer')->latest();

            return DataTables::of($invoices)
                ->addIndexColumn()
                ->addColumn('customer', fn ($row) => $row->customer->name ?? '-')
                ->editColumn('invoice_date', function ($row) {
                    return Carbon::parse($row->invoice_date)->format('d-m-Y');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('invoices.pdf', $row->id) . '" class="btn btn-sm btn-warning">PDF</a>
                        <a href="#" data-id="' . $row->id . '" class="btn btn-sm btn-danger deleteBtn">Delete</a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('invoices.index');
    }

    // -----------------------------
    // Create form
    // -----------------------------
    public function create()
    {
        return view('invoices.create', [
            'customers'      => Customer::all(),
            'items'          => Item::all(),
            'invoice_number' => InvoiceHelper::generateNumber(),
        ]);
    }

    // -----------------------------
    // Store invoice
    // -----------------------------
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'           => 'required|exists:customers,id',
            'invoice_date'          => 'required|date',
            'items'                 => 'required|array|min:1',

            'items.*.item_id'       => 'nullable|exists:items,id',
            'items.*.description'   => 'required|string',
            'items.*.hsn_sac'       => 'nullable|string',

            'items.*.qty'           => 'required|numeric|min:1',
            'items.*.price'         => 'required|numeric|min:0',
            'items.*.tax_percent'   => 'required|numeric|min:0',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $companyState = config('app.company_state', env('COMPANY_STATE'));

        DB::transaction(function () use ($validated, $customer, $companyState) {

            $subTotal = 0;
            $taxTotal = 0;
            $items = [];

            // -----------------------------
            // Calculate totals
            // -----------------------------
            foreach ($validated['items'] as $row) {

                $lineSubtotal = $row['qty'] * $row['price'];
                $taxAmount    = ($lineSubtotal * $row['tax_percent']) / 100;

                if ($customer->state === $companyState) {
                    $cgst = $taxAmount / 2;
                    $sgst = $taxAmount / 2;
                    $igst = 0;
                } else {
                    $cgst = 0;
                    $sgst = 0;
                    $igst = $taxAmount;
                }

                $lineTotal = $lineSubtotal + $taxAmount;

                $subTotal += $lineSubtotal;
                $taxTotal += $taxAmount;

                $items[] = array_merge($row, [
                    'line_subtotal' => $lineSubtotal,
                    'tax_amount'    => $taxAmount,
                    'cgst'          => $cgst,
                    'sgst'          => $sgst,
                    'igst'          => $igst,
                    'line_total'    => $lineTotal,
                ]);
            }

            // -----------------------------
            // Create invoice
            // -----------------------------
            $invoice = Invoice::create([
                'customer_id'   => $validated['customer_id'],
                'invoice_date'  => $validated['invoice_date'],
                'invoice_number'=> InvoiceHelper::generateNumber(),
                'sub_total'     => $subTotal,
                'tax_total'     => $taxTotal,
                'grand_total'   => $subTotal + $taxTotal,
            ]);

            // -----------------------------
            // Insert items
            // -----------------------------
            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id'    => $invoice->id,
                    'item_id'       => $item['item_id'],
                    'description'   => $item['description'],
                    'hsn'           => $item['hsn_sac'],
                    'quantity'      => $item['qty'],
                    'unit_price'    => $item['price'],
                    'tax_percent'   => $item['tax_percent'],
                    'cgst'          => $item['cgst'],
                    'sgst'          => $item['sgst'],
                    'igst'          => $item['igst'],
                    'tax_amount'    => $item['tax_amount'],
                    'line_subtotal' => $item['line_subtotal'],
                    'line_total'    => $item['line_total'],
                ]);
            }
        });

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice created successfully');
    }

    // -----------------------------
    // Show invoice
    // -----------------------------
    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items');
        return view('invoices.show', compact('invoice'));
    }

    // -----------------------------
    // Download PDF
    // -----------------------------
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load('customer', 'items');

        return PDF::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('A4', 'portrait')
            ->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    // -----------------------------
    // Delete invoice
    // -----------------------------
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            InvoiceItem::where('invoice_id', $id)->delete();
            Invoice::where('id', $id)->delete();
        });

        return response()->json(['success' => true]);
    }
}

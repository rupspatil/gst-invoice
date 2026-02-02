<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; }
        th { background: #f5f5f5; }
    </style>
</head>

<body>

<h2 style="text-align:center;">TAX INVOICE</h2>

<!-- Company Details -->
<table>
    <tr>
        <td style="width:60%;">
            <strong>Company Name</strong><br>
            Address Line 1<br>
            Address Line 2<br>
            GSTIN: XXXXXXXXXXXXXXX<br>
            Phone: XXXXXXXX<br>
            Email: your@mail.com
        </td>

        <td>
            <strong>Invoice No:</strong> {{ $invoice->invoice_number }} <br>
            <strong>Date:</strong> {{ $invoice->invoice_date->format('d-m-Y') }} <br>
        </td>
    </tr>
</table>

<!-- Customer -->
<table>
    <tr>
        <td>
            <strong>Bill To:</strong><br>
            {{ $invoice->customer->name ?? '' }}<br>
            {{ $invoice->customer->address ?? '' }}<br>
            GSTIN: {{ $invoice->customer->gstin ?? 'N/A' }}
        </td>
    </tr>
</table>

<!-- Items Table -->
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>HSN</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Taxable Value</th>
            <th>CGST</th>
            <th>SGST</th>
            <th>IGST</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($invoice->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->hsn ?? '-' }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->unit_price, 2) }}</td>

            <!-- Taxable Value -->
            <td>{{ number_format($item->line_subtotal, 2) }}</td>

            <!-- CGST/SGST/IGST -->
            <td>{{ number_format($item->cgst, 2) }}</td>
            <td>{{ number_format($item->sgst, 2) }}</td>
            <td>{{ number_format($item->igst, 2) }}</td>

            <td>{{ number_format($item->line_total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Totals -->
<table>
    <tr>
        <td style="width:70%;"><strong>Sub Total</strong></td>
        <td>{{ number_format($invoice->sub_total, 2) }}</td>
    </tr>

    <tr>
        <td><strong>Total Tax</strong></td>
        <td>{{ number_format($invoice->tax_total, 2) }}</td>
    </tr>

    <tr>
        <td><strong>Round Off</strong></td>
        <td>{{ number_format($invoice->round_off ?? 0, 2) }}</td>
    </tr>

    <tr>
        <td><strong>Grand Total</strong></td>
        <td><strong>{{ number_format($invoice->grand_total, 2) }}</strong></td>
    </tr>
</table>

<p><strong>Notes:</strong><br>
{{ $invoice->notes ?? 'Thank you for your business!' }}
</p>

</body>
</html>

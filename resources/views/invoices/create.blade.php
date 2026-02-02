@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')

<div class="card shadow mb-4">
    <div class="card-header">
        <h4 class="m-0 font-weight-bold text-primary">Create Invoice</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-4">
                    <label><b>Customer</b></label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">Select Customer</option>
                        @foreach ($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label><b>Invoice Date</b></label>
                    <input type="date" name="invoice_date" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label><b>Invoice Number</b></label>
                    <input type="text" class="form-control" value="{{ $invoice_number }}" readonly>
                </div>
            </div>

            <hr>

            <table class="table table-bordered" id="itemsTable">
                <thead class="thead-light">
                    <tr>
                        <th>Description</th>
                        <th width="10%">Qty</th>
                        <th width="15%">Price</th>
                        <th width="10%">Tax %</th>
                        <th width="15%">Line Total</th>
                        <th width="5%"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            <select name="items[0][item_id]" class="form-control item-select" required>
                                <option value="">Select Item</option>
                                @foreach ($items as $it)
                                <option value="{{ $it->id }}"
                                    data-name="{{ $it->name }}"
                                    data-price="{{ $it->price }}"
                                    data-tax="{{ $it->tax_percent ?? 0 }}"
                                    data-hsn="{{ $it->hsn_sac ?? '' }}">>
                                    {{ $it->name }} ({{ $it->sku }})
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <td><input type="number" name="items[0][qty]" class="form-control item-qty" required></td>
                        <td><input type="number" name="items[0][price]" class="form-control item-price" readonly></td>
                        <td><input type="number" name="items[0][tax_percent]" class="form-control item-tax" readonly></td>

                        <!-- FIX: line_total stored correctly -->
                        <td><input type="text" name="items[0][line_total]" class="form-control item-total" readonly></td>

                        <!-- FIX: description sent properly -->
                        <input type="hidden" name="items[0][description]" class="item-desc">
                        <input type="hidden" name="items[0][hsn_sac]" class="hsn_sac">

                        <td><button type="button" class="btn btn-danger removeRow">X</button></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-success mb-3" id="addRow">+ Add Item</button>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <label><b>Sub Total</b></label>
                    <input type="text" id="sub_total" class="form-control" readonly>
                </div>

                <div class="col-md-4">
                    <label><b>Total Tax</b></label>
                    <input type="text" id="tax_total" class="form-control" readonly>
                </div>

                <div class="col-md-4">
                    <label><b>Grand Total</b></label>
                    <input type="text" id="grand_total" class="form-control" readonly>
                </div>
            </div>

            <br>

            <button type="submit" class="btn btn-primary btn-lg">Save Invoice</button>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
let i = 1;

$("#addRow").click(function() {
    let html = `
        <tr>
            <td>
                <select name="items[${i}][item_id]" class="form-control item-select" required>
                    <option value="">Select Item</option>
                    @foreach ($items as $it)
                        <option value="{{ $it->id }}"
                                    data-name="{{ $it->name }}"
                                    data-price="{{ $it->price }}"
                                    data-tax="{{ $it->tax_percent ?? 0 }}"
                                    data-hsn="{{ $it->hsn_sac ?? '' }}">>
                                    {{ $it->name }} ({{ $it->sku }})
                        </option>
                    @endforeach
                </select>
            </td>

            <td><input type="number" name="items[${i}][qty]" class="form-control item-qty" required></td>
            <td><input type="number" name="items[${i}][price]" class="form-control item-price" readonly></td>
            <td><input type="number" name="items[${i}][tax_percent]" class="form-control item-tax" readonly></td>
            <td><input type="text" name="items[${i}][line_total]" class="form-control item-total" readonly></td>
            <input type="hidden" name="items[${i}][description]" class="item-desc">
            <input type="hidden" name="items[${i}][hsn_sac]" class="hsn_sac">
            <td><button type="button" class="btn btn-danger removeRow">X</button></td>
        </tr> `;
    $("#itemsTable tbody").append(html);
    i++;
});

// Item select change → auto fill price, tax, description, total
$(document).on("change", ".item-select", function() {
    let row = $(this).closest("tr");

    let price = parseFloat($(this).find(":selected").data("price")) || 0;
    let tax = parseFloat($(this).find(":selected").data("tax")) || 0;
    let name = $(this).find(":selected").data("name") || "";
    let hsn = $(this).find(":selected").data("hsn") || "";
    let qty = parseFloat(row.find(".item-qty").val()) || 0;

    let line = qty * price;
    let taxAmount = line * (tax / 100);
    let total = line + taxAmount;

    row.find(".item-price").val(price);
    row.find(".item-tax").val(tax);
    row.find(".item-total").val(total.toFixed(2));

    row.find(".item-desc").val(name);
    row.find(".hsn_sac").val(hsn);

    calculateTotals();
});

// qty change update
$(document).on("input", ".item-qty", function() {
    let row = $(this).closest("tr");

    let qty = parseFloat(row.find(".item-qty").val()) || 0;
    let price = parseFloat(row.find(".item-price").val()) || 0;
    let tax = parseFloat(row.find(".item-tax").val()) || 0;

    let line = qty * price;
    let taxAmount = line * (tax / 100);
    let total = line + taxAmount;

    row.find(".item-total").val(total.toFixed(2));
    row.find("input[name*='line_total']").val(total.toFixed(2));

    calculateTotals();
});

// Remove row
$(document).on("click", ".removeRow", function() {
    $(this).closest("tr").remove();
    calculateTotals();
});

// Totals
function calculateTotals() {
    let sub = 0, tax = 0, grand = 0;

    $("#itemsTable tbody tr").each(function() {
        let qty = parseFloat($(this).find(".item-qty").val()) || 0;
        let price = parseFloat($(this).find(".item-price").val()) || 0;
        let taxRate = parseFloat($(this).find(".item-tax").val()) || 0;

        let line = qty * price;
        let taxAmount = line * (taxRate / 100);
        let total = line + taxAmount;

        sub += line;
        tax += taxAmount;
        grand += total;
    });

    $("#sub_total").val(sub.toFixed(2));
    $("#tax_total").val(tax.toFixed(2));
    $("#grand_total").val(grand.toFixed(2));
}
</script>
@endpush

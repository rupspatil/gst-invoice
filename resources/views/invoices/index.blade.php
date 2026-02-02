@extends('layouts.app')

@section('title','Invoices')

@section('content')
<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between">
    <h6 class="m-0 font-weight-bold text-primary">Invoices</h6>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">New Invoice</a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="invoicesTable">
        <thead>
          <tr><th>ID</th><th>Invoice No</th><th>Customer</th><th>Date</th><th>Grand Total</th><th>Action</th></tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).on('click', '.deleteBtn', function (e) {
    e.preventDefault(); 

    if (!confirm("Are you sure you want to delete this invoice?")) {
        return;
    }

    let id = $(this).data('id');

    $.ajax({
        url: "/invoices/" + id,
        type: "DELETE",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function (response) {
            if (response.success) {
                $('#invoicesTable').DataTable().ajax.reload();
                alert("Invoice Deleted Successfully");
            }
        },
        error: function (xhr) {
            alert("Error deleting invoice");
        }
    });
});

$(function(){
  $('#invoicesTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("invoices.index") }}',
    columns: [
      { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
      { data: 'invoice_number', name: 'invoice_number' },
      { data: 'customer', name: 'customer' },
      { data: 'invoice_date', name: 'invoice_date' },
      { data: 'grand_total', name: 'grand_total' },
      { data: 'action', name: 'action', orderable: false, searchable: false }
    ]
  });
});
</script>
@endpush
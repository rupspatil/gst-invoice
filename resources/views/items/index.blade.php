@extends('layouts.app')

@section('title','Products & Services')

@section('content')

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between">
        <h4 class="m-0 font-weight-bold text-primary">Products & Services</h4>
        <a href="{{ route('items.create') }}" class="btn btn-success">+ Add New</a>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped" id="itemsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>GST %</th>
                    <th>Stock</th>
                    <th width="150px">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    $('#itemsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('items.index') }}",
        columns: [
            { data: 'id' },
            { data: 'sku' },
            { data: 'name' },
            { data: 'type' },
            { data: 'price' },
            { data: 'tax_percent' },
            { data: 'stock' },
            { data: 'action', orderable:false, searchable:false },
        ]
    });
});
</script>
@endpush

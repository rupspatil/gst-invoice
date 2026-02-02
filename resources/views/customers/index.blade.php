@extends('layouts.app')

@section('content')
<div>
    <h2 class="mb-3">Customers</h2>

    <a href="{{ route('customers.create') }}" class="btn btn-primary mb-3">Add Customer</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>GSTIN</th>
                <th>Name</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($customers as $customer)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $customer->gstin }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->company }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone }}</td>
                <td>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('customers.destroy', $customer->id) }}"
                          method="POST"
                          style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>
@endsection
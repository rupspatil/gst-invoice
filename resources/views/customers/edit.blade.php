@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Customer</h2>

    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $customer->name ?? '' }}" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $customer->email ?? '' }}">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $customer->phone ?? '' }}">
        </div>

        <div class="form-group">
            <label>GSTIN</label>
            <input type="text" name="gstin" class="form-control" value="{{ $customer->gstin ?? '' }}">
        </div>

<div class="form-group">
    <label>Company</label>
    <input type="text" id="company" name="company" class="form-control" value="{{ $customer->company ?? '' }}">
</div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control">{{ $customer->address ?? '' }}</textarea>
        </div>

        <button class="btn btn-primary mt-3">Update</button>
    </form>
</div>
@endsection
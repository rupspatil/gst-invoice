
@extends('layouts.app')

@section('title','Invoices')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header">
        <h4 class="m-0 font-weight-bold text-primary">Import Products</h4>
    </div>

    <div class="card-body">

@if(session('success'))
    <div style="color: green">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div style="color: red">{{ session('error') }}</div>
@endif

<form action="{{ route('products.import') }}" method="POST">
    @csrf

    <label>Shopify Store Name:</label>
    <input type="text" name="store" class="form-control" placeholder="example: bavhe2-17" required>

    <br>

    <button class="btn btn-primary" type="submit">Import Products</button>
</form>
</div>
@endsection

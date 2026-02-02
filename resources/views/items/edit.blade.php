@extends('layouts.app')
@section('title','Edit Item')
@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('items.update',$item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>SKU</label>
                <input type="text" name="sku" value="{{ $item->sku ?? '' }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" value="{{ $item->name ?? '' }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Type</label>
                <select name="type" class="form-control" required>
                    <option value="product" {{ ($item->type ?? '')=='product' ? 'selected':'' }}>Product</option>
                    <option value="service" {{ ($item->type ?? '')=='service' ? 'selected':'' }}>Service</option>
                </select>
            </div>

            <div class="mb-3">
                <label>HSN / SAC</label>
                <input type="text" name="hsn_sac" value="{{ $item->hsn_sac ?? '' }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Unit</label>
                <input type="text" name="unit" value="{{ $item->unit ?? '' }}" class="form-control" placeholder="pcs, nos, hrs">
            </div>

            <div class="mb-3">
                <label>Price</label>
                <input type="number" step="0.01" name="price" value="{{ $item->price ?? 0 }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Tax %</label>
                <input type="number" step="0.01" name="tax_percent" value="{{ $item->tax_percent ?? 0 }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Is Inventory?</label>
                <input type="checkbox" name="is_inventory" value="1" {{ isset($item) && $item->is_inventory ? 'checked' : (old('type')!='service' ? 'checked' : '') }}>
            </div>

            <div class="mb-3">
                <label>Stock</label>
                <input type="number" step="0.01" name="stock" value="{{ $item->stock ?? 0 }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ $item->description ?? '' }}</textarea>
            </div>

            <button class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
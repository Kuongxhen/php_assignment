@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Product</h1>
    <form method="POST" action="{{ route('admin.products.update', $product->product_id) }}" class="mt-3">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">SKU</label>
                <input name="sku" value="{{ old('sku', $product->sku) }}" class="form-control" required>
                @error('sku')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-8">
                <label class="form-label">Name</label>
                <input name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
                @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="category" class="form-select" required>
                    <option value="Medication" {{ old('category', $product->category)=='Medication'?'selected':'' }}>Medication</option>
                    <option value="Supplement" {{ old('category', $product->category)=='Supplement'?'selected':'' }}>Supplement</option>
                    <option value="Equipment" {{ old('category', $product->category)=='Equipment'?'selected':'' }}>Equipment</option>
                </select>
                @error('category')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" class="form-control" required>
                @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Cost</label>
                <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $product->cost) }}" class="form-control" required>
                @error('cost')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" min="0" name="quantity" value="{{ old('quantity', $product->quantity) }}" class="form-control" required>
                @error('quantity')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Expiration Date</label>
                <input type="date" name="expiration_date" value="{{ old('expiration_date', optional($product->expiration_date)->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Manufacturer</label>
                <input name="manufacturer" value="{{ old('manufacturer', $product->manufacturer) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Reorder Level</label>
                <input type="number" min="0" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <input name="unit" value="{{ old('unit', $product->unit) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Active</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ old('is_active', $product->is_active) == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('is_active', $product->is_active) == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection



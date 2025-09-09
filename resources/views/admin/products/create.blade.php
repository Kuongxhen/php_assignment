@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Product</h1>
    <form method="POST" action="{{ route('admin.products.store') }}" class="mt-3">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">SKU</label>
                <input name="sku" value="{{ old('sku') }}" class="form-control" required>
                @error('sku')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-8">
                <label class="form-label">Name</label>
                <input name="name" value="{{ old('name') }}" class="form-control" required>
                @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="category" class="form-select" required>
                    <option value="">-- Select --</option>
                    <option value="Medication" {{ old('category')=='Medication'?'selected':'' }}>Medication</option>
                    <option value="Supplement" {{ old('category')=='Supplement'?'selected':'' }}>Supplement</option>
                    <option value="Equipment" {{ old('category')=='Equipment'?'selected':'' }}>Equipment</option>
                </select>
                @error('category')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" class="form-control" required>
                @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Cost</label>
                <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost') }}" class="form-control" required>
                @error('cost')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" min="0" name="quantity" value="{{ old('quantity') }}" class="form-control" required>
                @error('quantity')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Expiration Date</label>
                <input type="date" name="expiration_date" value="{{ old('expiration_date') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Manufacturer</label>
                <input name="manufacturer" value="{{ old('manufacturer') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Reorder Level</label>
                <input type="number" min="0" name="reorder_level" value="{{ old('reorder_level', 5) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <input name="unit" value="{{ old('unit', 'pcs') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Active</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection



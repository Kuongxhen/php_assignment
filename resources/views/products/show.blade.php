@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">{{ $product->name }}</h2>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Back to Catalog</a>
    </div>

    @if($product->image_path)
        <div class="row mb-4">
            <div class="col-md-6">
                <img src="{{ asset($product->image_path) }}" class="img-fluid rounded shadow" alt="{{ $product->name }}" style="max-width: 100%; height: auto;">
            </div>
            <div class="col-md-6">
                <p class="text-muted">{{ $product->description }}</p>
                <p><strong>SKU:</strong> {{ $product->sku }}</p>
                <p><strong>Available Stock:</strong> {{ $product->quantity }}</p>
            </div>
        </div>
    @else
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="bg-light rounded d-flex align-items-center justify-content-center shadow" style="height: 300px;">
                    <i class="fas fa-image fa-4x text-muted"></i>
                </div>
            </div>
            <div class="col-md-6">
                <p class="text-muted">{{ $product->description }}</p>
                <p><strong>SKU:</strong> {{ $product->sku }}</p>
                <p><strong>Available Stock:</strong> {{ $product->quantity }}</p>
            </div>
        </div>
    @endif

    <hr>
    <div class="mb-3">
        <p class="mb-1"><strong>Base Price:</strong> RM {{ number_format($product->price, 2) }}</p>
        <p class="mb-0"><strong>Final Price (after tax & discount):</strong> 
           <span class="text-success">RM {{ number_format($finalPrice, 2) }}</span>
        </p>
    </div>

    <div class="d-flex gap-2">
        <form action="#" method="POST" onsubmit="return false;">
            @csrf
            <button type="button" class="btn btn-success" disabled>Add to Cart</button>
        </form>
        <a href="{{ route('products.index') }}" class="btn btn-link">Continue Shopping</a>
    </div>
</div>
@endsection

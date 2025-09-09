@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">{{ $product->name }}</h2>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Back to Catalog</a>
    </div>

    @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid mb-3" style="max-width: 300px;">
    @endif

    <p class="text-muted">{{ $product->description }}</p>
    <p><strong>SKU:</strong> {{ $product->sku }}</p>
    <p><strong>Available Stock:</strong> {{ $product->quantity }}</p>

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

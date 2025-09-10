@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Product Catalog</h2>

    <div class="row">
        @forelse($products as $product)
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                @if($product->image_path)
                    <img src="{{ asset($product->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-2">{{ $product->name }}</h5>
                    <p class="card-text text-muted mb-3">{{ Str::limit($product->description, 100) }}</p>
                    <p class="mt-auto mb-3"><strong>Price:</strong> RM {{ number_format($product->price, 2) }}</p>
                    <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-primary btn-sm align-self-start">View Details</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">No products available right now. Please check back later.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection


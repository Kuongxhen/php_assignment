<?php

namespace App\Http\Controllers;

use App\Repositories\ProductRepositoryInterface;
use App\Decorators\BasePrice;
use App\Decorators\TaxDecorator;
use App\Decorators\DiscountDecorator;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    // Show product catalog (only available + active products)
    public function index()
    {
        $products = $this->productRepository->all()
            ->where('quantity', '>', 0)
            ->where('is_active', 1);

        return view('products.index', compact('products'));
    }

    // Show single product details with final price (using Decorator Pattern)
    public function show($id)
    {
        $product = $this->productRepository->find($id);

        // Wrap pricing decorators
        $price = new DiscountDecorator(
            new TaxDecorator(
                new BasePrice($product->price)
            )
        );

        $finalPrice = $price->getPrice();

        return view('products.show', compact('product', 'finalPrice'));
    }
}

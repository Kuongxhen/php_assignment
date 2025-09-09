<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get all products for appointment module
     * /api/products
     */
    public function index(): JsonResponse
    {
        $products = $this->productRepository->all()
            ->where('is_active', 1)
            ->where('quantity', '>', 0)
            ->map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category,
                    'price' => $product->price,
                    'cost' => $product->cost,
                    'quantity' => $product->quantity,
                    'unit' => $product->unit,
                    'manufacturer' => $product->manufacturer,
                    'expiration_date' => $product->expiration_date,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ]);
    }

    /**
     * Get single product by ID for appointment module
     * /api/products/{id} 
     */
    public function show($id): JsonResponse
    {
        try {
            $product = $this->productRepository->find($id);
            
            if (!$product || !$product->is_active || $product->quantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not available'
                ], 404);
            }

            $productData = [
                'product_id' => $product->product_id,
                'sku' => $product->sku,
                'name' => $product->name,
                'description' => $product->description,
                'category' => $product->category,
                'price' => $product->price,
                'cost' => $product->cost,
                'quantity' => $product->quantity,
                'unit' => $product->unit,
                'manufacturer' => $product->manufacturer,
                'expiration_date' => $product->expiration_date,
            ];

            return response()->json([
                'success' => true,
                'data' => $productData,
                'message' => 'Product retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }

    /**
     * Get products by category for appointment module
     * /api/products/category/{category}
     */
    public function getByCategory($category): JsonResponse
    {
        $products = $this->productRepository->all()
            ->where('category', $category)
            ->where('is_active', 1)
            ->where('quantity', '>', 0)
            ->map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'unit' => $product->unit,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => "Products in category '{$category}' retrieved successfully"
        ]);
    }

    /**
     * Check product availability and get pricing
     * /api/products/check-availability
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,product_id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1'
        ]);

        $productIds = $request->product_ids;
        $quantities = $request->quantities;
        $results = [];
        $totalAmount = 0;

        foreach ($productIds as $index => $productId) {
            $product = $this->productRepository->find($productId);
            $requestedQuantity = $quantities[$index] ?? 1;

            if (!$product || !$product->is_active) {
                $results[] = [
                    'product_id' => $productId,
                    'available' => false,
                    'message' => 'Product not available'
                ];
                continue;
            }

            if ($product->quantity < $requestedQuantity) {
                $results[] = [
                    'product_id' => $productId,
                    'available' => false,
                    'message' => "Only {$product->quantity} units available"
                ];
                continue;
            }

            $subtotal = $product->price * $requestedQuantity;
            $totalAmount += $subtotal;

            $results[] = [
                'product_id' => $productId,
                'sku' => $product->sku,
                'name' => $product->name,
                'price' => $product->price,
                'requested_quantity' => $requestedQuantity,
                'available_quantity' => $product->quantity,
                'subtotal' => $subtotal,
                'available' => true
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $results,
                'total_amount' => $totalAmount
            ],
            'message' => 'Product availability checked successfully'
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'sku' => 'required|string|unique:products,sku|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'manufacturer' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $product = $this->productRepository->create($request->all());

            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $product->product_id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category,
                    'price' => $product->price,
                    'cost' => $product->cost,
                    'quantity' => $product->quantity,
                    'unit' => $product->unit,
                    'manufacturer' => $product->manufacturer,
                    'expiration_date' => $product->expiration_date,
                    'is_active' => $product->is_active,
                ],
                'message' => 'Product created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = $this->productRepository->find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $request->validate([
                'sku' => 'sometimes|string|max:50|unique:products,sku,' . $id . ',product_id',
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'category' => 'sometimes|string|max:100',
                'price' => 'sometimes|numeric|min:0',
                'cost' => 'nullable|numeric|min:0',
                'quantity' => 'sometimes|integer|min:0',
                'unit' => 'sometimes|string|max:50',
                'manufacturer' => 'nullable|string|max:255',
                'expiration_date' => 'nullable|date',
                'is_active' => 'sometimes|boolean',
            ]);

            $updatedProduct = $this->productRepository->update($id, $request->all());

            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $updatedProduct->product_id,
                    'sku' => $updatedProduct->sku,
                    'name' => $updatedProduct->name,
                    'description' => $updatedProduct->description,
                    'category' => $updatedProduct->category,
                    'price' => $updatedProduct->price,
                    'cost' => $updatedProduct->cost,
                    'quantity' => $updatedProduct->quantity,
                    'unit' => $updatedProduct->unit,
                    'manufacturer' => $updatedProduct->manufacturer,
                    'expiration_date' => $updatedProduct->expiration_date,
                    'is_active' => $updatedProduct->is_active,
                ],
                'message' => 'Product updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy($id): JsonResponse
    {
        try {
            $product = $this->productRepository->find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $this->productRepository->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

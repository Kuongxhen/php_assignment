<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $products = $this->productRepository->all();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => 'required|unique:products',
            'name' => 'required',
            'description' => 'nullable',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
            'manufacturer' => 'nullable|string|max:255',
            'reorder_level' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        // Handle image upload
        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image_path'] = 'storage/' . $imagePath;
        }

        $this->productRepository->create($data);
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = $this->productRepository->find($id);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'sku' => ['required', Rule::unique('products', 'sku')->ignore($id, 'product_id')],
            'name' => 'required',
            'description' => 'nullable',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
            'manufacturer' => 'nullable|string|max:255',
            'reorder_level' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        // Handle image upload
        if ($request->hasFile('product_image')) {
            $product = $this->productRepository->find($id);
            
            // Delete old image if exists
            if ($product->image_path && file_exists(public_path($product->image_path))) {
                unlink(public_path($product->image_path));
            }
            
            // Store new image
            $image = $request->file('product_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image_path'] = 'storage/' . $imagePath;
        }

        $this->productRepository->update($id, $data);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $this->productRepository->delete($id);
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}

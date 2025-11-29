<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Logs;
use App\Models\Product;
use App\Models\Categories;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource with search and filters
     */
    public function index(Request $request)
    {
        $query = Inventory::with(['product.category']);

        // Search functionality - name or description
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('categories') && !empty($request->categories)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->whereIn('category_id', $request->categories);
            });
        }

        // Filter by product status
        if ($request->has('product_status') && !empty($request->product_status)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->whereIn('status', $request->product_status);
            });
        }

        $products = $query->paginate(10);
        $categories = Categories::all();

        return view('admin.inventory', compact('products', 'categories'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,category_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,unavailable',
            'quantity' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload - FIXED PATH
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'product_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store in storage/app/public/products
            $path = $file->storeAs('products', $fileName, 'public');

            // Save only the relative path (products/filename.jpg)
            $imagePath = $path;
        }

        // Create product
        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['status'],
            'image' => $imagePath  // This will save as "products/product_xxx.jpg"
        ]);



        // Create inventory
        Inventory::create([
            'product_id' => $product->product_id,
            'quantity' => $validated['quantity'],
        ]);

        $currentUser = Auth::user();

        Logs::create([
            'user_id' => $currentUser->user_id,
            'action' => "{$currentUser->last_name} added a new product: {$product->name}.",
            'timestamp' => now(),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product added successfully!');
    }

    /**
     * Show a specific product
     */
    public function show(string $id)
    {
        try {
            $product = Product::with(['category', 'inventory'])
                ->where('product_id', $id)
                ->firstOrFail();

            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Product not found',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get a product for editing
     */
    public function edit(string $id)
    {
        $product = Product::with(['category', 'inventory'])
            ->findOrFail($id);

        return response()->json($product);
    }

    /**
     * Update a product
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $inventory = Inventory::where('product_id', $id)->firstOrFail();

        $validated = $request->validate([
            'category_id' => 'sometimes|integer|exists:categories,category_id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:available,unavailable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $inventoryValidated = $request->validate([
            'quantity' => 'sometimes|numeric|min:0',
        ]);

        // Handle image upload - FIXED
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $file = $request->file('image');
            $fileName = 'product_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store in storage/app/public/products
            $path = $file->storeAs('products', $fileName, 'public');

            // Save only the relative path
            $validated['image'] = $path;
        }

        // Update records
        $product->update($validated);
        $inventory->update($inventoryValidated);

        $currentUser = Auth::user();

        Logs::create([
            'user_id' => $currentUser->user_id,
            'action' => "{$currentUser->last_name} updated a product: {$product->name}.",
            'timestamp' => now(),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete a product
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $inventory = Inventory::where('product_id', $id)->firstOrFail();

        // Delete product image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $currentUser = Auth::user();

        Logs::create([
            'user_id' => $currentUser->user_id,
            'action' => "{$currentUser->last_name} deleted a product: {$product->name}.",
            'timestamp' => now(),
        ]);

        $inventory->delete();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}

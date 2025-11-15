<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Categories;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource with search and filters
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('categories') && !empty($request->categories)) {
            $query->whereIn('category_id', $request->categories);
        }

        // Filter by status
        if ($request->has('product_status') && !empty($request->product_status)) {
            $query->whereIn('status', $request->product_status);
        }

        $products = $query->paginate(10);
        $categories = Categories::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,category_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,unavailable',
        ]);

        $product = Product::create($validated);
        
        return redirect()->route('products.index')
            ->with('success', 'Product added successfully!');
    }

    /**
     * Display the specified resource
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
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(string $id)
    {
        $product = Product::with(['category', 'inventory'])
            ->findOrFail($id);
        
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|integer|exists:categories,category_id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:available,unavailable',
        ]);

        $product->update($validated);
        
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
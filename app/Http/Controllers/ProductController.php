<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use App\Models\Categories;
use App\Models\Inventory;
use App\Models\Logs;
use App\Models\Product;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource with search and filters
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        $branchId = null;
        if ($currentUser->role === 'super_admin') {
            $branchId = session('selected_branch_id');
        } else {
            $branchId = $currentUser->branch_id;
        }

        $query = Inventory::with(['product.category', 'product.branch']);

        // Search functionality - name or description
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('categories') && ! empty($request->categories)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->whereIn('category_id', $request->categories);
            });
        }

        // Filter by product status
        if ($request->has('product_status') && ! empty($request->product_status)) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->whereIn('status', $request->product_status);
            });
        }

        // Filter by branch
        if ($branchId) {
            $query->whereHas('product', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $products = $query->paginate(10);
        $categories = Categories::all();
        $branches = Branches::orderBy('name')->get();

        return view('admin.inventory', compact('products', 'categories', 'branches'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $branchId = $currentUser->role === 'super_admin'
            ? session('selected_branch_id')
            : $currentUser->branch_id;

        $rules = [
            'category_id' => 'required|integer|exists:categories,category_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,unavailable',
            'quantity' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Add branch_id validation for super_admin
        if ($currentUser->role === 'super_admin') {
            $rules['branch_id'] = 'required|exists:branches,branch_id';
        }

        $validated = $request->validate($rules);

        // Set branch_id based on user role
        if ($currentUser->role !== 'super_admin') {
            $validated['branch_id'] = $currentUser->branch_id;
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'product_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('products', $fileName, 'public');
            $imagePath = $path;
        }

        // Create product
        $product = Product::create([
            'branch_id' => $validated['branch_id'],
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['status'],
            'image' => $imagePath,
        ]);

        // Create inventory
        Inventory::create([
            'product_id' => $product->product_id,
            'branch_id' => $branchId,
            'quantity' => $validated['quantity'],
        ]);

        Transactions::create([
            'product_id' => $product->product_id,
            'branch_id' => $branchId,
            'performed_by' => $currentUser->user_id,
            'quantity' => $validated['quantity'],
            'type' => 'stock_in',
            'timestamp' => now(),
        ]);

        Logs::create([
            'user_id' => $currentUser->user_id,
            'branch_id' => $branchId,
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
            $product = Product::with(['category', 'inventory', 'branch'])
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
        $product = Product::with(['category', 'inventory', 'branch'])
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
        $currentUser = Auth::user();

        $rules = [
            'category_id' => 'sometimes|integer|exists:categories,category_id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:available,unavailable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Add branch_id validation for super_admin
        if ($currentUser->role === 'super_admin') {
            $rules['branch_id'] = 'sometimes|exists:branches,branch_id';
        }

        $validated = $request->validate($rules);

        // Set branch_id based on user role
        if ($currentUser->role !== 'super_admin' && ! isset($validated['branch_id'])) {
            $validated['branch_id'] = $currentUser->branch_id;
        }

        $inventoryValidated = $request->validate([
            'quantity' => 'sometimes|numeric|min:0',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $file = $request->file('image');
            $fileName = 'product_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('products', $fileName, 'public');
            $validated['image'] = $path;
        }

        // Check if quantity is being updated and increased
        $oldQuantity = $inventory->quantity;
        $quantityChanged = false;
        $quantityIncreased = false;
        $quantityDifference = 0;

        if (isset($inventoryValidated['quantity'])) {
            $newQuantity = $inventoryValidated['quantity'];

            if ($newQuantity != $oldQuantity) {
                $quantityChanged = true;
                $quantityDifference = $newQuantity - $oldQuantity;

                if ($newQuantity > $oldQuantity) {
                    $quantityIncreased = true;
                }
            }
        }

        // Update records
        $product->update($validated);
        $inventory->update($inventoryValidated);

        // Create transaction only if quantity was increased
        if ($quantityChanged && $quantityIncreased) {
            Transactions::create([
                'product_id' => $product->product_id,
                'type' => 'stock_in',
                'performed_by' => $currentUser->user_id,
                'quantity' => $quantityDifference,
                'timestamp' => now(),
            ]);
        }

        // Enhanced logging with quantity change details
        $logAction = "{$currentUser->last_name} updated product: {$product->name}";

        if ($quantityChanged) {
            if ($quantityIncreased) {
                $logAction .= " (Stock increased by {$quantityDifference} units)";
            } else {
                $logAction .= ' (Stock decreased by '.abs($quantityDifference).' units)';
            }
        }

        Logs::create([
            'user_id' => $currentUser->user_id,
            'action' => $logAction,
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

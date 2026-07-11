<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    /**
     * Display a listing of categories with search
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $categories = Categories::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(12)
            ->appends($request->query());

        return view('admin.categories', compact('categories', 'search'));
    }

    /**
     * Store a new category
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Categories::create($validated);
        $branchId = $currentUser->role === 'super_admin'
                  ? session('selected_branch_id')
                  : $currentUser->branch_id;

        Logs::create([
            'user_id' => $currentUser->user_id,
            'branch_id' => $branchId,
            'action' => "{$currentUser->last_name} created a category: {$category->name}.",
            'timestamp' => now(),
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully');
    }

    /**
     * Show a specific category
     */
    public function show(string $id)
    {
        try {
            $category = Categories::where('category_id', $id)->firstOrFail();

            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Category not found',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get category for editing
     */
    public function edit(string $id)
    {
        $category = Categories::where('category_id', $id)->firstOrFail();

        return response()->json($category);
    }

    /**
     * Update a category
     */
    public function update(Request $request, string $id)
    {
        $currentUser = Auth::user();
        $branchId = $currentUser->role === 'super_admin'
                  ? session('selected_branch_id')
                  : $currentUser->branch_id;
        $category = Categories::where('category_id', $id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id.',category_id',
        ]);

        $oldName = $category->name;
        $category->update($validated);

        Logs::create([
            'user_id' => $currentUser->user_id,
            'branch_id' => $branchId,
            'action' => "{$currentUser->last_name} updated category from '{$oldName}' to '{$category->name}'.",
            'timestamp' => now(),
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Delete a category
     */
    public function destroy(string $id)
    {
        $currentUser = Auth::user();
        $branchId = $currentUser->role === 'super_admin'
                  ? session('selected_branch_id')
                  : $currentUser->branch_id;
        $category = Categories::where('category_id', $id)->firstOrFail();

        $categoryName = $category->name;

        Logs::create([
            'user_id' => $currentUser->user_id,
            'branch_id' => $branchId,
            'action' => "{$currentUser->last_name} deleted category: {$categoryName}.",
            'timestamp' => now(),
        ]);

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::orderBy('sort_order')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'icon_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category created successfully'));
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $courses = $category->courses()->paginate(10);
        return view('admin.categories.show', compact('category', 'courses'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'icon_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category updated successfully'));
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category deleted successfully'));
    }

    /**
     * Reorder categories via AJAX
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|integer|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['categories'] as $cat) {
            Category::where('id', $cat['id'])->update(['sort_order' => $cat['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => __('Categories reordered successfully')]);
    }
}

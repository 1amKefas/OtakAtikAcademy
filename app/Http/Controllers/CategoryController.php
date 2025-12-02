<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        // Ambil semua course aktif untuk dipilih
        $courses = Course::where('is_active', true)->get();
        return view('admin.categories.create', compact('courses'));
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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'icon_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['name', 'description']);
        $data['slug'] = Str::slug($request->name);

        // 1. Upload Thumbnail
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('categories', 'public');
        }

        $category = Category::create($data);

        // 2. Assign Courses (Attach)
        if ($request->has('courses')) {
            $category->courses()->attach($request->courses);
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dibuat!');
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
        $courses = Course::where('is_active', true)->get();
        // Load relasi courses agar checkbox tercentang otomatis
        $category->load('courses');
        return view('admin.categories.edit', compact('category', 'courses'));
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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'courses' => 'nullable|array',
            'icon_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['name', 'description']);
        $data['slug'] = Str::slug($request->name);

        // 1. Update Thumbnail
        if ($request->hasFile('thumbnail')) {
            // Hapus gambar lama jika ada
            if ($category->thumbnail) {
                Storage::disk('public')->delete($category->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('categories', 'public');
        }

        $category->update($data);

        // 2. Sync Courses (Otomatis tambah/hapus sesuai checklist)
        $category->courses()->sync($request->input('courses', []));

        return redirect()->route('categories.index')->with('success', 'Kategori diperbarui!');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        if ($category->thumbnail) {
            Storage::disk('public')->delete($category->thumbnail);
        }
        // Hapus relasi course dulu
        $category->courses()->detach();
        $category->delete();

        return back()->with('success', 'Kategori dihapus!');
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

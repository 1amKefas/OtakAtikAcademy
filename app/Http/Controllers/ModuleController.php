<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\ModuleMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    /**
     * Display modules for a course
     */
    public function index(Course $course)
    {
        $this->authorize('update', $course);
        
        $modules = $course->modules()->orderBy('sort_order')->get();
        return view('instructor.modules.index', compact('course', 'modules'));
    }

    /**
     * Show the form for creating a new module
     */
    public function create(Course $course)
    {
        $this->authorize('update', $course);
        return view('instructor.modules.create', compact('course'));
    }

    /**
     * Reorder Modules via AJAX
     */
    public function reorder(Request $request, Course $course)
    {
        // 1. Validasi input
        $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'exists:course_modules,id',
        ]);

        // 2. Loop dan update urutan
        // Bungkus dengan Transaction biar 'Atomik' (Cepat & Aman)
        DB::transaction(function () use ($request, $course) {
            foreach ($request->ordered_ids as $index => $moduleId) {
                \App\Models\CourseModule::where('id', $moduleId)
                    ->where('course_id', $course->id)
                    ->update(['order' => $index + 1]); // Pastikan nama kolom 'order' sesuai migration
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Reorder Quizzes via AJAX
     */
    public function reorderQuizzes(Request $request, Course $course, CourseModule $module)
    {
        $request->validate([
            'quizzes' => 'required|array',
            'quizzes.*.id' => 'required|integer|exists:quizzes,id',
            'quizzes.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->quizzes as $q) {
            \App\Models\Quiz::where('id', $q['id'])
                ->where('course_module_id', $module->id)
                ->update(['sort_order' => $q['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan quiz diperbarui']);
    }

    /**
     * Reorder Mixed Contents (Material & Quiz)
     */
    public function reorderContents(Request $request, Course $course, CourseModule $module)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.type' => 'required|in:material,quiz', // Wajib kirim type
        ]);

        // Loop urutan dari frontend (0, 1, 2, ...)
        foreach ($request->items as $index => $item) {
            $sortOrder = $index + 1;

            if ($item['type'] === 'material') {
                \App\Models\CourseMaterial::where('id', $item['id'])
                    ->where('course_module_id', $module->id)
                    ->update(['sort_order' => $sortOrder]);
            } 
            elseif ($item['type'] === 'quiz') {
                \App\Models\Quiz::where('id', $item['id'])
                    ->where('course_module_id', $module->id)
                    ->update(['sort_order' => $sortOrder]);
            }
        }

        return response()->json(['success' => true]);
    }
    /**
     * Store a newly created module
     */
    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['course_id'] = $course->id;
        $module = CourseModule::create($validated);

        return redirect()->route('instructor.modules.show', [$course, $module])
            ->with('success', __('Module created successfully'));
    }

    /**
     * Display the specified module
     */
    public function show(Course $course, CourseModule $module)
    {
        $this->authorize('update', $course);
        
        $materials = $module->materials()->orderBy('sort_order')->get();
        return view('instructor.modules.show', compact('course', 'module', 'materials'));
    }

    /**
     * Show the form for editing the specified module
     */
    public function edit(Course $course, CourseModule $module)
    {
        $this->authorize('update', $course);
        return view('instructor.modules.edit', compact('course', 'module'));
    }

    /**
     * Update the specified module
     */
    public function update(Request $request, Course $course, CourseModule $module)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $module->update($validated);

        return redirect()->route('instructor.modules.show', [$course, $module])
            ->with('success', __('Module updated successfully'));
    }

    /**
     * Remove the specified module
     */
    public function destroy(Course $course, CourseModule $module)
    {
        $this->authorize('update', $course);
        $module->delete();

        return redirect()->route('instructor.modules.index', $course)
            ->with('success', __('Module deleted successfully'));
    }

    /**
     * Add material to module
     */
    public function addMaterial(Request $request, Course $course, CourseModule $module)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,file,image,link,gdrive,text',
            'file_url' => 'nullable|string|max:500',
            'external_url' => 'nullable|string|max:500',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        $validated['module_id'] = $module->id;
        $material = ModuleMaterial::create($validated);

        return redirect()->route('instructor.modules.show', [$course, $module])
            ->with('success', __('Material added successfully'));
    }

    /**
     * Update module material
     */
    public function updateMaterial(Request $request, Course $course, CourseModule $module, ModuleMaterial $material)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,file,image,link,gdrive,text',
            'file_url' => 'nullable|string|max:500',
            'external_url' => 'nullable|string|max:500',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        $material->update($validated);

        return redirect()->route('instructor.modules.show', [$course, $module])
            ->with('success', __('Material updated successfully'));
    }

    /**
     * Delete module material
     */
    public function deleteMaterial(Course $course, CourseModule $module, ModuleMaterial $material)
    {
        $this->authorize('update', $course);
        $material->delete();

        return redirect()->route('instructor.modules.show', [$course, $module])
            ->with('success', __('Material deleted successfully'));
    }

    /**
     * Reorder materials via AJAX
     */
    public function reorderMaterials(Request $request, Course $course, CourseModule $module)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*.id' => 'required|integer|exists:module_materials,id',
            'materials.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['materials'] as $mat) {
            ModuleMaterial::where('id', $mat['id'])->update(['sort_order' => $mat['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => __('Materials reordered successfully')]);
    }
}

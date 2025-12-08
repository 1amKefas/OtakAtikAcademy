<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseClassController extends Controller
{
    /**
     * Tampilkan halaman manajemen kelas
     */
    public function index($courseId)
    {
        $course = Course::where('instructor_id', Auth::id())->findOrFail($courseId);

        // Hanya untuk Hybrid/Offline
        if ($course->type === 'online') {
            return back()->with('error', 'Fitur Kelas hanya untuk kursus Hybrid/Tatap Muka.');
        }

        // Load kelas beserta siswanya
        $classes = $course->classes()->with(['instructor', 'students.user'])->get();

        // Load siswa yang BELUM punya kelas (Unassigned)
        $unassignedStudents = $course->registrations()
            ->where('status', 'paid')
            ->whereNull('course_class_id')
            ->with('user')
            ->get();

        // Load list asisten/instruktur lain untuk dijadikan PJ Kelas (Opsional)
        // Disini kita ambil semua user instructor sebagai contoh
        $availableInstructors = User::where('is_instructor', true)->get();

        return view('instructor.course-classes', compact('course', 'classes', 'unassignedStudents', 'availableInstructors'));
    }

    /**
     * Buat Kelas Baru
     */
    public function store(Request $request, $courseId)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'quota' => 'required|integer|min:1',
            'instructor_id' => 'nullable|exists:users,id'
        ]);

        CourseClass::create([
            'course_id' => $courseId,
            'name' => $request->name,
            'slug' => Str::slug($request->name . '-' . Str::random(5)),
            'quota' => $request->quota,
            'instructor_id' => $request->instructor_id ?? Auth::id(), // Default ke diri sendiri jika kosong
        ]);

        return back()->with('success', 'Kelas berhasil dibuat!');
    }

    /**
     * Assign Student ke Kelas
     */
    public function assignStudent(Request $request, $classId)
    {
        $class = CourseClass::findOrFail($classId);
        
        // Cek Kuota
        if ($class->students()->count() >= $class->quota) {
            return back()->with('error', 'Kelas sudah penuh!');
        }

        $registration = CourseRegistration::where('id', $request->registration_id)
            ->where('course_id', $class->course_id) // Pastikan student emang daftar di course ini
            ->firstOrFail();

        $registration->update(['course_class_id' => $classId]);

        return back()->with('success', 'Siswa berhasil dimasukkan ke kelas!');
    }

    /**
     * Keluarkan Student dari Kelas (Balikin ke Unassigned)
     */
    public function removeStudent($registrationId)
    {
        $registration = CourseRegistration::findOrFail($registrationId);
        // Validasi owner course
        if($registration->course->instructor_id !== Auth::id()){
             abort(403);
        }

        $registration->update(['course_class_id' => null]);

        return back()->with('success', 'Siswa dikeluarkan dari kelas.');
    }

    /**
     * Hapus Kelas
     */
    public function destroy($classId)
    {
        $class = CourseClass::findOrFail($classId);
        
        // Kembalikan semua siswa di kelas ini jadi unassigned
        CourseRegistration::where('course_class_id', $classId)
            ->update(['course_class_id' => null]);

        $class->delete();

        return back()->with('success', 'Kelas dihapus. Siswa kembali ke status belum dapat kelas.');
    }
}
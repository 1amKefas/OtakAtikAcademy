<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category; // <--- WAJIB ADA
use App\Models\CourseRegistration;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // --- INI YANG KURANG ---
        // Kita ambil data kategori dari database
        $categories = Category::orderBy('sort_order', 'asc')->get();

        // Data tambahan (opsional)
        $myCoursesCount = CourseRegistration::where('user_id', $user->id)->where('status', 'paid')->count();
        $completedCoursesCount = CourseRegistration::where('user_id', $user->id)->where('status', 'paid')->where('progress', 100)->count();
        $certificatesCount = $user->certificates ? $user->certificates->count() : 0;

        // Kirim $categories ke view
        return view('dashboard', compact(
            'user', 
            'categories', // <--- PENTING: Harus ada di sini
            'myCoursesCount', 
            'completedCoursesCount', 
            'certificatesCount'
        ));
    }
}
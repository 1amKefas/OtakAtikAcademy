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
        
        // [OPTIMASI] Cache kategori selama 60 menit karena datanya jarang berubah
        $categories = \Illuminate\Support\Facades\Cache::remember('all_categories_ordered', 3600, function () {
            return \App\Models\Category::orderBy('sort_order', 'asc')->get();
        });

        // Data tambahan (opsional)
        // Gunakan cache pendek (misal 30 detik) untuk hitungan personal jika traffic tinggi
        $myCoursesCount = CourseRegistration::where('user_id', $user->id)->where('status', 'paid')->count();
        $completedCoursesCount = CourseRegistration::where('user_id', $user->id)->where('status', 'paid')->where('progress', 100)->count();
        $certificatesCount = $user->certificates ? $user->certificates->count() : 0;

        return view('dashboard', compact(
            'user', 
            'categories', 
            'myCoursesCount', 
            'completedCoursesCount', 
            'certificatesCount'
        ));
    }
}
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
        // [OPTIMASI] Gabungin 2 query count menjadi 1 query agregat
        $stats = CourseRegistration::where('user_id', $user->id)
            ->where('status', 'paid')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when progress = 100 then 1 end) as completed')
            ->first();

        $myCoursesCount = $stats->total ?? 0;
        $completedCoursesCount = $stats->completed ?? 0;
        $certificatesCount = $user->certificates()->count();

        return view('dashboard', compact(
            'user', 
            'categories', 
            'myCoursesCount', 
            'completedCoursesCount', 
            'certificatesCount'
        ));
    }
}
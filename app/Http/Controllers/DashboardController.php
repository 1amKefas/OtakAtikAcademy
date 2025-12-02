<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Category; // <--- JANGAN LUPA IMPORT INI
use App\Models\CourseRegistration;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil data untuk statistik dashboard student
        $myCoursesCount = CourseRegistration::where('user_id', $user->id)
            ->where('status', 'paid') // Asumsi 'paid' berarti aktif
            ->count();
            
        $completedCoursesCount = CourseRegistration::where('user_id', $user->id)
            ->where('status', 'paid')
            ->where('progress', 100)
            ->count();
            
        $certificatesCount = $user->certificates ? $user->certificates->count() : 0; // Asumsi ada relasi certificates

        // --- TAMBAHAN: Ambil Kategori untuk Slider ---
        $categories = Category::orderBy('sort_order', 'asc')->get();

        // Kirim semua data ke view
        return view('dashboard', compact(
            'user', 
            'myCoursesCount', 
            'completedCoursesCount', 
            'certificatesCount',
            'categories' // <--- KIRIM DATA KATEGORI
        ));
    }
}
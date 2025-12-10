<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    // App/Http/Controllers/HomeController.php
    public function index() {
        $categories = \App\Models\Category::orderBy('sort_order', 'asc')->get();
        return view('dashboard', compact('categories'));
    }
}

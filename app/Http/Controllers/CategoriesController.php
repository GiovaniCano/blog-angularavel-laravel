<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoriesController extends Controller
{
    function index() {
        return response()->json( Category::all() );
    }
}

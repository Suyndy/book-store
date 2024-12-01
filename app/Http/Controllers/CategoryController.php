<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CreateCategoryRequest;

class CategoryController extends Controller
{
    public function store(CreateCategoryRequest $request)
    {
       $validated = $request->validated();

       $category = Category::create($validated);

       return response()->json($category, 201);
    } 
}

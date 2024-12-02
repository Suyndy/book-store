<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\Api\Category\CreateCategoryRequest;
use App\Http\Requests\Api\Category\UpdateCategoryRequest;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function store(CreateCategoryRequest $request)
    {
       $validated = $request->validated();

       $category = $this->categoryService->create($validated);

       return response()->json($category, 201);
    }

    public function getAll()
    {
        $category = $this->categoryService->getAll();

        return response()->json($category);
    }

    public function getOne($id)
    {
        $category = $this->categoryService->getOne($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

     public function update($id, UpdateCategoryRequest $request)
     {
        $validated = $request->validated();
        $category = $this->categoryService->update($id, $validated);
 
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
 
        return response()->json($category, 200);
     }

    public function softDelete($id)
    {
        $category = $this->categoryService->softDelete($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}

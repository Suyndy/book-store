<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function store(CreateCategoryRequest $request)
    {
       $validated = $request->validated();

       $category = Category::create($validated);

       return response()->json($category, 201);
    }

    public function getAll()
    {
        // Lấy tất cả các bản ghi từ bảng 'categories'
        $categories = Category::all();

        // Trả về dữ liệu dưới dạng JSON
        return response()->json($categories);
    }

    // Phương thức lấy một category theo id (UUID)
    public function getOne($id)
    {
        // Tìm category theo id
        $category = Category::find($id);

        // Nếu không tìm thấy, trả về lỗi 404
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Trả về thông tin category
        return response()->json($category);
    }

     // Cập nhật category
     public function update($id, UpdateCategoryRequest $request)
     {
         // Tìm category theo id
         $category = Category::find($id);
 
         if (!$category) {
             return response()->json(['message' => 'Category not found'], 404);
         }
 
         // Validate dữ liệu từ request
         $validated = $request->validated();
 
         // Cập nhật category với dữ liệu mới
         $category->update($validated);
 
         return response()->json($category, 200);
     }

    public function softDelete($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Xóa mềm category
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}

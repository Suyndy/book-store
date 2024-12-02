<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryService
{
    public function create(array $data)
    {
        return Category::create($data);
    }

    public function getAll()
    {
        return Category::all();
    }

    public function getOne($id)
    {
        return Category::find($id);
    }

    public function update($id, array $data)
    {
        $category = Category::find($id);
        if (!$category) {
            return null;
        }
        $category->update($data);
        return $category;
    }

    public function softDelete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return null;
        }
        $category->delete();
        return $category;
    }
}
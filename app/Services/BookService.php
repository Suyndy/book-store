<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookService 
{
    public function store($data)
    {
        return Book::create($data);
    }

    public function getAll(Request $request)
    {
        $query = Book::query();

        // Tìm kiếm theo từ khóa
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('author', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo danh mục (sử dụng category=1,2)
        if ($request->has('category')) {
            $categories = $request->category; // Lấy giá trị từ query string

            // Nếu là chuỗi, tách thành mảng
            if (is_string($categories)) {
                // Tách chuỗi thành mảng nếu có dấu phẩy
                $categories = explode(',', $categories);
            }

            // Đảm bảo rằng $categories luôn là mảng
            if (is_array($categories) && count($categories) > 0) {
                $query->whereHas('category', function ($q) use ($categories) {
                    $q->whereIn('id', $categories);
                });
            }
        }

        // Lọc theo khoảng giá
        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = $request->min_price;
            $maxPrice = $request->max_price;
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        // Trả về dữ liệu phân trang
        return $query->with('category')->paginate(10);
    }


    public function getOne($id)
    {
        return Book::with('category')->findOrFail($id);
    }

    public function update($id, $data)
    {
        $book = Book::findOrFail($id);
        $book->update($data);
        return $book;
    }

    public function softDelete($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return null;
        }

        $book->delete();
        return $book;
    }
}
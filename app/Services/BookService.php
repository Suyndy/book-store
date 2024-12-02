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

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('author', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('category')) {
            $category = $request->category;
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', 'like', '%' . $category . '%');
            });
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = $request->min_price;
            $maxPrice = $request->max_price;
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

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
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function store(StoreBookRequest $request) 
    {
        $result = Book::create($request->validated());

        if ($result) {
            return response()->json($result, 201);
        }
        return response()->json('Failed to create book', 500);
    }
    

    public function getAll(Request $request) 
    {
        $query = Book::query();

        if ( $request->has('search')) {
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

        $books = $query->with('category')->paginate(10);

        return response()->json($books);
    }

    public function getOne($id)
    {
        $book = Book::with('category')->findOrFail($id);
        return response()->json($book);
    }

    public function update(UpdateBookRequest $request, $id)
    {
        $book = Book::findOrFail($id);

        $book->update($request->validated());
        return response()->json($book);
    }

    public function softDelete($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Xóa mềm category
        $book->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}

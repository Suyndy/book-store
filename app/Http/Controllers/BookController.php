<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function create(Request $request) 
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'isbn' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'manufacturer' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        $book = Book::create($request->all());
        return response()->json($book, 201);
    }

    public function getAll(Request $request)
    {
        $query = Book::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('author', 'like', '%' . $request->search . '%');
        }

        $books = $query->with('category')->get(); 
        return response()->json($books);
    }

    public function getOne($id)
    {
        $book = Book::with('category')->findOrFail($id);
        return response()->json($book);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'isbn' => 'nullable|string|max:20',
            'category_id' => 'nullable|exists:categories,id',
            'manufacturer' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        $book->update($request->all());
        return response()->json($book);
    }

    public function delete($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully']);
    }
}

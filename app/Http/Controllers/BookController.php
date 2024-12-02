<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\Api\Book\StoreBookRequest;
use App\Http\Requests\Api\Book\UpdateBookRequest;
use App\Services\BookService; 
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function store(StoreBookRequest $request)
    {
        $result = $this->bookService->store($request->validated());

        if ($result) {
            return response()->json($result, 201);
        }

        return response()->json('Failed to create book', 500);
    }
    

    public function getAll(Request $request)
    {
        $books = $this->bookService->getAll($request);
        return response()->json($books);
    }

    public function getOne($id)
    {
        $book = $this->bookService->getOne($id);

        return response()->json($book);
    }

    public function update(UpdateBookRequest $request, $id)
    {
        $book = $this->bookService->update($id, $request->validated());

        return response()->json($book);
    }

    public function softDelete($id)
    {
        $book = $this->bookService->softDelete($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        return response()->json(['message' => 'Book deleted successfully'], 200);
    }
}

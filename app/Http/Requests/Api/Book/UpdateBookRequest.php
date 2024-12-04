<?php

namespace App\Http\Requests\Api\Book;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric'],
            'quantity' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn,' . $this->route('id')],
            'category_id' => ['nullable', 'exists:categories,id'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}

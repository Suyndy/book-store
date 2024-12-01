<?php

namespace App\Http\Requests;

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
            'description' => ['nullable', 'string'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn,' . $this->route('id')],
            'category_id' => ['nullable', 'exists:categories,id'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
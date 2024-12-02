<?php

namespace App\Http\Requests\Api\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
   
    public function authorize()
    {
        return true; // Bạn có thể thay đổi logic này nếu muốn kiểm tra quyền của người dùng
    }

   
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}

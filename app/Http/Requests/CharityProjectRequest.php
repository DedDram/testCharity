<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CharityProjectRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:active,closed',
            'launch_date' => 'nullable|date',
            'per_page' => 'nullable|integer|min:3|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Статус должен быть либо "active", либо "closed".',
            'launch_date.date' => 'Дата должна быть корректной датой.',
            'per_page.integer' => 'Количество элементов на странице должно быть целым числом.',
            'per_page.min' => 'Количество элементов на странице должно быть не менее 3.',
            'per_page.max' => 'Количество элементов на странице не может превышать 10.',
        ];
    }
}

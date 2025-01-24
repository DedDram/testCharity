<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonationRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'charity_project_id' => 'required|exists:charity_projects,id',
            'amount' => 'required|numeric|min:1',
            'donation_date' => 'nullable|date|before_or_equal:now',
            'comment' => 'nullable|string|max:255',
        ];
    }
}

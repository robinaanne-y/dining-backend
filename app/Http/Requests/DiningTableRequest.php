<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DiningTableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {   
        $diningTable = $this->route('diningTable');
        return [
            'table_number' => 'required|string|max:10',
            'seating_capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
            'qr_token' => [
                'sometimes',
                'string',
                Rule::unique('dining_tables')->ignore($diningTable->id ?? null),
            ],
            'restaurant_id' => 'required|exists:restaurants,id',
        ];
    }
}

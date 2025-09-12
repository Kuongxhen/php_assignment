<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderRequestFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,product_id',
            'current_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:1',
            'suggested_quantity' => 'required|integer|min:1',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_cost' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required',
            'product_id.exists' => 'The selected product does not exist',
            'current_quantity.required' => 'Current quantity is required',
            'current_quantity.integer' => 'Current quantity must be a valid integer',
            'current_quantity.min' => 'Current quantity cannot be negative',
            'reorder_level.required' => 'Reorder level is required',
            'reorder_level.integer' => 'Reorder level must be a valid integer',
            'reorder_level.min' => 'Reorder level must be at least 1',
            'suggested_quantity.required' => 'Suggested quantity is required',
            'suggested_quantity.integer' => 'Suggested quantity must be a valid integer',
            'suggested_quantity.min' => 'Suggested quantity must be at least 1',
            'priority.required' => 'Priority is required',
            'priority.in' => 'Priority must be one of: low, medium, high, urgent',
            'estimated_cost.numeric' => 'Estimated cost must be a valid number',
            'estimated_cost.min' => 'Estimated cost cannot be negative',
            'supplier.max' => 'Supplier name cannot exceed 255 characters',
            'notes.max' => 'Notes cannot exceed 1000 characters'
        ];
    }
}

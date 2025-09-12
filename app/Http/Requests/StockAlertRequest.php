<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAlertRequest extends FormRequest
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
            'alert_type' => 'required|in:low_stock,out_of_stock,expired',
            'message' => 'required|string|max:500',
            'current_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'severity' => 'required|in:low,medium,high,critical'
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
            'alert_type.required' => 'Alert type is required',
            'alert_type.in' => 'Alert type must be one of: low_stock, out_of_stock, expired',
            'message.required' => 'Alert message is required',
            'message.max' => 'Alert message cannot exceed 500 characters',
            'current_quantity.required' => 'Current quantity is required',
            'current_quantity.integer' => 'Current quantity must be a valid integer',
            'current_quantity.min' => 'Current quantity cannot be negative',
            'reorder_level.required' => 'Reorder level is required',
            'reorder_level.integer' => 'Reorder level must be a valid integer',
            'reorder_level.min' => 'Reorder level cannot be negative',
            'severity.required' => 'Severity level is required',
            'severity.in' => 'Severity must be one of: low, medium, high, critical'
        ];
    }
}

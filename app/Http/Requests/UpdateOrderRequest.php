<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::in(Order::getStatuses())],
            'items' => 'sometimes|array|min:1',
            'items.*.product_name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.price' => 'required_with:items|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Invalid status. Valid statuses are: pending, confirmed, cancelled',
            'items.*.product_name.required_with' => 'Product name is required for all items',
            'items.*.quantity.required_with' => 'Quantity is required for all items',
            'items.*.price.required_with' => 'Price is required for all items',
        ];
    }
}

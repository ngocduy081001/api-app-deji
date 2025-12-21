<?php

namespace Vendor\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:15',
            'showroom_id' => 'required|integer|exists:showrooms,id',
            'date' => 'required|date',
            'time' => 'required',
            'product_id' => 'required|integer|exists:products,id',
            'price' => 'required',
            'product' => 'required',
            'notes' => 'sometimes|nullable|string',
        ];
    }
}

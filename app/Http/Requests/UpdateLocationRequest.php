<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role->name === 'organizer';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $locationId = $this->route('location')->id;

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('locations', 'name')->ignore($locationId),
            ],
            'address' => 'nullable|string|max:500',
            'capacity' => 'nullable|integer|min:1|max:50000',
            'description' => 'nullable|string|max:1000',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الموقع مطلوب',
            'name.max' => 'اسم الموقع يجب ألا يتجاوز 255 حرف',
            'name.unique' => 'اسم الموقع مستخدم بالفعل',
            'address.max' => 'العنوان يجب ألا يتجاوز 500 حرف',
            'capacity.integer' => 'السعة يجب أن تكون رقم صحيح',
            'capacity.min' => 'السعة يجب أن تكون على الأقل 1',
            'capacity.max' => 'السعة يجب ألا تتجاوز 50000',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
            'facilities.array' => 'المرافق يجب أن تكون مصفوفة',
            'facilities.*.string' => 'كل مرفق يجب أن يكون نص',
            'facilities.*.max' => 'كل مرفق يجب ألا يتجاوز 100 حرف',
        ];
    }
}

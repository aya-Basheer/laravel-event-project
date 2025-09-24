<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpeakerRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'bio' => 'required|string|max:2000',
            'email' => 'nullable|email|max:255|unique:speakers,email',
            'phone' => 'nullable|string|max:20',
            'avatar_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'twitter_url' => 'nullable|url|max:500',
            'website_url' => 'nullable|url|max:500',
            'topics' => 'nullable|array',
            'topics.*' => 'string|max:100',
            'is_featured' => 'boolean',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم المتحدث مطلوب',
            'name.max' => 'اسم المتحدث يجب ألا يتجاوز 255 حرف',
            'bio.required' => 'السيرة الذاتية مطلوبة',
            'bio.max' => 'السيرة الذاتية يجب ألا تتجاوز 2000 حرف',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف',
            'avatar_url.url' => 'رابط الصورة غير صحيح',
            'avatar_url.max' => 'رابط الصورة يجب ألا يتجاوز 500 حرف',
            'linkedin_url.url' => 'رابط LinkedIn غير صحيح',
            'linkedin_url.max' => 'رابط LinkedIn يجب ألا يتجاوز 500 حرف',
            'twitter_url.url' => 'رابط Twitter غير صحيح',
            'twitter_url.max' => 'رابط Twitter يجب ألا يتجاوز 500 حرف',
            'website_url.url' => 'رابط الموقع غير صحيح',
            'website_url.max' => 'رابط الموقع يجب ألا يتجاوز 500 حرف',
            'topics.array' => 'المواضيع يجب أن تكون مصفوفة',
            'topics.*.string' => 'كل موضوع يجب أن يكون نص',
            'topics.*.max' => 'كل موضوع يجب ألا يتجاوز 100 حرف',
            'company.max' => 'اسم الشركة يجب ألا يتجاوز 255 حرف',
            'position.max' => 'المنصب يجب ألا يتجاوز 255 حرف',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert boolean strings to actual booleans
        if ($this->has('is_featured')) {
            $this->merge([
                'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}

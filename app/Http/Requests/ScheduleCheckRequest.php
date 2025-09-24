<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleCheckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'location_id' => 'required|exists:locations,id',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
            'speaker_ids' => 'nullable|array',
            'speaker_ids.*' => 'exists:speakers,id',
            'event_id' => 'nullable|exists:events,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'location_id.required' => 'الموقع مطلوب',
            'location_id.exists' => 'الموقع المحدد غير موجود',
            'starts_at.required' => 'تاريخ البداية مطلوب',
            'starts_at.date' => 'تاريخ البداية غير صحيح',
            'starts_at.after' => 'تاريخ البداية يجب أن يكون في المستقبل',
            'ends_at.required' => 'تاريخ النهاية مطلوب',
            'ends_at.date' => 'تاريخ النهاية غير صحيح',
            'ends_at.after' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
            'speaker_ids.array' => 'المتحدثون يجب أن يكونوا مصفوفة',
            'speaker_ids.*.exists' => 'أحد المتحدثين المحددين غير موجود',
            'event_id.exists' => 'الفعالية المحددة غير موجودة',
        ];
    }
}

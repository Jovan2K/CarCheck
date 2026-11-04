<?php

namespace App\Http\Requests;

use App\Models\InspectionItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInspectionItemRequest extends FormRequest
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
            'category' => ['required', 'string', Rule::in(InspectionItem::CATEGORIES)],
            'item_name' => ['required', 'string', 'max:150'],
            'condition' => ['required', 'string', Rule::in(['Good', 'Warning', 'Critical'])],
            'estimated_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'defect_description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ];
    }
}

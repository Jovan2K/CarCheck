<?php

namespace App\Http\Requests;

use App\Models\InspectionItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInspectionRequest extends FormRequest
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
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'inspection_date' => ['required', 'date'],
            'general_notes' => ['nullable', 'string', 'max:3000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.category' => ['required', 'string', Rule::in(InspectionItem::CATEGORIES)],
            'items.*.item_name' => ['required', 'string', 'max:150'],
            'items.*.condition' => ['required', 'string', Rule::in(['Good', 'Warning', 'Critical'])],
            'items.*.estimated_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'items.*.defect_description' => ['nullable', 'string', 'max:1000'],
            'items.*.photo' => ['nullable', 'image', 'max:4096'],
        ];
    }

    /**
     * Custom Indonesian validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'car_id.required' => 'Silakan pilih unit mobil yang akan diinspeksi.',
            'car_id.exists' => 'Unit mobil yang dipilih tidak terdaftar di sistem.',
            'inspection_date.required' => 'Tanggal pelaksanaan inspeksi wajib diisi.',
            'items.required' => 'Checklist evaluasi komponen wajib diisi minimal untuk 6 area.',
            'items.*.condition.in' => 'Status kelayakan harus berupa Good, Warning, atau Critical.',
            'items.*.estimated_cost.min' => 'Estimasi biaya perbaikan tidak boleh bernilai negatif.',
        ];
    }
}

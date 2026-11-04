<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarRequest extends FormRequest
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
        $maxYear = (int) date('Y') + 1;

        return [
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1980', "max:{$maxYear}"],
            'license_plate' => ['required', 'string', 'max:20'],
            'transmission' => ['required', Rule::in(['Manual', 'Automatic', 'CVT', 'DCT'])],
            'mileage' => ['required', 'integer', 'min:0', 'max:2000000'],
            'listing_price' => ['required', 'numeric', 'min:1000000', 'max:999999999999'],
            'color' => ['nullable', 'string', 'max:100'],
            'fuel_type' => ['nullable', 'string', 'max:50'],
            'body_type' => ['nullable', 'string', 'max:60'],
            'engine_capacity' => ['nullable', 'string', 'max:50'],
            'vin_number' => ['nullable', 'string', 'max:100'],
            'engine_number' => ['nullable', 'string', 'max:100'],
            'ownership_type' => ['nullable', 'string', 'max:50'],
            'tax_expiry_date' => ['nullable', 'date'],
            'document_status' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'max:4096'],
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
            'brand.required' => 'Merek kendaraan wajib diisi (contoh: Toyota, Nissan, Honda).',
            'model.required' => 'Model/tipe kendaraan wajib diisi (contoh: Avanza Veloz, X-Trail).',
            'year.required' => 'Tahun pembuatan wajib diisi.',
            'year.min' => 'Tahun pembuatan minimal 1980.',
            'license_plate.required' => 'Nomor polisi (plat nomor) wajib diisi.',
            'transmission.required' => 'Tipe transmisi wajib dipilih.',
            'transmission.in' => 'Tipe transmisi tidak valid.',
            'mileage.required' => 'Jarak tempuh (odometer) wajib diisi.',
            'mileage.min' => 'Jarak tempuh tidak boleh bernilai negatif.',
            'listing_price.required' => 'Harga taksiran awal wajib diisi.',
            'listing_price.min' => 'Harga taksiran awal minimal Rp 1.000.000.',
        ];
    }
}

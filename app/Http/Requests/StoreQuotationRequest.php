<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:2000|max:2030',
            'car_registration' => 'nullable|string|regex:/^[A-Z]{3}[0-9]{3}[A-Z]$/|max:50',
            'car_valuation' => 'required|numeric|min:800000|max:1300000',
            'company_id' => 'required|exists:companies,id',
            'total_premium' => 'required|numeric|min:0',
            'excess_own_damage' => 'nullable|numeric|min:0',
            'excess_theft' => 'nullable|numeric|min:0',
            'breakdown' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'car_registration.regex' => 'Plate format must be like KCA001A',
            'car_valuation.min' => 'Minimum valuation is KES 800,000',
            'car_valuation.max' => 'Maximum valuation is KES 1,300,000',
        ];
    }
}

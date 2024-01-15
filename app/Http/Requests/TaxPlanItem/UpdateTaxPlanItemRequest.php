<?php

namespace App\Http\Requests\TaxPlanItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxPlanItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'amount' => 'required',
            'due_date' => 'nullable|date_format:Y-m-d',
            'payment_date' => 'nullable|date_format:Y-m-d',
            'period' => 'nullable|string|max:30',
            'in_one_c' => 'required|integer',
            'status_id' => 'required|integer',
        ];
    }
}

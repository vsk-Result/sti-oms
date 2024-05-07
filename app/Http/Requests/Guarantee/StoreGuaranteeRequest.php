<?php

namespace App\Http\Requests\Guarantee;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuaranteeRequest extends FormRequest
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
            'company_id' => 'required|integer',
            'object_id' => 'required|integer',
            'organization_id' => 'required|integer',
            'state' => 'nullable|string|max:255',
            'conditions' => 'nullable|string|max:1000',
            'amount' => 'required',
            'fact_amount' => 'required',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ];
    }
}

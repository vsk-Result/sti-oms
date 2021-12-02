<?php

namespace App\Http\Requests\BankGuarantee;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankGuaranteeRequest extends FormRequest
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
            'bank_id' => 'required|integer',
            'object_id' => 'required|integer',
            'target' => 'nullable|string|max:50',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'amount' => 'required|numeric',
            'start_date_deposit' => 'nullable|date_format:Y-m-d',
            'end_date_deposit' => 'nullable|date_format:Y-m-d',
            'amount_deposit' => 'nullable|numeric',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ];
    }
}

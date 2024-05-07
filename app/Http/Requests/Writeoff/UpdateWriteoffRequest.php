<?php

namespace App\Http\Requests\Writeoff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWriteoffRequest extends FormRequest
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
            'date' => 'nullable|date_format:Y-m-d',
            'crm_employee_uid' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:1500',
            'amount' => 'required',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            'status_id' => 'required|integer',
        ];
    }
}

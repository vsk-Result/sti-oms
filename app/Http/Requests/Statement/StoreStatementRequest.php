<?php

namespace App\Http\Requests\Statement;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatementRequest extends FormRequest
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
            'date' => 'required|date_format:Y-m-d',
            'file' => 'required|file|mimes:xls,xlsx|max:2048',
        ];
    }
}

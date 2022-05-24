<?php

namespace App\Http\Requests\Object;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateObjectRequest extends FormRequest
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
            'code' => [
                'required', 'string', 'max:4', Rule::unique('objects')->ignore($this->object->id)
            ],
            'name' => 'required|string|max:120',
            'address' => 'nullable|string|max:200',
            'responsible_name' => 'nullable|string|max:50',
            'responsible_email' => 'nullable|string|max:50',
            'responsible_phone' => 'nullable|string|max:50',
            'photo' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'status_id' => 'integer',
            'closing_date' => 'nullable|date_format:Y-m-d',
        ];
    }
}

<?php

namespace App\Http\Requests\Object;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateObjectRequest extends FormRequest
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
            'code' => 'required|string|max:4|unique:objects',
            'name' => 'required|string|max:120',
            'address' => 'nullable|string|max:200',
            'photo' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'status_id' => 'integer'
        ];
    }
}

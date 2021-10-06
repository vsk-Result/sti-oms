<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'statement_id' => 'integer',
            'company_id' => 'required|integer',
            'bank_id' => 'integer',
            'object_id' => 'required|string',
            'organization_sender_id' => 'required|integer',
            'organization_receiver_id' => 'required|integer',
            'type_id' => 'integer',
            'payment_type_id' => 'required|integer',
            'category' => 'string|max:20',
            'description' => 'string|max:1000',
            'code' => 'string|max:6',
            'date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric',
            'status_id' => 'integer',
        ];
    }
}

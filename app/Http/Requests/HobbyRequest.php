<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class HobbyRequest extends FormRequest
{

    public function rules()
    {
        return [
            'hobby' => 'required',
        ];

    }

    public function failedValidation(Validator $validator)
    {

        $response = [
            'success' => false,
            'message' => implode(',', $validator->errors()->all()),
            'data' => ''
        ];

        throw new HttpResponseException(response()->json($response, 422));
    }

    public function messages()
    {
        return [];
    }

}

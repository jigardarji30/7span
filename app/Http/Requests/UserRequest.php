<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{

    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'user_photo' => 'required|mimes:jpeg,jpg,png,gif',
            'mobile_no' => 'required|regex:/(91)[0-9]{9}/',
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

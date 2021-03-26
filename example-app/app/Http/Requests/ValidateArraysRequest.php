<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ValidateArraysRequest extends FormRequest
{
    protected $errors;

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
            'users.*.id' => 'required|min:1|max:2'
        ];
    }

    /**
     * Override default error handler with redirection (only for testing purpose)
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $this->errors = $validator->errors();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

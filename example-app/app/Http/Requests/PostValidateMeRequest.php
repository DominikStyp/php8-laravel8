<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class PostValidateMeRequest extends FormRequest
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
            'name' => 'required|min:5|max:100',
            'email' => 'required|email:rfc,dns'
        ];
    }


    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // we can add "after hook" here

        $validator->after(function ($validator) {
                /** @var MessageBag $bag */
                // $bag = $validator->errors();
                // $bag->messages();

                /** @var $validator \Illuminate\Validation\Validator */
                if($validator->errors()->count() > 0){
                    $validator->messages()->merge([
                        'email' => [
                            'POST_VALIDATE_ADDED_MESSAGE'
                        ]
                    ]);
                }
        });
    }

}

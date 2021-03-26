<?php

namespace Tests\Feature;

use App\Models\User;
use App\Rules\MatchesEnvVar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class ValidatorMakeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_array_validation()
    {
        $rules = [
            'users' => 'required|array',
            'users.*.id' => 'required|distinct:strict'
        ];
        $data = [
            'users' => [
                ['id' => 1],
                ['id' => 2],
                ['id' => 4],
                ['id' => 4],
            ]
        ];
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        //dump($validator->errors());
    }

    public function test_in_array_rule()
    {
        $data = [
            'v1' => 10,
            'v2' => [8,9,10],
            'v3' => [7,8,9]
        ];
        $rules = [
            'v1' => 'in_array:v2.*'
        ];
        $validator = Validator::make($data, $rules);
        $this->assertFalse( $validator->fails() );
        $this->assertEmpty( $validator->errors() );

        $rules = [
            'v1' => 'in_array:v3.*'
        ];
        $validator = Validator::make($data, $rules);
        $this->assertTrue( $validator->fails() );
        $this->assertNotEmpty( $validator->errors() );
        $this->assertEquals(
            "The v1 field does not exist in v3.*.",
            $validator->errors()->messages()['v1'][0]
        );
    }

    public function test_rule_unique()
    {

       $rules = [
           'email' => [
               Rule::unique('users')->ignore(1)
          ]
       ];
        $validator = Validator::make(['email' => User::find(1)->email], $rules);
        $this->assertFalse( $validator->fails() );

        $validator = Validator::make(['email' => User::find(2)->email], $rules);
        // fails because user(2)->email exists in database and is not ignored
        $this->assertTrue( $validator->fails() );
    }

    public function test_sometimes_conditional_rule_not_applied()
    {
        $rules = [
            'points' => 'required|numeric'
        ];
        $validator = Validator::make(['points' => 100], $rules);

        $validator->sometimes('club_member', 'required|boolean', function($input){
            return $input->points > 300;
        });
        // user does not need to check if he wants to be a club member below 300 points
        $this->assertFalse( $validator->fails() );
    }

    public function test_sometimes_conditional_rule_applied()
    {
        $rules = [
            'points' => 'required|numeric'
        ];
        $validator = Validator::make(['points' => 301], $rules);

        $validator->sometimes('club_member', 'required|boolean', function($input){
            return $input->points > 300;
        });
        // user NEEDS to check if he wants to be a club member with more than 300 points
        $this->assertTrue( $validator->fails() );
        //dump($validator->errors()->messages());
    }

    public function test_matches_custom_rule()
    {
        $rules = [
            'pusher_app_key' => new MatchesEnvVar('PUSHER_APP_KEY')
        ];
        $data = [
            'pusher_app_key' => env('PUSHER_APP_KEY')
        ];

        $validator = Validator::make($data, $rules);
        $this->assertFalse( $validator->fails() );

        //dump($validator->errors()->messages());
    }

    public function test_DOES_NOT_match_custom_rule()
    {
        $rules = [
            'pusher_app_key' => new MatchesEnvVar('PUSHER_APP_KEY')
        ];
        $data = [
            'pusher_app_key' => env('PUSHER_APP_KEY') . 'x'
        ];

        $validator = Validator::make($data, $rules);
        $this->assertTrue( $validator->fails() );

       // dump($validator->errors()->messages());
    }

    public function test_custom_foo_validator()
    {
        $rules = [
            'test_var' => 'required|dominik_rule'
        ];
        $data = [
            'test_var' => 'this_is_dominik_rule'
        ];

        $validator = Validator::make($data, $rules);
        $this->assertFalse( $validator->fails() );
    }

    public function test_custom_foo_validator_FAILS()
    {
        $rules = [
            'test_var' => 'required|dominik_rule'
        ];
        $data = [
            'test_var' => 'this_is_dominik_rule_SHOULD_FAIL'
        ];

        $validator = Validator::make($data, $rules);
        $this->assertTrue( $validator->fails() );

        dump($validator->errors()->messages());
    }
}

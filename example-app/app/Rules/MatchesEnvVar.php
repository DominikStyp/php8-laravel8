<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MatchesEnvVar implements Rule
{
    private $env;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($env)
    {

        $this->env = $env;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return env($this->env) === $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ":attribute does not match {$this->env}";
    }
}

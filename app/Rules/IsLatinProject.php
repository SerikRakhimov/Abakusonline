<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsLatinProject implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        //
        $chr_ru = "A-Za-z0-9\s`~!@#$%^&*()_+-={}|:;<>?,.\/\"\'\\\[\]";

        return (preg_match("/^[$chr_ru]+$/u", $value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('main.only_latin_characters_numbers_and_special_characters_are_allowed') . '.';
    }

}

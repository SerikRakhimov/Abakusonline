<?php

namespace App\Rules;

use App\Models\Set;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class IsUniqueSet implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !Set::where('serial_number', $this->request->serial_number)->
        where('line_number', $this->request->line_number)->
        where('link_from_id', $this->request->link_from_id)->
        where('link_to_id', $this->request->link_to_id)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('main.uniqueness_of_fields_violated') .
            ': ' . trans('main.serial_number') .
            ', ' . trans('main.line_number') .
            ', ' . trans('main.link_from') .
            ',  ' . trans('main.link_to') . '.';
    }
}

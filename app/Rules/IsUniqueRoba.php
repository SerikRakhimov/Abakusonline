<?php

namespace App\Rules;

use App\Models\Roba;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class IsUniqueRoba implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $request;

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
        return !Roba::where('role_id', $this->request->role_id)->where('base_id', $this->request->base_id)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('main.uniqueness_of_fields_violated') . ' ' . trans('main.role') . ' ' . trans('main.base') . '.';
    }
}

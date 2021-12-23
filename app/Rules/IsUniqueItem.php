<?php

namespace App\Rules;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Item;

class IsUniqueItem implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $request, $project_id, $base_id;

    public function __construct(Request $request, $project_id, $base_id)
    {
        $this->request = $request;
        $this->project_id = $project_id;
        $this->base_id = $base_id;
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
        return !Item::where('project_id', $this->project_id)->where('base_id', $this->base_id)->where('code', $this->request->code)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('main.uniqueness_of_fields_violated') . ' ' . trans('main.project') . ' ' . trans('main.base') . ' ' . trans('main.code') . '.';
    }

}

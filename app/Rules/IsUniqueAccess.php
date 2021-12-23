<?php

namespace App\Rules;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Access;

class IsUniqueAccess implements Rule
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
        return !Access::where('project_id', $this->request->project_id)->where('user_id', $this->request->user_id)->where('role_id', $this->request->role_id)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('main.uniqueness_of_fields_violated') . ' ' . trans('main.project') . ' ' . trans('main.user') . ' ' . trans('main.role') . '.';
    }
}

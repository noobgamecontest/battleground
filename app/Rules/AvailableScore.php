<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AvailableScore implements Rule
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
     * Check if score is not same value for
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->noOpponent($value)) {
            return true;
        }

        $value = array_unique($value);

        return count($value) > 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Il n'est pas possible que les équipes ont le même score";
    }

    /**
     * @param array $value
     * @return bool
     */
    protected function noOpponent(array $value)
    {
        return count($value) === 1;
    }
}

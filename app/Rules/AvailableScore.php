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
     * @param  mixed  $scores
     * @return bool
     */
    public function passes($attribute, $scores)
    {
        if ($this->noOpponent($scores)) {
            return true;
        }

        $scores = array_unique($scores);

        return count($scores) > 1;
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
     * @param array $scores
     * @return bool
     */
    protected function noOpponent(array $scores)
    {
        return count($scores) === 1;
    }
}

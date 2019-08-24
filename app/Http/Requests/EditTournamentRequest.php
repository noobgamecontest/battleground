<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditTournamentRequest extends FormRequest
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
            'name' => [
                'required',
                Rule::unique('tournaments')->ignore($this->tournament),
            ],
            'slots' => 'nullable|integer|min:0',
            'opponents_by_match' => 'nullable|integer|min:0',
            'winners_by_match' => 'nullable|integer|min:0',
        ];
    }
}

<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class LoanApplyRequest extends FormRequest
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
            'amount' => 'required|min:1',
            'term' => 'required|min:1',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Please specify amount',
            'amount.min' => 'Minimum amount should be 1',
            'term.required' => 'Please specify loan term',            
            'term.min' => 'Loan should be given for at least 1 week'
        ];
    }    
}

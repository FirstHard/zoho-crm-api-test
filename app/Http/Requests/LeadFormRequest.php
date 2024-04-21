<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dealName' => 'required|string',
            'dealStage' => 'required|string',
            'dealClosingDate' => 'required|date',
            'accountName' => 'required|string',
            'accountWebsite' => 'required|url',
            'accountPhone' => 'required|string|regex:/^\+(?:[0-9] ?){6,14}[0-9]$/',
        ];
    }
}

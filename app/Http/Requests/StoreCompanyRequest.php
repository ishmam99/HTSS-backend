<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
      public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required','string','max:255'],
            'address' => ['nullable','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:50'],
            'website' => ['nullable','url','max:255'],
        ];
    }
}

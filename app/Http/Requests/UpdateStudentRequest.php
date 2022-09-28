<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'firstname' => ['nullable', 'max:120'],
            'lastname' => ['nullable', 'max:120'],
            'email' => ['nullable', 'max:255', 'email'],
            'address' => ['nullable', 'max:255'],
            'score' => ['nullable', 'min:0', 'numeric'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ];
    }
}

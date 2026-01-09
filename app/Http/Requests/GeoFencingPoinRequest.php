<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeoFencingPoinRequest extends FormRequest
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
            'plant_id' => 'required|exists:plants,id',
            'gate_count'=>'required',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'reason' => 'nullable|string|max:255',
         ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
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
            // 'vehicle_no' => 'required|string|max:50|unique:vehicles,vehicle_no,' . $this->id,
            'vehicle_type' => 'nullable|string|max:100',
            'driving_license_no' => 'required|string|max:150',
            'name' => 'nullable|string|max:100',
           // 'pass_no' => 'required|regex:/^[A-Za-z]{4}[0-9]{4}$/',
            'driving_license_validity' => 'nullable|date',
            'puc_validity'=>'required|date',
            'insurance_validity' =>'required|date',
            'email_id'=>'required|email',
            // 'driving_licence_validity' =>'required|date',
            'rc_validity' =>'required|date',
            'residence' =>'nullable',
            'contact_no'=>'required|numeric|digits:10',
            'emp_code'=>'required|string',
            // 'emp_code' => [
            // 'required',
            // 'string',
            // 'max:50',
            //  Rule::unique('vehicles', 'emp_code')->ignore($this->route('vehicles')),],
            'vehicle_no' => [
            'required',
            'string',
            'max:50',
             Rule::unique('vehicles', 'vehicle_no')->ignore($this->route('vehicle')),],

            ];
            
    }
    
  public function messages(){
    return [
        'pass_no.regex' => 'Pass number are not format in ASVP0000 (4 letters + 4 digits).',
    ];
  }
}

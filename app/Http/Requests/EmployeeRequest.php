<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
            'emp_code' => [
            'required',
            'string',
            'max:50',
             Rule::unique('empolyees', 'emp_code')->ignore($this->route('employee')),],
            'emp_name' => 'required|string|max:150',
            'emp_email_id' => 'required|string|max:150',
            'site_area_id' => 'required|string|max:150',
            'plant_id' => 'required|string|max:150',
            'department_id' => 'required|string|max:150',
            'emp_mobile_no' => 'required|numeric|digits:10',


        ];
    }
}

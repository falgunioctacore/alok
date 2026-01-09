<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Vehicle;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class VehicleImport implements ToModel, WithHeadingRow
{
    /**
     * Map each row to the Vehicle model
     */
    public function model(array $row)
    {
   

        if (empty($row['vehiclenumber']) || empty($row['employeecode'])) {
            return null;
        }


        // Find employee by code
        // $employee = Employee::where('emp_code', trim($row['employee_code']))->first();

        return new Vehicle([
            'vehicle_no'             => str_replace(' ', '', trim($row['vehiclenumber'])),
            'vehicle_type'           => trim($row['vehicletype']),
            'emp_code'               => trim($row['employeecode']),
            // 'pass_no'                => trim($row['passno']),
            'name'                   => trim($row['name']),
            'contact_no'             => trim($row['mobileno']),
            'email_id'               => trim($row['email']),
            'driving_license_no'     => trim($row['drivinglicenseno']),
            'driving_license_validity'=> $this->formatDate($row['drivinglicensevalidity']),
            'rc_validity'            => $this->formatDate($row['rcvalidity']),
            'puc_validity'           => $this->formatDate($row['pucvalidity']),
            'insurance_validity'     => $this->formatDate($row['insurancevalidity']),
            'residence'              => trim($row['residence']),
        ]);
    }

    /**
     * Convert Excel date / string to Y-m-d
     */
    protected function formatDate($value)
    {
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        return date('Y-m-d', strtotime($value));
    }

    /**
     * Validation rules for each row
     */
    // public function rules(): array
    // {
    //     return [
    //         '*.vehicle_no' => ['required', 'string', 'max:50'],
    //         '*.vehicle_type' => ['required', 'string'],
    //         '*.employee_code' => ['required', 'string'],
    //         '*.email' => ['nullable', 'email'],
    //     ];
    // }
}

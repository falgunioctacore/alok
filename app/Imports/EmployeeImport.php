<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Employee;
use App\Models\GeoFencingPoint;
use App\Models\Plant;
use App\Models\SiteArea;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     */
    public function model(array $row)
    {
        // Skip if both name and emp_code are empty
        if (empty($row['name']) && empty($row['emp_code'])) {
            return null;
        }

        // --- Site Area ---
        $site = SiteArea::firstOrCreate(['name' => trim($row['site'])]);

        // --- Plant ---
        $plant = Plant::firstOrCreate(['name' => trim($row['plant'])]);

        // --- Department ---
        $department = Department::firstOrCreate(['name' => trim($row['department'])]);

        // --- Employee ---
        $employee = Employee::updateOrCreate(
            ['emp_code' => trim($row['emp_code'])],
            [
                'emp_name'       => trim($row['name']),
                'emp_email_id'   => trim($row['email_id']),
                'site_area_id'   => $site->id,
                'plant_id'       => $plant->id,
                'department_id'  => $department->id,
                'emp_mobile_no'  => trim($row['mobile_no']),
            ]
        );

        // --- Geo Fencing Assign ---
        $geoPoints = GeoFencingPoint::where('plant_id', $plant->id)->pluck('id')->toArray();
        // dd($geoPoints);
        if (!empty($geoPoints)) {
            $employee->geoFencingPoints()->sync($geoPoints);
        }

        return $employee;
    }
}

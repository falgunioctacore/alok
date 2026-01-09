<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'name',
            'emp_code',
            'email_id',
            'site',
            'plant',
            'department',
            'mobile_no',
        ];
    }
}

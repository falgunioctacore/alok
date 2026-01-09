<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepartmentExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Department::select('name')->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'Name'
        ];
    }
}

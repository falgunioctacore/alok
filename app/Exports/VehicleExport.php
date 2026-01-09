<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class VehicleExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'vehiclenumber',
            'vehicletype',
            'employeecode',
            // 'passno',
            'name',
            'mobileno',
            'email',
            'drivinglicenseno',
            'drivinglicensevalidity',
            'rcvalidity',
            'pucvalidity',
            'insurancevalidity',
            'residence',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Plant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlantExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Plant::select('name')->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'Name'
        ];
    }
}

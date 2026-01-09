<?php

namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class DepartmentImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    public function model(array $row)
    {
        $name = isset($row['name']) ? trim($row['name']) : null;
        if (empty($name)) return null;

        return Department::updateOrCreate(
            ['name' => $name],
            ['name' => $name]
        );
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'input_encoding' => 'UTF-8',
        ];
    }
}

<?php

namespace App\Imports;

use App\Models\Plant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Log;

class PlantImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    /**
     * @param array $row
     */
    public function model(array $row)
    {
        $name = isset($row['name']) ? trim($row['name']) : null;
        if (empty($name)) {
            return null;
        }

        // Use updateOrCreate so existing records are preserved and not truncated.
        return Plant::updateOrCreate(
            ['name' => $name],
            ['name' => $name]
        );
    }

    /**
     * Ensure CSV reader uses comma delimiter and proper enclosure so spaces are preserved.
     */
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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Services\QrService;
use Illuminate\Support\Facades\DB;

class GenerateEmployeeIds extends Command
{
    protected $signature = 'employee:generate-ids';
    protected $description = 'Generate employee_id for existing employees without one';

    public function handle()
    {
        $year = now()->year;

        // Get current sequence for year
        $sequence = DB::table('employee_sequences')->where('year', $year)->lockForUpdate()->first();
        $lastNumber = $sequence ? $sequence->last_number : 0;

        // Get employees without employee_id
        $employees = Employee::whereNull('employee_id')->orderBy('created_at')->get();

        DB::transaction(function () use ($employees, $year, &$lastNumber, $sequence) {
            foreach ($employees as $employee) {
                $lastNumber++;
                $employee->employee_id = 'EMP' . $year . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
                $employee->save();
                app(QrService::class)->generate($employee);
            }

            // Update sequence table
            if ($sequence) {
                DB::table('employee_sequences')->where('year', $year)->update(['last_number' => $lastNumber]);
            } else {
                DB::table('employee_sequences')->insert([
                    'year' => $year,
                    'last_number' => $lastNumber
                ]);
            }
        });

        $this->info("Employee IDs generated for " . count($employees) . " employees.");
    }
}

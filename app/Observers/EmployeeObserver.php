<?php

namespace App\Observers;

use App\Models\Employee;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

// use SimpleSoftwareIO\QrCode\Facades\QrCode;
// use SimpleSoftwareIO\QrCode\Writer\PngWriter;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
         $year = now()->year;
          DB::transaction(function () use ($employee, $year) {
            // Lock the sequence row for update to avoid race conditions
            $sequence = DB::table('employee_sequences')->where('year', $year)->lockForUpdate()->first();

            $lastNumber = $sequence ? $sequence->last_number : 0;
            $newNumber = $lastNumber + 1;

            // Generate employee_id
            $employee->employee_id = 'EMP' . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            // Update or insert sequence
            if ($sequence) {
                DB::table('employee_sequences')->where('year', $year)->update(['last_number' => $newNumber]);
            } else {
                DB::table('employee_sequences')->insert([
                    'year' => $year,
                    'last_number' => $newNumber
                ]);
            }
        });
    
        $this->generateQR($employee);

    }
    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
    //    if ($employee->wasChanged(['employee_code','emp_name','mobile','emai_id'])) {
            $this->generateQR($employee);
        // }
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }

 private function generateQR($employee)
    {
        $data = json_encode([
            'type' => 'plant',
            'code' => $employee->emp_code,
            'name' => $employee->emp_name,
            'mobile' => $employee->emp_mobile_no,
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($data)
            ->size(300)
            ->margin(10)
            ->build();

        $path = "qrcodes/{$employee->emp_code}.png";

        Storage::disk('public')->put($path, $result->getString());
        
        $employee->qr_code_path = $path;
        $employee->saveQuietly();
    }

}

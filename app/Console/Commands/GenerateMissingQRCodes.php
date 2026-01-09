<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Services\QrService;

class GenerateMissingQRCodes extends Command
{
    protected $signature = 'qr:generate-all';
    protected $description = 'Generate QR codes for all employees and vehicles that do not have QR codes';

    public function handle(QrService $qr)
    {
        $this->info("Generating Employee QR Codes...");

        $employees = Employee::all();
        foreach ($employees as $emp) {
            $qr->generate($emp);
        }

        $this->info("Generating Vehicle QR Codes...");

        $vehicles = Vehicle::all();
        foreach ($vehicles as $vehicle) {
            $qr->generateVehicleQR($vehicle);
        }

        $this->info("QR Generation Complete!");
    }
}

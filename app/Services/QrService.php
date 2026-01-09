<?php 

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class QrService
{
    public function generate($employee)
    {
        $data = json_encode([
            'type'=>'Plant',
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

     public function generateVehicleQR($vehicle){
        $passno='ASVP' . sprintf('%04d', $vehicle->pass_no);
        $data = json_encode([
            'type'=>'Vehicle',
            'code' => $vehicle->vehicle_no,
            'vehicle_type' => $vehicle->vehicle_type,
            'emp_code' => $vehicle->emp_code,
            'name' => $vehicle->name,
            'mobile_no'=>$vehicle->contact_no,
            'pass_no'=>$passno,
    
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($data)
            ->size(300)
            ->margin(10)
            ->build();

        $path = "qrcodes/{$vehicle->vehicle_no}.png";

        Storage::disk('public')->put($path, $result->getString());
        
        $vehicle->qr_code_path = $path;
        $vehicle->saveQuietly();
    }
}

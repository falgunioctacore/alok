<?php

namespace App\Observers;

use App\Models\Vehicle;
use App\Services\QrService;

class VehicleObserver
{
    /**
     * Handle the Vehicle "created" event.
     */
     
     public function creating(Vehicle $vehicle){
         // Get the latest pass_no, or 0 if none exists
        //  $latestPassNo = Vehicle::max('pass_no') ?? 0;
     
        //  // Increment for the new vehicle
        //  $vehicle->pass_no = (int)$latestPassNo + 1;
        
         $latestPassNo = Vehicle::selectRaw('MAX(CAST(pass_no AS UNSIGNED)) as max_pass')
                       ->value('max_pass') ?? 0;

         $vehicle->pass_no = $latestPassNo + 1;

       }

    public function created(Vehicle $vehicle): void
    {
        app(QrService::class)->generateVehicleQR($vehicle);
    }

    /**
     * Handle the Vehicle "updated" event.
     */
    public function updated(Vehicle $vehicle): void
    {
        app(QrService::class)->generateVehicleQR($vehicle);
    }

    /**
     * Handle the Vehicle "deleted" event.
     */
    public function deleted(Vehicle $vehicle): void
    {
        //
    }

    /**
     * Handle the Vehicle "restored" event.
     */
    public function restored(Vehicle $vehicle): void
    {
        //
    }

    /**
     * Handle the Vehicle "force deleted" event.
     */
    public function forceDeleted(Vehicle $vehicle): void
    {
        //
    }
}

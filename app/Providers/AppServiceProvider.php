<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\Vehicle;
use App\Observers\EmployeeObserver;
use App\Observers\VehicleObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Employee::observe(EmployeeObserver::class);
        Vehicle::observe(VehicleObserver::class);
    }
}

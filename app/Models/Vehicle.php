<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    
    protected $casts = [
      'pass_no' => 'integer',
    ];

    public function vehicleAttendances(){
        return $this->hasMany(VehicleAttendance::class,'vehicle_id');
    }
    // public function geoFencingPoints(){
    //     return $this->belongsToMany(GeoFencingPoint::class, 'permitted_employees', 'employee_id', 'geo_fencing_point_id');
    // }
}

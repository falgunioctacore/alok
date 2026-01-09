<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeoFencingPoint extends Model
{
    use SoftDeletes;
    protected $table="geo_fencing_points";
    protected $guarded = [];
    public function plant(){
        return $this->belongsTo(Plant::class);
    }

    public function attendance(){
        return $this->hasMany(Attendance::class,'geo_fencing_point_id');
    }

    public function employees(){
     return $this->belongsToMany(Employee::class, 'permitted_employees', 'employee_id', 'geo_fencing_point_id');
    }

    public function vehicleAttendance(){
        return $this->hasOne(VehicleAttendance::class,'geo_fencing_point_id');
    }


    
}

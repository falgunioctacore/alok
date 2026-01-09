<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];

    protected $table = 'employee_attendances';
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function inGeoFencingPoint() {
        return $this->belongsTo(GeoFencingPoint::class, 'in_geo_fencing_point_id');
    }

    public function outGeoFencingPoint() {
        return $this->belongsTo(GeoFencingPoint::class, 'out_geo_fencing_point_id');
    }

    public function geoFencingPoint()
    {
        return $this->belongsTo(GeoFencingPoint::class,'geo_fencing_point_id');
    }


}

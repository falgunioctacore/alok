<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleAttendance extends Model
{
    protected $table = "vehicles_attendances";
    protected $guarded = [];
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function inGeoFencingPoint() {
        return $this->belongsTo(GeoFencingPoint::class, 'in_geo_fencing_point_id');
    }

    public function outGeoFencingPoint() {
        return $this->belongsTo(GeoFencingPoint::class, 'out_geo_fencing_point_id');
    }

    public function geoFencingPoint() {
        return $this->belongsTo(GeoFencingPoint::class, 'geo_fencing_point_id');
    }



}

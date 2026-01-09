<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plant extends Model
{
    use SoftDeletes;
     protected $guarded = [];

     public function geoFencingPoints()
    {
        return $this->hasMany(GeoFencingPoint::class);
    }

    public function employees(){
        return $this->hasMany(Employee::class,'plant_id');
    }
}

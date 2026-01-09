<?php

namespace App\Models;

use App\Http\Controllers\DepartmentController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $table='empolyees';
    protected $guarded =[];

    public function department(){
        return $this->belongsTo(Department::class,'department_id');
    }
    
    public function siteArea(){
        return $this->belongsTo(SiteArea::class,'site_area_id');
    }

    public function plant(){
        return $this->belongsTo(Plant::class,'plant_id');
    }

    public function geoFencingPoints(){
        return $this->belongsToMany(GeoFencingPoint::class, 'permitted_employees', 'employee_id', 'geo_fencing_point_id');
    }

    public function attendances(){
        return $this->hasMany(Attendance::class);
    }


}

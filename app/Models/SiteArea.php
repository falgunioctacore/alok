<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteArea extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function employees(){
        return $this->hasMany(Employee::class,'site_area_id');
    }
}

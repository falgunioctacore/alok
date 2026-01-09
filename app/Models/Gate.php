<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gate extends Model
{
   use SoftDeletes;
   protected $guarded = [];
}

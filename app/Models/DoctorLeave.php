<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorLeave extends Model
{
    protected $fillable = ['staffId','start_date','end_date','reason','status'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];
}



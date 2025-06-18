<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationBlock extends Model
{
    protected $fillable = ['name', 'start_hour', 'end_hour', 'days_of_week'];

    protected $casts = [
        'days_of_week' => 'array',
    ];
}

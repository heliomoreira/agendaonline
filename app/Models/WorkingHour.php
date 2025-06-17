<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    protected $table = 'professional_working_hours';

    protected $fillable = ['professional_id', 'weekday', 'start_hour', 'end_hour'];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}

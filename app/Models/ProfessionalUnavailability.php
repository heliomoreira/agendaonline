<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalUnavailability extends Model
{
    protected $fillable = [
        'professional_id',
        'day',
        'start_hour',
        'end_hour',
        'reason',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function isFullDay()
    {
        return is_null($this->start_hour) && is_null($this->end_hour);
    }
}

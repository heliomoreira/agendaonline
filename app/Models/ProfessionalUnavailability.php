<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalUnavailability extends Model
{
    protected $fillable = ['professional_id', 'day', 'reason'];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}

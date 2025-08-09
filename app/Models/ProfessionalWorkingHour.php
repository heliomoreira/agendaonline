<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalWorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'professional_id',
        'weekday',
        'start_hour',
        'end_hour'
    ];

    protected $casts = [
        'start_hour' => 'datetime:H:i',
        'end_hour' => 'datetime:H:i',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}

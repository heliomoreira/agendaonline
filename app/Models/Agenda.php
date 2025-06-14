<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{

    protected  $table = 'agenda';
    protected $fillable = [
        'client_id',
        'professional_id',
        'service_id',
        'day',
        'start_hour',
        'end_hour',
        'notes',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

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

    public function scopeUpcoming($query, $limit = 5)
    {
        $now = now();

        return $query->where(function ($query) use ($now) {
            $query->where('day', '>', $now->toDateString())
                ->orWhere(function ($q) use ($now) {
                    $q->where('day', $now->toDateString())
                        ->where('start_hour', '>=', $now->toTimeString());
                });
        })
            ->orderBy('day')
            ->orderBy('start_hour')
            ->take($limit);
    }
}

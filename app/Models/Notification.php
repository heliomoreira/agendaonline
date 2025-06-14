<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'tenant_id',
        'sender',
        'destinatary',
        'type',
        'text',
        'service_name',
        'service_day',
        'service_start_hour',
        'service_end_hour',
        'status'
    ];

    /*  public function sender()
      {
          return $this->belongsTo(User::class, 'sender');
      }

      public function destinatary()
      {
          return $this->belongsTo(User::class, 'destinatary');
      }*/
}

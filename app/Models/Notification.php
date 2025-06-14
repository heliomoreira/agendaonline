<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'sender',
        'destinatary',
        'type',
        'text',
        'status',
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

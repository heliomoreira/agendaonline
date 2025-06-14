<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable, softDeletes;

    protected $fillable = [
        'name', 'phone_1', 'phone_2', 'email', 'address', 'number_port', 'zip_code',
        'city', 'district_id', 'county_id', 'marketing_allowed', 'type','birthdate', 'notes',
    ];
}

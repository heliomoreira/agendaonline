<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'phone_1', 'phone_2', 'email', 'address', 'number_port', 'zip_code',
        'city', 'district_id', 'county_id', 'marketing_allowed', 'type','birthdate', 'notes',
    ];
}

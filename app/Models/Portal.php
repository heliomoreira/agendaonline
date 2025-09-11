<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    protected $table = 'portal';

    protected $fillable = ['title', 'logo', 'main_color', 'secondary_color'];
}

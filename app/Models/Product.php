<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use softDeletes;

    protected $fillable = ['name', 'description', 'price_1', 'price_2', 'stock_movement', 'image', 'notes'];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    protected $table = 'portal';

    protected $fillable = [
        'title',
        'subtitle',
        'address',
        'postal_code',
        'city',
        'phone_1',
        'phone_2',
        'email',
        'about_us',
        'logo',
        'main_color',
        'secondary_color',
        'button_background_color',
        'button_text_color',
        'requires_payment',
        'payment_percentage',
        'background_image'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    protected $fillable = ['name', 'color', 'is_paid', 'is_default', 'order'];

    protected $casts = ['is_paid' => 'boolean', 'is_default' => 'boolean'];

    public function agenda()
    {
        return $this->hasMany(Agenda::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    public static function default(): ?self
    {
        return static::where('is_default', true)->first() ?? static::ordered()->first();
    }
}

<?php

namespace App\Models;

use App\Enums\ClientValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{

    protected $fillable = [
        'client_validation',
        'booking_allow_overlap',
        'sms_send_hour',
        'sms_advance_days'
    ];

    protected $casts = [
        'booking_allow_overlap' => 'boolean',
        'client_validation' => ClientValidation::class,
        'sms_advance_days' => 'integer',
    ];

    public static function current(): self
    {
        return Cache::rememberForever('settings.current', fn() => static::firstOrCreate([], self::defaults())
        );
    }

    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget('settings.current'));
    }

    protected static function defaults(): array
    {
        return [
            'client_validation' => null,
            'booking_allow_overlap' => false,
            'sms_send_hour' => '18:00:00',
            'sms_advance_days' => 1,
        ];
    }
}

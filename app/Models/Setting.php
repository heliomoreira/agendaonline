<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{

    protected $fillable = [
        'client_validation',
        'allow_overlap'
    ];

    protected $casts = [
        'allow_overlap' => 'boolean',
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
            'client_validation' => 'email',
            'allow_overlap' => false,
        ];
    }
}

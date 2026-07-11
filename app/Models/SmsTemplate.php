<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'sms_templates';

    protected $fillable = [
        'title', 'body', 'order', 'default', 'status'
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'date',
        ];
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}

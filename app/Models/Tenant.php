<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'vat',
        'address',
        'number_port',
        'zip_code',
        'county',
        'city',
        'country',
        'phone_1',
        'phone_2',
        'email',
        'sms_status',
        'sms_sender',
        'sms_credits',
        'logo',
        'main_color',
        'secondary_color',
        'is_beta',
        'storage_token',
        'storage_quota',
        'plan_id',
        'plan_end_date',
        'booking_available',
        'status',
    ];

    protected $casts = [
        'sms_status' => 'boolean',
        'sms_credits' => 'integer',
        'is_beta' => 'boolean',
        'storage_quota' => 'integer',
        'plan_id' => 'integer',
        'plan_end_date' => 'date',
        'booking_available' => 'boolean',
        'status' => 'boolean',
    ];

    public function plan()
    {
        //return $this->belongsTo(Plan::class);
    }

    public static function getCustomColumns(): array
    {
        return array_merge(parent::getCustomColumns(), [
            'id',
            'name',
            'username',
            'vat',
            'address',
            'number_port',
            'zip_code',
            'county',
            'city',
            'country',
            'phone_1',
            'phone_2',
            'email',
            'sms_status',
            'sms_sender',
            'sms_credits',
            'logo',
            'main_color',
            'secondary_color',
            'is_beta',
            'storage_token',
            'storage_quota',
            'plan_id',
            'plan_end_date',
            'booking_available',
            'status',
        ]);
    }
}

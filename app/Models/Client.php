<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{

    use HasFactory, Notifiable, softDeletes, CanResetPassword;

    protected $fillable = [
        'name', 'phone_1', 'phone_2', 'phone_1_country_code',
        'phone_2_country_code', 'email', 'address', 'number_port', 'zip_code',
        'city', 'district_id', 'county_id', 'marketing_allowed', 'type', 'birthdate', 'notes',
        'password', 'is_minor',
        'parent_name',
        'parent_phone_1_country_code',
        'parent_phone_1',
        'parent_email',
        'parent_notes',
        'send_sms_to_parent',
        'parent_sms_template_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_minor' => 'boolean',
        'send_sms_to_parent' => 'boolean',
    ];

    /* ═══════════════════════════════════════
       RELATIONSHIPS
    ═══════════════════════════════════════ */

    public function bookings()
    {
        return $this->hasMany(Agenda::class, 'client_id');
    }

    /* ═══════════════════════════════════════
       SCOPES
    ═══════════════════════════════════════ */

    public function scopeWithAccount($query)
    {
        return $query->whereNotNull('password');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function parentSmsTemplate()
    {
        return $this->belongsTo(SmsTemplate::class, 'parent_sms_template_id');
    }

    /* ═══════════════════════════════════════
       METHODS
    ═══════════════════════════════════════ */

    public function hasAccount(): bool
    {
        return !is_null($this->password);
    }

    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function upcomingBookings()
    {
        return $this->bookings()
            ->where('day', '>=', now()->toDateString())
            ->whereIn('status', ['confirmed', 'pending', 'payment_pending'])
            ->with(['service', 'professional'])
            ->orderBy('day')
            ->orderBy('start_hour')
            ->get();
    }

    public function pastBookings()
    {
        return $this->bookings()
            ->where('day', '<', now()->toDateString())
            ->with(['service', 'professional'])
            ->orderBy('day', 'desc')
            ->orderBy('start_hour', 'desc')
            ->get();
    }
}

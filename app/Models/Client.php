<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable, softDeletes;

    protected $fillable = [
        'name', 'phone_1', 'phone_2', 'email', 'address', 'number_port', 'zip_code',
        'city', 'district_id', 'county_id', 'marketing_allowed', 'type','birthdate', 'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
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

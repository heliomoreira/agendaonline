<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Professional extends Model
{
    use HasFactory, Notifiable, softDeletes;

    protected $fillable = ['name', 'phone_1', 'phone_2', 'email','agenda_color', 'notes', 'order', 'status'];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
    public function statusLabel(): string
    {
        return $this->status ? 'Activo' : 'Inactivo';
    }

    public function statusClass(): string
    {
        return $this->status ? 'success' : 'danger';
    }
}

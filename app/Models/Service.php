<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Service extends Model
{
    use HasFactory, Notifiable, softDeletes;

    protected $fillable = ['name', 'duration', 'price', 'image', 'order', 'status', 'notes'];

    public function professionals()
    {
        return $this->belongsToMany(Professional::class);
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

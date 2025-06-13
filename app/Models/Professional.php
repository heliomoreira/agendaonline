<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Professional extends Model
{
    use softDeletes;

    protected $fillable = ['name', 'phone_1', 'phone_2', 'email', 'notes', 'order', 'status'];

    public function statusLabel(): string
    {
        return $this->status ? 'Activo' : 'Inactivo';
    }

    public function statusClass(): string
    {
        return $this->status ? 'success' : 'danger';
    }
}

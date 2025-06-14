<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, Notifiable, softDeletes;

    protected $fillable = ['product_code','name', 'description', 'price_1', 'price_2', 'stock_movement', 'image', 'notes'];

    public function statusLabel(): string
    {
        return $this->status ? 'Activo' : 'Inactivo';
    }

    public function statusClass(): string
    {
        return $this->status ? 'success' : 'danger';
    }
}

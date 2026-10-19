<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BetaSignup extends Model
{
    public const BUSINESS_TYPES = [
        'hair_barber' => 'Cabeleireiro ou barbearia',
        'beauty_wellness' => 'Estética e bem-estar',
        'clinic_practice' => 'Clínica ou consultório',
        'services_freelance' => 'Serviços ou profissional independente',
        'other' => 'Outro',
    ];

    public const TEAM_SIZES = [
        '1' => 'Só eu',
        '2-5' => '2 a 5',
        '6+' => '6 ou mais',
    ];

    protected $fillable = [
        'name',
        'email',
        'business',
        'business_type',
        'professionals',
        'consented_at',
        'ip',
        'user_agent',
        'invited_at',
    ];

    protected function casts(): array
    {
        return [
            'consented_at' => 'datetime',
            'invited_at' => 'datetime',
        ];
    }

    public function businessTypeLabel(): string
    {
        return self::BUSINESS_TYPES[$this->business_type] ?? $this->business_type;
    }

    public function teamSizeLabel(): ?string
    {
        return $this->professionals ? (self::TEAM_SIZES[$this->professionals] ?? $this->professionals) : null;
    }
}

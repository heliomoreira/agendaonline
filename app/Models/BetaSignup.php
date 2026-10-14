<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BetaSignup extends Model
{
    public const TIPOS = [
        'cabeleireiro_barbearia' => 'Cabeleireiro ou barbearia',
        'estetica_bem_estar' => 'Estética e bem-estar',
        'clinica_consultorio' => 'Clínica ou consultório',
        'servicos_independente' => 'Serviços ou profissional independente',
        'outro' => 'Outro',
    ];

    public const PROFISSIONAIS = [
        '1' => 'Só eu',
        '2-5' => '2 a 5',
        '6+' => '6 ou mais',
    ];

    protected $fillable = [
        'nome',
        'email',
        'negocio',
        'tipo_negocio',
        'profissionais',
        'consentimento_em',
        'ip',
        'user_agent',
        'convidado_em',
    ];

    protected function casts(): array
    {
        return [
            'consentimento_em' => 'datetime',
            'convidado_em' => 'datetime',
        ];
    }

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo_negocio] ?? $this->tipo_negocio;
    }

    public function profissionaisLabel(): ?string
    {
        return $this->profissionais ? (self::PROFISSIONAIS[$this->profissionais] ?? $this->profissionais) : null;
    }
}

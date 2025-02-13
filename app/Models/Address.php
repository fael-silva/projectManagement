<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cep',
        'logradouro',
        'complemento',
        'bairro',
        'localidade',
        'uf',
        'estado',
        'regiao',
        'ibge',
        'ddd',
        'siafi',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}

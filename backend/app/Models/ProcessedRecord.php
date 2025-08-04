<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedRecord extends Model
{
    protected $table = 'processed_records';

    protected $fillable = [
        'data_registro',
        'metrica_a',
        'metrica_b',
        'indicador_x',
        'indicador_y',
    ];

    protected $casts = [
        'data_registro' => 'date',
        'metrica_a' => 'float',
        'metrica_b' => 'float',
        'indicador_x' => 'float',
        'indicador_y' => 'float',
    ];
}

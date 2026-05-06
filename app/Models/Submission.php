<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'nom_complet',
        'phone',
        'situation_familiale',
        'ci_employe',
        'photo_employe',
        'nom_pere',
        'ci_pere',
        'photo_pere',
        'nom_mere',
        'ci_mere',
        'photo_mere',
        'freres',
        'soeurs',
        'nom_conjoint',
        'ci_conjoint',
        'photo_conjoint',
        'descendants',
    ];

    protected $casts = [
        'freres'      => 'array',
        'soeurs'      => 'array',
        'descendants' => 'array',
    ];
}

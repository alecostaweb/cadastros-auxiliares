<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoGraduacao extends Model
{
    use HasFactory;

    protected $table = 'cursos_graduacao';

    protected $fillable = [
        'codcur',
        'nomcur',
        'codset',
        'nomset',
        'nomabvset',
    ];
}

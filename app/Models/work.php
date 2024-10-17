<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class work extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_application',
        'name',
        'price',
        'count_hours',
        'summ',
        'npp',
        'author',
        'count_hours_pzn'
    ];
}

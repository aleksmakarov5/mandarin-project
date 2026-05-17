<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class period_filter extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_user',
        'start_date',
        'end_date'
    ];
}

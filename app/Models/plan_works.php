<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class plan_works extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_application',
        'name'
    ];
}

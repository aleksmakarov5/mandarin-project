<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tru extends Model
{
    use HasFactory;
    protected $fillable = ['id_application', 'id_var'];
}

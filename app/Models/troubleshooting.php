<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class troubleshooting extends Model
{
    use HasFactory;
    protected $fillable = ['PartName', 'Work', 'ExpertApinion', 'Conclusion', 'id_application'];
}

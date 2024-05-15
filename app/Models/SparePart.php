<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{

    use HasFactory;
    protected $fillable = [
        'id_application', 'part_number', 'name', 'price_ic_full', 'price_ic_half',
        'price', 'count', 'iznos', 'npp', 'status_id'


    ];
}

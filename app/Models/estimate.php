<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class estimate extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_application',
        'price_sp_full',
        'price_sp_half',
        'price_repair',
        'price_paint',
        'price_zip',
        'price_work',
        'price_repair_full',
        'price_repair_full_md2',
        'price_repair_half',
        'dog_number',
        'zn_number',
        'hda_number',
        'emr10',
        'add_pay'

    ];
}

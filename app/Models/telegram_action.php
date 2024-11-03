<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class telegram_action extends Model
{
    use HasFactory;
    protected $fillable = [

        'id_chat',
        'id_application',
        'action',
        'number',
        'id_message'
    ];
}

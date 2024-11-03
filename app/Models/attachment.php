<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attachment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id_application',
        'id_status',
        'file_name',
        'message_id',
        'mime_type',
        'file_size',
        'location',
    ];
}

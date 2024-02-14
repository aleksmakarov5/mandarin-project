<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class application extends Model
{
    use HasFactory;
    protected $fillable = ['id_insuranceCompany', 'numberInsuranceCompanyApplication', 'CarModel', 'gnz'];
}

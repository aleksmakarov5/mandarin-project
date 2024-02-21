<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class application extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_insuranceCompany', 'numberInsuranceCompanyApplication', 'CarModel', 'gnz', 'vin',
        'ClientName', 'ClientPhone', 'id_MRK', 'id_spm', 'mileage', 'vehicle_passport', 'Color', 'ClientPassport', 'DTPdate'
    ];
}

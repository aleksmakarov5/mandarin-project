<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class application extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_insuranceCompany',
        'numberInsuranceCompanyApplication',
        'CarModel',
        'gnz',
        'vin',
        'ClientName',
        'ClientPhone',
        'id_MRK',
        'id_spm',
        'mileage',
        'vehicle_passport',
        'Color',
        'ClientPassport',
        'DTPdate',
        'id_status',
        'comment_true',
        'comment_false',
        'ClientPasport_number',
        'ClientPasport_date',
        'ClientPasport_struct',
        'start_date',
        'end_date',
        'wait_sp',
        'def',
        'dogovor',
        'mnf_year',
        'ub_number',
        'our_company',
        'sertificat',
        'ub_type',
        'id_city'

    ];
}

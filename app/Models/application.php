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
        'id_city',
        'pred',
        'pred_phone',
        'id_application',
        'ic_pay_date',
        'sto_pay_date',
        'zp_date',
        'view_date',
        'ic_answer_date',
        'sp_date',
    ];
    public function works()
    {
        return $this->hasMany('App\Models\work', 'id_application');
    }
    public function SpareParts()
    {
        return $this->hasMany('App\Models\SparePart', 'id_application');
    }
    public function status_applications()
    {
        return $this->hasMany('App\Models\status_application', 'id_application');
    }
    public function estimate()
    {
        return $this->hasOne('App\Models\estimate', 'id_application');
    }
    public function status_chats()
    {
        return $this->hasMany('App\Models\status_chat', 'id_application');
    }
    public function status_messages()
    {
        return $this->hasMany('App\Models\status_message', 'id_application');
    }
    public function finance_status()
    {
        return $this->hasOne('App\Models\FinanceStatus', 'id');
    }
}

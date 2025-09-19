<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceStatus extends Model
{
    use HasFactory;
    public function application()
    {
        return $this->hasMany('App\Models\application', 'finance_statuses_id');
    }
}
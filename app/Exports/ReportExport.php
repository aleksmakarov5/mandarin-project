<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;



class ReportExport implements FromView
{
    public $app_number;
    public function __construct($app_number)
    {
        $this->app_number = $app_number;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $spareparts = DB::table('spare_parts')
            ->where('id_application', '=', $this->app_number)
            ->leftJoin(
                'sparepart_statuses',
                'sparepart_statuses.id',
                '=',
                'spare_parts.status_id'
            )
            ->select('spare_parts.*', 'sparepart_statuses.StatusName')
            ->get();

        return view('exportReport', ['spareparts' => $spareparts]);
    }
}

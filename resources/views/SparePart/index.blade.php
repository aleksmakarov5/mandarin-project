@extends('header_menu')
@section('title')
    Запчасти
@endsection
@section('main')
    @php
        $Plan_works = DB::table('plan_works')->where('id_application', '=', $id)->get();
        $ub_type = DB::table('applications')->where('id', '=', $id)->first()->ub_type;

    @endphp
    {{-- {{ session('GroupId') }} --}}

    @if (Gate::allows('service'))
        <spare-part-service :app_id={{ $id }}
            :in_spareparts="{{ json_encode($spareparts) }}"></spare-part-service>
    @else
        <spare-part-km :ub_type={{ $ub_type }} :app_id={{ $id }} :rout=`{{ session('rout') }}`
            :in_summ_ic_full=`{{ $summ_ic_full }}` :in_summ_ic_half=`{{ $summ_ic_half }}` :in_summ_sto=`{{ $summ_sto }}`
            :in_delta_res_full=`{{ $delta_res_full }}` :in_delta_res_half=`{{ $delta_res_half }}`
            :in_statuses="{{ json_encode($statuses) }}" :in_spareparts="{{ json_encode($spareparts) }}"></spare-part-km>
        {{-- <spare-part-all :app_id={{ $id }} :rout=`{{ session('rout') }}`
                            :in_summ_ic_full=`{{ $summ_ic_full }}` :in_summ_sto=`{{ $summ_sto }}`
                            :in_delta_res_full=`{{ $delta_res_full }}` :in_statuses="{{ json_encode($statuses) }}"
                            :in_spareparts="{{ json_encode($spareparts) }}"></spare-part-all> --}}
    @endif



    @can('company')
        <div class="row">
            <div class="col  col-sm-12 col-md-12 col-lg-12">
                <work :app_id={{ $id }} :in_summ=`{{ $summ_ic }}` :in_summ_pzn=`{{ $summ_pzn }}`
                    :in_works="{{ json_encode($Works) }}"></work>
            </div>
            <div class="col  col-sm-12 col-md-12 col-lg-12">
                <work-paint :app_id={{ $id }} :in_summ=`{{ $summ_paint }}` :in_summ_pzn=`{{ $summ_pzn_paint }}`
                    :in_works="{{ json_encode($Works_paint) }}">
                </work-paint>
            </div>
        </div>
        <br>
        <br>
    @endcan
    <div class="row">
        <div class="col  col-sm-12 col-md-6 col-lg-6">
            <plan-work :app_id={{ $id }} :in_works="{{ json_encode($Plan_works) }}"></plan-work>
        </div>
        <div class="col  col-sm-12 col-md-6 col-lg-6">
            <fact-work :app_id={{ $id }} :in_summ_service=`{{ $summ_service }}`
                :in_works="{{ json_encode($Works_serv) }}"></fact-work>
        </div>
    </div>
    <br>
    <br>

    @can('company')
        <estimate :app_id={{ $id }} :in_sert=`{{ $sertificat }}` :in_estimate="{{ json_encode($estimate) }}">
        </estimate>
    @endcan
@endsection

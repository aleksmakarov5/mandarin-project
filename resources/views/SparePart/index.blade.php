@extends('header_menu')
@section('title')
    Запчасти
@endsection
@section('main')
    {{-- {{ session('GroupId') }} --}}
    @if (Gate::allows('km') || Gate::allows('km') || (Gate::allows('mrk') && Gate::allows('spm')))
        <spare-part-km :app_id={{ $id }} :rout=`{{ session('rout') }}` :in_summ_ic_full=`{{ $summ_ic_full }}`
            :in_summ_sto=`{{ $summ_sto }}` :in_delta_res_full=`{{ $delta_res_full }}`
            :in_statuses="{{ json_encode($statuses) }}" :in_spareparts="{{ json_encode($spareparts) }}"></spare-part-km>
    @else
        @if (Gate::allows('mrk'))
            <spare-part-mrk :app_id={{ $id }} :rout=`{{ session('rout') }}` :in_summ_ic_full=`{{ $summ_ic_full }}`
                :in_summ_sto=`{{ $summ_sto }}` :in_delta_res_full=`{{ $delta_res_full }}`
                :in_statuses="{{ json_encode($statuses) }}"
                :in_spareparts="{{ json_encode($spareparts) }}"></spare-part-mrk>
        @else
            @if (Gate::allows('spm'))
                <spare-part-zap :app_id={{ $id }} :rout=`{{ session('rout') }}`
                    :in_summ_ic_full=`{{ $summ_ic_full }}` :in_summ_sto=`{{ $summ_sto }}`
                    :in_delta_res_full=`{{ $delta_res_full }}` :in_statuses="{{ json_encode($statuses) }}"
                    :in_spareparts="{{ json_encode($spareparts) }}"></spare-part-zap>
            @else
                <spare-part-all :app_id={{ $id }} :rout=`{{ session('rout') }}`
                    :in_summ_ic_full=`{{ $summ_ic_full }}` :in_summ_sto=`{{ $summ_sto }}`
                    :in_delta_res_full=`{{ $delta_res_full }}` :in_statuses="{{ json_encode($statuses) }}"
                    :in_spareparts="{{ json_encode($spareparts) }}"></spare-part-all>
            @endif
        @endif
    @endif


    @can('company')
        <work :app_id={{ $id }} :in_summ=`{{ $summ_ic }}` :in_summ_pzn=`{{ $summ_pzn }}`
            :in_works="{{ json_encode($Works) }}"></work>
    @endcan
    <fact-work :app_id={{ $id }} :in_summ_service=`{{ $summ_service }}`
        :in_works="{{ json_encode($Works_serv) }}"></fact-work>


    @can('company')
        <estimate :app_id={{ $id }} :in_sert=`{{ $sertificat }}` :in_estimate="{{ json_encode($estimate) }}">
        </estimate>
    @endcan
@endsection

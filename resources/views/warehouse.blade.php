@extends('layout')
@section('title')
    Склад
@endsection
@section('main_content')
    <div id="app">
        @php
            // dd($warehouses);
        @endphp
        <warehouses :warehouses="{{ json_encode($warehouses) }}" :counterparties="{{ json_encode($counterparties) }}"
            :inventory_items="{{ json_encode($inventory_items) }}" :ware_houses="{{ json_encode($ware_houses) }}" />
    </div>
@endsection

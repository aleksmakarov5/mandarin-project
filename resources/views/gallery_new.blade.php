@extends('header_menu')
@section('title')
    {{ $id }} Галерея
@endsection

@section('main')
    <gallery :app_id="{{ $id }}" :name="{{ json_encode($name) }}" :images="{{ json_encode($images) }}"
        :location="{{ json_encode($location) }}" :dir="{{ json_encode(value: $dir) }}"
        :doc_type="{{ json_encode($doc_type) }}" :troubleshootings="{{ json_encode($troubleshootings) }}" />
@endsection

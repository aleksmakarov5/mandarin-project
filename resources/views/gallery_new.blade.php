@extends('header_menu')
@section('title')
    {{ $id }} Галерея
@endsection

@section('main')
    <gallery :app_id="{{ $id }}" :name="{{ json_encode($name) }}" :in_images="{{ json_encode($images) }}"
        :location="{{ json_encode($location) }}" :dir="{{ json_encode(value: $dir) }}"
        :doc_type="{{ json_encode($doc_type) }}" :troubleshootings="{{ json_encode($troubleshootings) }}"
        :doc_id="{{ json_encode($doc_id) }}" />
@endsection

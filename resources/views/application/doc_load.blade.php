@extends('header_menu')
@section('main')
    <div class="row">
        @foreach ($documents as $key => $doc)
            @if (pathinfo($doc->location, PATHINFO_EXTENSION) == 'pdf')
                <div class="card" style="width: 20rem;">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <form name="form{{ $key }}" action="data_load/" method="POST">
                                @csrf
                                <input type="hidden" name="location" value='{{ $doc->location }}'>
                                <img onclick="document.forms['form{{ $key }}'].submit()" style='cursor: pointer;'
                                    src="/storage/img/{{ pathinfo($doc->location, PATHINFO_EXTENSION) }}.png"
                                    class="card-img-top"
                                    alt="{{ mb_substr($doc->mime_type, mb_strpos($doc->mime_type, '/'), strlen($doc->mime_type) - mb_strpos($doc->mime_type, '/')) }}">
                            </form>
                        </div>

                        <div class="col-md-8">

                            <div class="card-body">

                                <h5 class="card-title">{{ $doc->file_name }}</h5>
                            </div>

                        </div>
                    </div>

                </div>
            @endif
        @endforeach
    </div>
@endsection

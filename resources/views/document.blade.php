@extends('header_menu')
@section('title')
    Документы
@endsection
@section('main')
    <form action="/documents/{{ $id }}/doc_add" method="post" enctype="multipart/form-data">
        @csrf <!-- с версии Laravel 5.6 -->
        <input type="file" name="userfile[]" multiple>
        <input type="submit" value="Загрузить документ" class="nav-link">
    </form>
    <br>

    <div class="row">
        @foreach ($document as $doc)
            {{-- <a href="/showdoc/{{$doc->location}}" class="nav-link">!{{$doc->file_name}}</a> --}}
            {{--    
 <a href="/storage/files/{{$doc->location}}" class="nav-link" target="_blank">{{$doc->file_name}}</a> --}}

            <div class="card" style="width: 20rem;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <a href="/storage/files/{{ $id }}/{{ $doc->location }}" target="_blank">


                            <img src="/storage/img/{{ mb_substr($doc->location, mb_strripos($doc->location, '.') + 1, mb_strlen($doc->location) - mb_strripos($doc->location, '.')) }}.png"
                                class="card-img-top"
                                alt="{{ mb_substr($doc->mime_type, mb_strpos($doc->mime_type, '/'), strlen($doc->mime_type) - mb_strpos($doc->mime_type, '/')) }}">
                        </a>
                    </div>

                    <div class="col-md-8">
                        <a href="/document/{{ $id }}/doc_edit/{{ $doc->id }}"><img
                                src="/storage/img/edit.png" width="35" alt="edit">
                        </a>
                        <a onclick="if (!confirm('Точно удалить документ <{{ $doc->file_name }}?>')) return false"
                            href="/document/{{ $id }}/doc_delete/{{ $doc->id }}"><img
                                src="/storage/img/delete.png" width="35" alt="delete">
                        </a>
                        <div class="card-body">
                            @if (isset($document_edit->id))
                                @if ($document_edit->id == $doc->id)
                                    <form action="/document/{{ $id }}/doc_update/{{ $doc->id }}"
                                        method="POST">
                                        @csrf
                                        <input type="text" name="file_name" value="{{ $doc_name }}">
                                        <input type="hidden" name="file_ext" value="{{ $doc_ext }}">
                                        <input type="submit" value="ок">
                                    </form>
                                @else
                                    <h5 class="card-title">{{ $doc->file_name }}</h5>
                                @endif
                            @else
                                <h5 class="card-title">{{ $doc->file_name }}</h5>
                            @endif


                        </div>

                    </div>
                </div>

            </div>
        @endforeach
    </div>
@endsection

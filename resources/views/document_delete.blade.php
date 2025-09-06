@extends('header_menu')
@section('title')
    Документы
@endsection

@section('main')
    @if (!isset($hide_menu))
        @php
            $all = true;
            foreach ($document as $doc) {
                if ($doc->id_status != 0) {
                    $all = false;
                }
            }
        @endphp

        <form action="/document_deliting/{{ $id }}" method="POST">
            @csrf

            <div class="row">

                @foreach ($document as $doc)
                    @if (pathinfo($doc->location, PATHINFO_EXTENSION) != 'gif')



                        <div class="card" style="width: 20rem;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <a @if (pathinfo($doc->location, PATHINFO_EXTENSION) == 'zip' ||
                                            pathinfo($doc->location, PATHINFO_EXTENSION) == 'rar' ||
                                            pathinfo($doc->location, PATHINFO_EXTENSION) == '7z') href='/gallery/{{ $id }}/{{ $doc->location }}/{{ $doc->file_name }}' target="_blank"
                    @else

                    @if (pathinfo($doc->location, PATHINFO_EXTENSION) == 'docx' ||
                            pathinfo($doc->location, PATHINFO_EXTENSION) == 'doc' ||
                            pathinfo($doc->location, PATHINFO_EXTENSION) == 'rtf')

                    @if (file_exists('storage/files/' . $id . '/' . pathinfo($doc->location, PATHINFO_FILENAME) . '.pdf'))
                    href="/storage/files/{{ $id }}/{{ pathinfo($doc->location, PATHINFO_FILENAME) . '.pdf' }}"
                    @else
                    @php
                    $pathToSave = 'storage/files/' . $id . '/' . $doc->location;
                    $dir = 'storage/files/' . $id;
                    exec('chmod 755 "' . $pathToSave . '"');
                    exec('libreoffice --headless --convert-to pdf "' . $pathToSave . '" --outdir ' . $dir);
                    @endphp
                    href="/storage/files/{{ $id }}/{{ pathinfo($doc->location, PATHINFO_FILENAME) . '.pdf' }}"
                    {{-- href="/storage/files/{{ $id }}/{{ $doc->location }}" --}} @endif
                                    @else href="/storage/files/{{ $id }}/{{ $doc->location }}" @endif
                                        target="_blank"
                    @endif>
                    <img src="/storage/img/{{ mb_substr($doc->location, mb_strripos($doc->location, '.') + 1, mb_strlen($doc->location) - mb_strripos($doc->location, '.')) }}.png"
                        class="card-img-top"
                        alt="{{ mb_substr($doc->mime_type, mb_strpos($doc->mime_type, '/'), strlen($doc->mime_type) - mb_strpos($doc->mime_type, '/')) }}">

                    </a>
            </div>

            <div class="col-md-8">
                <input type="checkbox" style="zoom:4;" name="doc[{{ $doc->id }}]" id="">

                <div class="card-body">
                    <h5 class="card-title">{{ $doc->file_name == 'image001.png' ? 'Номер телефона' : $doc->file_name }}
                    </h5>
                    <p class="file-size">
                        {{ round($doc->file_size / 1024 / 1024, 2) }} мб
                    </p>


                </div>

            </div>
            </div>

            </div>
    @endif
    @endforeach

    </div>
    <input type="submit"class="nav-link" value="Удалить">
    </form>
    <br>
    @endif
@endsection

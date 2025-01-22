@extends('layout')
@section('title')
{{ $id }} Галерея
@endsection

@section('main_content')
    <div class="container gallery-container">

        <h1>{{ $name }}</h1>
        <a href="/document/{{ $id }}" class="nav-link">Назад</a>

        <div class="tz-gallery">

            <div class="row">
                @foreach ($images as $key => $image)
                    @if ($image != '.' && $image != '..')
                        <div class="col-sm-6 col-md-4">
                            <a class="lightbox" href="/{{ $dir }}/{{ $image }}">
                                <img src="/{{ $dir }}/{{ $image }}" alt="{{ $image }}">
                            </a>
                            <form action="/photo_destroy" method="POST" name="form{{ $key }}">
                                @csrf
                                <input type="hidden" name="dir" value={{ $dir }}>
                                <input type="hidden" name="image" value="{{ $image }}">
                                <input type="hidden" name="id" value="{{ $id }}">
                                <input type="hidden" name="location" value="{{ $location }}">

                            </form>
                            <p onclick="if (confirm('Точно удалить ?')) form{{ $key }}.submit()"
                                href="/photo_destroy/{{ $id }}/{{ $image }}/{{ $dir }}/{{ $location }}"
                                class="doc-delete nav-link"><img src="/storage/img/delete.png" width="30"
                                    alt="delete"></p>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.8.1/baguetteBox.min.js"></script>
    <script>
        baguetteBox.run('.tz-gallery');
    </script>
@endsection

@extends('layout')
@section('title')
    Банковская выписка
@endsection
@section('main_content')
    <div id="app">
        @php
            // dd($attachments);
        @endphp
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form action="/vyp_upload" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            @csrf
                            <label for="file">Выберите файл выписки для загрузки</label>
                            <input type="file" name="file" class="form-control-file" id="file" accept="text/txt">
                            <button type="submit" class="btn btn-primary">Загрузить</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

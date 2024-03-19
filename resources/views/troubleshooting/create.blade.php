@extends('layout')
@section('main_content')
    <a href="/troubleshooting/{{ $id }}" class="nav-link">Отмена</a>
    <form action="/troubleshootings/store" method="POST">
        @csrf

        <input name = "id_application" type="hidden" value="{{ $id }}">

        <div class="form-float">

            <input type="text" name="PartName" id="PartName" class="form-cont">
            <label for="PartName">Наименование детали автомобиля</label>
        </div>

        <div class="form-float">
            <input type="text" name="Work" id="Work" class="form-cont">
            <label for="Work">Требуемое ремонтное воздействие</label>
        </div>
        <div class="form-float">
            <input type="text" name="ExpertApinion" id="ExpertApinion" class="form-cont">
            <label for="ExpertApinion">Мнение эксперта</label>
        </div>
        <div class="form-float">
            <input type="text" name="Сonclusion" id="Сonclusion" class="form-cont">
            <label for="Сonclusion">Заключение</label>
        </div>

        <input type="submit" value="Создать" class="nav-link">

    </form>
@endsection

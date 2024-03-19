@extends('header_menu')
@section('title')
    Работы
@endsection
@section('main')
    <a href="/troubleshooting/{{ $id }}/create" class="nav-link">Добавить</a>



    <table class="table table-striped-columns table-hover table-group-divider table-responsive">
        <thead>

            <tr>
                <th scope="col">#</th>
                <th scope="col">Наименование детали автомобиля</th>

                <th scope="col">Требуемое ремонтное воздействие</th>
                <th scope="col">Мнение эксперта</th>
                <th scope="col">Заключение</th>
                <th scope="col"></th>


        </thead>
        <tbody>



            @foreach ($troubleshootings as $troubleshooting)
                <tr>

                    <th scope="col">

                        {{ $troubleshooting->id }}

                    </th>
                    <th scope="col"
                        onclick="window.location.href='/troubleshooting/{{ $troubleshooting->id }}/edit'; return false">
                        {{ $troubleshooting->PartName }}</th>
                    <th scope="col"
                        onclick="window.location.href='/troubleshooting/{{ $troubleshooting->id }}/edit'; return false">
                        {{ $troubleshooting->Work }}</th>
                    <th scope="col"
                        onclick="window.location.href='/troubleshooting/{{ $troubleshooting->id }}/edit'; return false">
                        {{ $troubleshooting->ExpertApinion }}</th>
                    <th scope="col"
                        onclick="window.location.href='/troubleshooting/{{ $troubleshooting->id }}/edit'; return false">
                        {{ $troubleshooting->Сonclusion }}</th>
                    <th scope="col"> <a
                            onclick="if (!confirm('Точно удалить позицию <{{ $troubleshooting->PartName }}?>')) return false"
                            href="{{ route('troubleshooting.destroy', $troubleshooting->id) }}" class="nav-link"><img
                                src="/storage/img/delete.png" width="30" alt="delete"></a>
                    </th>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

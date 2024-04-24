@extends('admin.header_menu')
@section('title')
    Пользователи
@endsection
@section('main')
    <a href="/services/create" class="nav-link">Добавить сервис</a>

    <table class="table table-striped-columns table-hover table-group-divider table-responsive">
        <thead>

            <tr>
                <th scope="col">#</th>
                <th scope="col">Название</th>
                <th scope="col">Адрес</th>
                <th scope="col">Пользователь</th>

            </tr>
        </thead>
        <tbody>



            @foreach ($services as $service)
                <tr>

                    <th scope="col">

                        {{ $service->id }}

                    </th>
                    <th scope="col" onclick="window.location.href='/services/{{ $service->id }}'; return false">
                        {{ $service->service_name }} </th>
                    <th scope="col" onclick="window.location.href='/services/{{ $service->id }}'; return false">
                        {{ $service->service_description }} </th>
                    <th scope="col" onclick="window.location.href='/services/{{ $service->id }}'; return false">
                        {{ $service->name }} </th>
                    <th scope="col">
                        <a onclick="if (!confirm('Точно удалить сервис <{{ $service->service_name }}?>')) return false"
                            href="{{ route('service.destroy', $service->id) }}" class="nav-link">Удалить</a>

                    </th>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

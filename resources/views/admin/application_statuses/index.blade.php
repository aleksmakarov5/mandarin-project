@extends('admin.header_menu')
@section('title')
    Статусы заявок
@endsection
@section('main')
    <a href="/application_statuses/create" class="nav-link">Добавить статус</a>

    <table class="table table-striped-columns table-hover table-group-divider table-responsive">
        <thead>

            <tr>
                <th scope="col">#</th>
                <th scope="col">Статус</th>
                <th scope="col"></th>

            </tr>
        </thead>
        <tbody>



            @foreach ($application_statuses->all() as $application_status)
                <tr>

                    <th scope="col">

                        {{ $application_status->id }}

                    </th>
                    <th scope="col"
                        onclick="window.location.href='/application_status/{{ $application_status->id }}'; return false">
                        {{ $application_status->StatusName }} </th>
                    <th scope="col">
                        <a onclick="if (!confirm('Точно удалить статус <{{ $application_status->StatusName }}?>')) return false"
                            href="{{ route('application_status.destroy', $application_status->id) }}"
                            class="nav-link">Удалить</a>

                    </th>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

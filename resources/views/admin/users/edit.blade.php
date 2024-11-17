@extends('admin.header_menu')
@section('main_content')
    <div class="row">
        <div class="col-4">
            <a href="/users" class="nav-link">Отмена</a>
            <form method="POST" action="/user/{{ $user->id }}/update">

                <h1 class="h3 mb-3 fw-normal">Изменение данных пользователя</h1>
                @csrf
                <div class="form-group">
                    <label for="floatingInput">ФИО</label>
                    <input type="text" name='name' id="name" class="form-control" id="floatingInput"
                        placeholder="ФИО" value="{{ $user->name }}">

                    @error('name')
                        <p class="text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="floatingInput">Электронный адрес</label>
                    <input type="email" name='email' id="email" class="form-control" id="floatingInput"
                        placeholder="name@example.com" value="{{ $user->email }}">

                    @error('email')
                        <p class="text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="floatingInput">Номер телефона</label>
                    <input type="text" name='phone' id="phone" class="form-control" id="floatingInput"
                        value="{{ $user->phone }}">

                    @error('phone')
                        <p class="text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="floatingInput">Роль</label>
                    <select class="form-control" id="floatingInput" name="groupid" value="{{ $user->groupid }}">
                        <option value="0">-</option>
                        @foreach ($groups as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->GroupName }}</option>
                        @endforeach

                    </select>

                </div>
                <div class="form-group">
                    <label for="floatingInput">Компания</label>
                    <select class="form-control" id="floatingInput" name="id_company">
                        <option value="0">-</option>
                        @foreach ($companies as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->our_name }}</option>
                        @endforeach

                    </select>

                </div>
                <div class="form-group">
                    <label for="floatingInput">Города</label>
                    <select class="form-control" id="floatingInput" name="id_city">
                        <option value="0">-</option>
                        @foreach ($cities as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}</option>
                        @endforeach

                    </select>

                </div>
                <div class="form-group">
                    <label for="floatingPassword">Пароль</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password">

                    @error('password')
                        <p class="text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="floatingPassword">Подтверждение пароля</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                        placeholder="Password">


                </div>
                <button class="nav-link" type="submit">Изменить</button>
            </form>
        </div>
        <div class="col-8">
            <div>
                <table class="table table-border table-striped">
                    <tr>
                        <thead>
                            <th colspan="2">
                                Роли/Компании
                            </th>
                    </tr>
                    </thead>
                    <tbody>
                        @if ($user->roles)
                            @foreach ($user->roles as $role)
                                <tr>
                                    <td>
                                        {{ $role->GroupName }}
                                    </td>
                                    <td>
                                        <a href="/user_role/delete/{{ $role->id }}">
                                            <img src="/storage/img/delete.png" width=30 alt="">
                                        </a>
                                    </td>
                                </tr>
                                @if ($role->company)
                                    @foreach ($role->company as $company)
                                        <tr>
                                            <td>------
                                                {{ $company->our_name }}
                                            </td>
                                            <td>
                                                <a href="/user_company/delete/{{ $company->id }}">
                                                    <img src="/storage/img/delete.png" width=30 alt="">
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <form method="POST" action="/role_company/{{ $role->id }}/create">
                                        <td>

                                            @csrf
                                            <div class="form-group">

                                                <select class="form-control" id="floatingInput" name="id_company">
                                                    <option value="0">-</option>
                                                    @foreach ($companies as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->our_name }}</option>
                                                    @endforeach

                                                </select>

                                            </div>
                                        </td>
                                        <td>
                                            <button type="submit" class="nav-link">
                                                Добавить
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div>
                <table class="table table-border table-striped">
                    <tr>
                        <thead>
                            <th colspan="2">
                                Города
                            </th>
                    </tr>
                    </thead>
                    <tbody>
                        @if ($user->city)
                            @foreach ($user->city as $city)
                                <tr>
                                    <td>
                                        {{ $city->city }}
                                    </td>
                                    <td>
                                        <a href="/user_city/delete/{{ $city->id }}">
                                            <img src="/storage/img/delete.png" width=30 alt="">
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

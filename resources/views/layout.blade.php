<!DOCTYPE html>
<html lang="ru" @if (isset(Auth::user()->theme)) data-bs-theme="{{ Auth::user()->theme }}" @endif>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>


    <meta charset="utf-8">



    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/carousel/">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Droid+Sans:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.8.1/baguetteBox.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <!-- Favicons -->
    <link rel="apple-touch-icon" href="/storage/img/favicons/image_36.png" sizes="180x180">
    <link rel="icon" href="/storage/img/favicons/image_36.png" sizes="32x32" type="image/png">
    <link rel="icon" href="/storage/img/favicons/image_36.png" sizes="16x16" type="image/png">
    <link rel="mask-icon" href="/storage/img/favicons/safari-pinned-tab.svg" color="#712cf9">

    <link rel="icon" href="favicon.ico">
    <meta name="theme-color" content="#712cf9">
    <!-- Custom styles for this template -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @vite(['resources/js/app.js'])
</head>

<body>
    @auth('web')
        @php
            $applications = DB::table('applications')
                ->where('id_status', '<>', '27')
                ->leftjoin('status_applications', function ($join) {
                    $join
                        ->on('status_applications.id_application', '=', 'applications.id')
                        ->where('status_applications.id_user', '=', Auth::user()->id)
                        ->where('status_applications.is_readed', '=', '0');
                })
                ->leftjoin('status_chats', function ($join) {
                    $join
                        ->on('status_chats.id_application', '=', 'applications.id')
                        ->where('status_chats.id_user', '=', Auth::user()->id)
                        ->where('status_chats.is_readed', '=', '0');
                })
                ->leftjoin('status_messages', function ($join) {
                    $join
                        ->on('status_messages.id_application', '=', 'applications.id')
                        ->where('status_messages.id_user', '=', Auth::user()->id)
                        ->where('status_messages.is_readed', '=', '0');
                })
                ->select(
                    'applications.id_status as status',
                    'status_applications.id as app',
                    'status_chats.id as chat',
                    'status_messages.id as message',
                )
                ->get();
            // dd($applications);
            $count_applications = ['application' => 0, 'chat' => 0, 'message' => 0];
            $count_directions = ['application' => 0, 'chat' => 0, 'message' => 0];
            $count_failures = ['application' => 0, 'chat' => 0, 'message' => 0];

            $count_archive = ['application' => 0, 'chat' => 0, 'message' => 0];
            $count_priem = ['application' => 0, 'chat' => 0, 'message' => 0];
            foreach ($applications as $application) {
                if ($application->status < 12 && $application->status != 6 && $application->status != 7) {
                    if ($application->app) {
                        $count_applications['application']++;
                    }
                    if ($application->chat) {
                        $count_applications['chat']++;
                    }
                    if ($application->message) {
                        $count_applications['message']++;
                    }
                }
                if ($application->status > 11 && $application->status < 25) {
                    if ($application->app) {
                        $count_directions['application']++;
                    }
                    if ($application->chat) {
                        $count_directions['chat']++;
                    }
                    if ($application->message) {
                        $count_directions['message']++;
                    }
                }
                if ($application->status > 5 && $application->status < 8) {
                    if ($application->app) {
                        $count_failures['application']++;
                    }
                    if ($application->chat) {
                        $count_failures['chat']++;
                    }
                    if ($application->message) {
                        $count_failures['message']++;
                    }
                }
                if ($application->status == 25) {
                    if ($application->app) {
                        $count_archive['application']++;
                    }
                    if ($application->chat) {
                        $count_archive['chat']++;
                    }
                    if ($application->message) {
                        $count_archive['message']++;
                    }
                }
                if ($application->status == 28) {
                    if ($application->app) {
                        $count_priem['application']++;
                    }
                    if ($application->chat) {
                        $count_priem['chat']++;
                    }
                    if ($application->message) {
                        $count_priem['message']++;
                    }
                }
            }
            $notifications = DB::table('notifies')
                ->where('notifies.id_user', '=', Auth::user()->id)
                ->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                ->select('notifies.*', 'text_notifies.text')
                ->get();
            // dd($count_archive, $count_failures);
        @endphp
    @endauth
    <div class="container">
        @if (!isset($hide_menu))


            @auth('web')
                <div class='user'>

                    <a href="/personal" class="nav-link {{ session('rout') == 'personal' ? 'tn-active' : '' }}">
                        @if (isset(Auth::user()->avatar))
                            <img src="{{ Auth::user()->avatar }}" class="avatar">
                        @endif
                        {{ Auth::user()->name }}
                    </a>

                </div>
                <div id="app1" class="mytoast">
                    <post-notify :user="{{ Auth::user()->id }}" :notification_in="{{ json_encode($notifications) }}" />
                </div>
                <div class="d1">
                    <form action="/find" method="get">
                        @csrf
                        <input type="text" placeholder="Поиск" name="find">
                        <button type="submit"></button>
                    </form>
                </div>
            @endauth

            <div id="nb">
                <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
                    @auth('web')
                        <br>
                    @endauth
                    <a class="tn-atom t-bgimg loaded" href="/"
                        data-original="https://static.tildacdn.com/tild3035-3764-4461-a632-393433376239/c4628e96-a0e1-4301-b.png"
                        style="background-image: url(&quot;https://optim.tildacdn.com/tild3035-3764-4461-a632-393433376239/-/cover/97x93/center/center/-/format/webp/c4628e96-a0e1-4301-b.png&quot;);"></a>
                    <a href="/"
                        class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
                        <img class="bi me-2" width="100" height="100"
                            src="https://static.tildacdn.com/tild3035-3764-4461-a632-393433376239/c4628e96-a0e1-4301-b.png">

                    </a>

                    <ul class="nav nav-pills">

                        <li class="nav-item"><a href="\" class="nav-link
                                {{ session('rout') == 'home' ? 'tn-active' : '' }}">Главная</a></li>


                        @auth('web')
                            <li class="nav-item"><a href="\applications"
                                    class="nav-link {{ session('rout') == 'applications' ? 'tn-active' : '' }}">Запросы</a>
                                <notify-buttons :user="{{ Auth::user()->id }}" :pi=`applications`
                                    :count_in="{{ json_encode($count_applications) }}"></notify-buttons>

                            </li>
                            <li class="nav-item"><a href="\directions"
                                    class="nav-link {{ session('rout') == 'directions' ? 'tn-active' : '' }}">Направления</a>
                                <notify-buttons :user="{{ Auth::user()->id }}" :pi=`directions`
                                    :count_in="{{ json_encode($count_directions) }}"></notify-buttons>

                            </li>
                            @can('company')
                                <li class="nav-item"><a href="\priem"
                                        class="nav-link {{ session('rout') == 'priem' ? 'tn-active' : '' }}">Приемка</a>
                                    <notify-buttons :user="{{ Auth::user()->id }}" :pi=`priem`
                                        :count_in="{{ json_encode($count_priem) }}"></notify-buttons>
                                </li>
                                {{-- <li class="nav-item"><a href="\failures"
                                        class="nav-link {{ session('rout') == 'failures' ? 'tn-active' : '' }}">Отказы</a>
                                    <notify-buttons :user="{{ Auth::user()->id }}" :pi=`failures`
                                        :count_in="{{ json_encode($count_failures) }}"></notify-buttons>
                                </li> --}}

                                <li class="nav-item"><a href="\archive"
                                        class="nav-link {{ session('rout') == 'archive' ? 'tn-active' : '' }}">Архив</a>
                                    <notify-buttons :user="{{ Auth::user()->id }}" :pi=`archive`
                                        :count_in="{{ json_encode($count_archive) }}"></notify-buttons>
                                </li>
                                <li class="nav-item"><a href="\temp"
                                        class="nav-link {{ session('rout') == 'temp' ? 'tn-active' : '' }}">Корзина</a>

                                </li>
                                <li class="nav-item"><a href="\message"
                                        class="nav-link {{ session('rout') == 'message' ? 'tn-active' : '' }}">Почта</a></li>
                            @endcan




                            &nbsp&nbsp&nbsp&nbsp
                            <li class="nav-item"><a href="\logout"
                                    class="nav-link {{ request()->routeIs('login') ? 'tn-active' : '' }}">Выйти</a></li>
                        @endauth
                        @guest('web')
                            &nbsp&nbsp&nbsp&nbsp
                            <li class="nav-item"><a href="\login"
                                    class="nav-link {{ request()->routeIs('login') ? 'tn-active' : '' }}">Войти</a></li>
                        @endguest
                        @can('administrator')
                            <li class="nav-item"><a href="\admin"
                                    class="nav-link {{ session('rout') == 'admin' ? 'tn-active' : '' }}">Админ панель</a>
                            </li>
                            @if (session('current'))
                                <li class="nav-item"><a href="\application\{{ session('current') }}"
                                        class="nav-link ">Вернуться к
                                        заявке</a>
                                </li>
                            @endif
                        @endcan
                    </ul>

                </header>
            </div>

        @endif

        @yield('main_content')
    </div>
    <div class="container">
        <footer class="py-3 my-4 text-white">

            <p class="text-center ">© 2024 Мандарин авто, Inc</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.8.1/baguetteBox.min.js"></script>
    <script>
        baguetteBox.run('.tz-gallery');
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>
    <script src="/docs/5.3/assets/js/color-modes.js"></script>
 <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.118.2">
    <title>Carousel Template · Bootstrap v5.3</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/carousel/">

    

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">

<link href="/docs/5.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Favicons -->
<link rel="apple-touch-icon" href="/docs/5.3/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
<link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
<link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
<link rel="manifest" href="/docs/5.3/assets/img/favicons/manifest.json">
<link rel="mask-icon" href="/docs/5.3/assets/img/favicons/safari-pinned-tab.svg" color="#712cf9">
<link rel="icon" href="/docs/5.3/assets/img/favicons/favicon.ico">
<meta name="theme-color" content="#712cf9">
    <!-- Custom styles for this template -->
    <link href="carousel.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body class="bg-dark text-white ">
<div class="container">
    @auth("web")
    <p align=right> {{ Auth::user()->name }} </p>
    
@endauth
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
       @auth('web')
          
          <br>
      @endauth
      <a class="tn-atom t-bgimg loaded" href="/" data-original="https://static.tildacdn.com/tild3035-3764-4461-a632-393433376239/c4628e96-a0e1-4301-b.png" style="background-image: url(&quot;https://optim.tildacdn.com/tild3035-3764-4461-a632-393433376239/-/cover/97x93/center/center/-/format/webp/c4628e96-a0e1-4301-b.png&quot;);"></a>
      <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none" >
        <img class="bi me-2" width="100" height="100" src="https://static.tildacdn.com/tild3035-3764-4461-a632-393433376239/c4628e96-a0e1-4301-b.png">
     
      </a>
     
      <ul class="nav nav-pills"> 
       
        <li class="nav-item"><a href="\" class="nav-link {{ request()->routeIs('home') ? 'tn-active' : '' }}" >Главная</a></li>
        <li class="nav-item"><a href="\review" class="nav-link {{ request()->routeIs('review') ? 'tn-active' : '' }}">Отзывы</a></li>

        
        @auth("web")
        <li class="nav-item"><a href="\applications" class="nav-link {{ request()->routeIs('applications') ? 'tn-active' : '' }}">Заявки</a></li>
        <li class="nav-item"><a href="\mail" class="nav-link {{ request()->routeIs('mail') ? 'tn-active' : '' }}">Почта</a></li>
        &nbsp&nbsp&nbsp&nbsp
               <li class="nav-item"><a href="\logout" class="nav-link {{ request()->routeIs('login') ? 'tn-active' : '' }}" >Выйти</a></li>
               
        @endauth
        @guest("web") 
&nbsp&nbsp&nbsp&nbsp
         <li class="nav-item"><a href="\login" class="nav-link {{ request()->routeIs('login') ? 'tn-active' : '' }}" >Войти</a></li>
         @endguest
         @can('administrator')
             <li class="nav-item"><a href="\admin" class="nav-link {{ request()->routeIs('admin') ? 'tn-active' : '' }}" >Админ панель</a></li>
         @endcan
       </ul>
    </header>
  
  @yield('main_content')
  </div>
  <div class="container">
    <footer class="py-3 my-4 text-white">
     
      <p class="text-center ">© 2023 Мандарин авто, Inc</p>
    </footer>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

 </body>
</html>

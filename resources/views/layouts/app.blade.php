<?php
use \App\Http\Controllers\GlobalController;
?>
    <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{--    <meta http-equiv="Refresh" content="30">--}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" CONTENT="Первая облачная мультиязычная универсальная расчетная учетная платформа">
    <meta name="description" CONTENT="Первая облачная мультиязычная универсальная расчетная учетная платформа">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    {{--    <link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
    @include('layouts.style_header')
    <style>
        .navbar {
            background-image: url('{{Storage::url('gray-abstract.jpg')}}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            height: 100%;
        }
    </style>
</head>
{{--https://www.manhunter.ru/webmaster/905_kak_na_javascript_uznat_realniy_razmer_izobrazheniya.html--}}
{{--get_dimensions(el) используется в view\img.php--}}
<script type="text/javascript">
    // Функция для получения реального размера изображения
    // На входе: el - элемент DOM, для которого надо получить размеры
    // На выходе: объект {real_width, real_height, client_width, client_height}
    // или false, если произошла ошибка
    function get_dimensions(el) {
        // Браузер с поддержкой naturalWidth/naturalHeight
        if (el.naturalWidth != undefined) {
            return {
                'real_width': el.naturalWidth,
                'real_height': el.naturalHeight,
                'client_width': el.width,
                'client_height': el.height
            };
        }
        // Устаревший браузер
        else if (el.tagName.toLowerCase() == 'img') {
            var img = new Image();
            img.src = el.src;
            var real_w = img.width;
            var real_h = img.height;
            return {
                'real_width': real_w,
                'real_height': real_h,
                'client_width': el.width,
                'client_height': el.height
            };
        }
        // Что-то непонятное
        else {
            return false;
        }
    }
</script>
{{--<body>--}}
<body background="{{Storage::url('gray-abstract.jpg')}}"
style="
background-size: cover;
background-position: center center;
background-repeat: no-repeat;
height: 100%;
"
>
RSB
{{--    <div id="app">--}}
{{--        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">--}}
{{--            <div class="container">--}}
{{--                <a class="navbar-brand" href="{{ url('/') }}">--}}
{{--                    {{ config('app.name', 'Laravel') }}--}}
{{--                </a>--}}
{{--                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">--}}
{{--                    <span class="navbar-toggler-icon"></span>--}}
{{--                </button>--}}

{{--                <div class="collapse navbar-collapse" id="navbarSupportedContent">--}}
{{--                    <!-- Left Side Of Navbar -->--}}
{{--                    <ul class="navbar-nav me-auto">--}}

{{--                    </ul>--}}

{{--                    <!-- Right Side Of Navbar -->--}}
{{--                    <ul class="navbar-nav ms-auto">--}}
{{--                        <!-- Authentication Links -->--}}
{{--                        @guest--}}
{{--                            @if (Route::has('login'))--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>--}}
{{--                                </li>--}}
{{--                            @endif--}}

{{--                            @if (Route::has('register'))--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>--}}
{{--                                </li>--}}
{{--                            @endif--}}
{{--                        @else--}}
{{--                            <li class="nav-item dropdown">--}}
{{--                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>--}}
{{--                                    {{ Auth::user()->name }}--}}
{{--                                </a>--}}

{{--                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">--}}
{{--                                    <a class="dropdown-item" href="{{ route('logout') }}"--}}
{{--                                       onclick="event.preventDefault();--}}
{{--                                                     document.getElementById('logout-form').submit();">--}}
{{--                                        {{ __('Logout') }}--}}
{{--                                    </a>--}}

{{--                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">--}}
{{--                                        @csrf--}}
{{--                                    </form>--}}
{{--                                </div>--}}
{{--                            </li>--}}
{{--                        @endguest--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </nav>--}}

{{--        <main class="py-4">--}}
{{--            @yield('content')--}}
{{--        </main>--}}
{{--    </div>--}}
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            {{--            @foreach (session('glo_menu_lang') as $value)--}}
            @foreach (config('app.locales') as $value)
                <a class="navbar-brand" href="{{ url('/setlocale/' . $value) }}">
                    <span
                        {{--                                                @if(session('locale') == $value)--}}
                        @if(App::getLocale() == $value)
                        {{--                        @if(App::currentLocale() == $value)--}}
                        style="text-decoration: underline"
                        @endif
                    >{{mb_strtoupper($value)}}</span>
                </a>
            @endforeach
            {{--            @foreach (config('app.displays') as $value)--}}
            {{--                <a class="navbar-brand" href="{{route('global.set_display',--}}
            {{--                            ['display'=>$value])}}">--}}
            {{--                    <span--}}
            {{--                        @if(GlobalController::get_display() == $value)--}}
            {{--                        style="text-decoration: underline"--}}
            {{--                        @endif--}}
            {{--                    >{{mb_strtoupper($value)}}</span>--}}
            {{--                </a>--}}
            {{--            @endforeach--}}
            <a class="navbar-brand" href="{{ url('/') }}" title="{{config('app.name')}}">
                <img src="{{Storage::url('logotype.png')}}" width="30" height="30"
                     class="circle d-inline-block align-top"
                     alt="" loading="lazy">
                {{config('app.name')}}
            </a>
            <?php
            // Подсчет количества посетителей на сайте онлайн
            $visitors_count = \App\Http\Controllers\VisitorController::visitors_count();
            ?>
            <a class="navbar-brand" href="#"
               title="{{trans('main.online_now') . ": " . $visitors_count . ' ' . mb_strtolower(trans('main.visitors_info'))}}">
                ({{$visitors_count}})
            </a>
            {{--                Этот <button> не удалять, нужен для связки с <div class="collapse navbar-collapse" id="navbarSupportedContent">--}}
            <button type="button" class="navbar-toggler" data-toggle="collapse"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            {{--            @auth--}}
            {{--            @guest--}}
            <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('project.all_index')}}"
                           title="{{trans('main.all_projects') . ' - ' . trans('main.info_all_projects')}}">
                            {{trans('main.all_projects')}}
                        </a>
                    </li>
                    {{--                        <li class="nav-item">--}}
                    {{--                            --}}{{--                            <a class="nav-link" style="color: green"--}}
                    {{--                            --}}{{--                            <a class="nav-link text-primary font-weight-bold"--}}
                    {{--                            --}}{{--                                <a class="nav-link text-primary"--}}
                    {{--                            --}}{{--                                   href="{{route('base.template_index', $glo_project_template_id)}}}">{{trans('main.bases')}}</a>--}}
                    {{--                            <a class="nav-link" href="\home"--}}
                    {{--                               title="{{trans('main.info_project_role_selection')}}">--}}
                    {{--                                {{trans('main.project_role_selection')}}--}}
                    {{--                            </a>--}}
                    {{--                        </li>--}}
                    {{--                        <li class="nav-item">--}}
                    {{--                            <a class="nav-link" href="{{route('project.index_user', Auth::user())}}"--}}
                    {{--                            title="{{trans('main.info_projects')}}">--}}
                    {{--                                {{trans('main.projects')}}--}}
                    {{--                            </a>--}}
                    {{--                        </li>--}}
                    {{--                        <li class="nav-item">--}}
                    {{--                            <a class="nav-link" href="{{route('access.index_user', Auth::user())}}"--}}
                    {{--                            title="{{trans('main.info_accesses')}}">--}}
                    {{--                                {{trans('main.accesses')}}--}}
                    {{--                            </a>--}}
                    {{--                        </li>--}}

                    {{--                        <li class="nav-item">--}}
                    {{--                            <a class="nav-link"--}}
                    {{--                               href="{{route('access.index_user', Auth::user())}}">{{trans('main.accesses')}}</a>--}}
                    {{--                        </li>--}}
                    {{--                        <li class="nav-item">--}}
                    {{--                            <a class="nav-link" href="#">{{trans('main.all_projects')}}</a>--}}
                    {{--                        </li>--}}
                    {{--                @endguest--}}
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('project.subs_index')}}"
                               title="{{trans('main.subscribe') . ' - ' . trans('main.info_subscribe')}}">
                                {{trans('main.subscribe')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('project.my_index')}}"
                               title="{{trans('main.my_projects') . ' - ' . trans('main.info_my_projects')}}">
                                {{trans('main.my_projects')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('project.mysubs_index')}}"
                               title="{{trans('main.my_subscriptions') . ' - ' . trans('main.info_my_subscriptions')}}">
                                {{trans('main.my_subscriptions')}}
                            </a>
                        </li>
                        {{--                @if(Auth::user()->isAdmin())--}}
                        {{--                    <!-- Right Side Of Navbar -->--}}
                        {{--                        <ul class="navbar-nav ml-auto">--}}
                        {{--                            <li class="nav-item">--}}
                        {{--                                <a class="nav-link" href="{{route('template.index')}}">{{trans('main.templates')}}</a>--}}
                        {{--                            </li>--}}
                        {{--                            <li class="nav-item">--}}
                        {{--                                <a class="nav-link" href="{{route('user.index')}}">{{trans('main.users')}}</a>--}}
                        {{--                            </li>--}}
                        {{--                        </ul>--}}
                        {{--                @endif--}}
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{route('template.main_index')}}"
                           title="{{trans('main.templates') . ' - ' . trans('main.info_templates')}}">
                            {{trans('main.templates')}}
                        </a>
                    </li>
                    <?php
                    // Ссылка на проект Инструкции Abakusonline
                    $instr_link = env('INSTRUCTIONS_LINK');
                    $instr_new_link = env('INSTRUCTIONS_NEW_LINK');
                    ?>
                    @if($instr_link !='')
                        <li class="nav-item"><a class="nav-link"
                                                href="{{$instr_link}}"
                                                title="{{trans('main.instructions')}}">
                                {{trans('main.instructions')}}
                            </a>
                        </li>
                    @endif
                    @if($instr_new_link !='')
                        <li class="nav-item"><a class="nav-link"
                                                href="{{$instr_new_link}}"
                                                title="{{trans('main.instructions_new_version')}}">
                                {{trans('main.instructions_new_version')}}
                            </a>
                        </li>
                    @endif
                </ul>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            {{--                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>--}}
                            <a class="nav-link" href="{{ route('login') }}"
                               title="{{trans('main.login')}}">{{trans('main.login')}}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}" title="{{trans('main.register')}}">
                                    {{trans('main.register')}}</a>
                            </li>
                        @endif
                    @else
                        <?php
                        $get_user_author_avatar_item = Auth::user()->get_user_avatar_item();
                        ?>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre
                               title="{{Auth::user()->name}}">
                                {{Auth::user()->name}}
                                @if($get_user_author_avatar_item)
                                    @include('view.img',['item'=>$get_user_author_avatar_item, 'size'=>"avatar", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>Auth::user()->name])
                                @endif
                                <span class="caret"></span>
                            </a>
                            @if(Auth::user()->isAdmin())
                                <span class="badge badge-related">{{trans('main.admin')}}</span>
                            @endif

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                                   title="{{trans('main.logout')}}">
                                    {{trans('main.logout')}}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                                @auth
                                    <a class="dropdown-item" href="{{route('project.index_user', Auth::user())}}"
                                       title="{{trans('main.setup_projects')}}">
                                        {{trans('main.setup_projects')}}
                                    </a>
                                    <?php
                                    // Ссылка на проект Настройки пользователя
                                    $usersetup_link = env('USERSETUP_LINK');
                                    $mail_link = env('MAIL_LINK');
                                    ?>
                                    @if($usersetup_link !='')
                                        <a class="dropdown-item" href="{{$usersetup_link}}"
                                           title="{{trans('main.personal_account_of_the_user')}}">
                                            {{trans('main.personal_account_of_the_user')}}
                                        </a>
                                    @endif
                                    @if($mail_link !='')
                                        <a class="dropdown-item" href="{{$mail_link}}"
                                           title="{{trans('main.mail')}}">
                                            {{trans('main.mail')}}
                                        </a>
                                    @endif
                                    {{--                                    <a class="dropdown-item" href="#">--}}
                                    {{--                                        {{trans('main.all_projects')}}--}}
                                    {{--                                    </a>--}}
                                    @if(Auth::user()->isModerator())
                                        <a class="dropdown-item" href="{{route('moderation.index')}}"
                                           title="{{trans('main.moderation')}}">
                                            {{trans('main.moderation')}}(<span
                                                class="badge badge-related">{{trans('main.moderator')}}</span>)
                                        </a>
                                    @endif
                                    @if(Auth::user()->isAdmin())
                                        <a class="dropdown-item" href="{{route('template.index')}}"
                                           title="{{trans('main.configuring_templates')}}">
                                            {{trans('main.configuring_templates')}}(<span
                                                class="badge badge-related">{{trans('main.admin')}}</span>)
                                        </a>
                                        <a class="dropdown-item" href="{{route('user.index')}}"
                                           title="{{trans('main.users')}}">
                                            {{trans('main.users')}}(<span
                                                class="badge badge-related">{{trans('main.admin')}}</span>)
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    {{--Одинаковый процент 0.75 layouts\app.php и view\img.php--}}
    <main class="py-4 w-75 mw-75 mx-auto">
        {{--                <div class="mx-auto" style="width: 1200px;">--}}
        @yield('content')
        {{--                </div>--}}
        {{--        <hr>--}}
        {{--        <center>--}}
        {{--            <?php--}}
        {{--            $nom_pict = mt_rand(1, 7);--}}
        {{--            $img_name = "";--}}
        {{--            switch ($nom_pict) {--}}
        {{--                case 1:--}}
        {{--                    $img_name = "new_year1.jpg";--}}
        {{--                    break;--}}
        {{--                case 2:--}}
        {{--                    $img_name = "new_year2.jpg";--}}
        {{--                    break;--}}
        {{--                case 3:--}}
        {{--                    $img_name = "new_year3.png";--}}
        {{--                    break;--}}
        {{--            }--}}
        {{--            ?>--}}
        {{--            @if($img_name != "")--}}
        {{--                <img src="{{Storage::url($img_name)}} " width="75%">--}}
        {{--            @endif--}}
        {{--        </center>--}}
        @guest
            <hr>
            {{--            Похожие строки layouts\app.blade.php и message.blade.php--}}
            {{--            <div class="alert alert-danger alert-dismissible fade show" role="alert">--}}
            <div class="alert alert-dismissible fade show" role="alert">
                <p>
                <h5 class="display-5 text-danger text-center">
                    {{trans('main.please_follow')}}
                    <a href="{{ route('login') }}"
                       title="{{trans('main.login')}}">{{trans('main.login')}}</a>
                    {{trans('main.or')}}
                    <a href="{{ route('register') }}" title="{{trans('main.register')}}">
                        {{trans('main.register')}}</a>
                </h5>
                </p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endguest
    </main>
</div>
<!-- Ajax -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
        crossorigin="anonymous"></script>

</body>
</html>
